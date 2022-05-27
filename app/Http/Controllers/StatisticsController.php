<?php

namespace Hui\Xproject\Http\Controllers;

use Hui\Xproject\Entities\Synthetic\Statistic;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Http\Controllers\Traits\ExcelExportTrait;
use Hui\Xproject\Http\Controllers\Traits\ListTrait;
use Hui\Xproject\Http\Controllers\Traits\UserInfoTrait;
use Hui\Xproject\Http\Helpers\ExcelExport\StatisticsExcelExport;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Hui\Xproject\Services\ProjectManager\ProjectManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StatisticsController extends Controller{
  use ListTrait,
    ExcelExportTrait,
    UserInfoTrait;

  public function __construct(ProjectManager $projectManager,MarketplaceInformation $marketplaceInformation){
    $this->projectManager=$projectManager;
    $this->marketplaceInformation=$marketplaceInformation;
  }

  public function index():View{
    return view('statistics');
  }

  public function list(Request $request):array{
    $orderCallback=function(string $order):string{
      switch($order){
        case 'row':
          return 'code,number';
        case 'seller':
          return 'seller_name';
        case 'storefront':
          return 'storefront_name';
        case 'asinMarketplace':
          return 'product_asin';
        case 'price':
          return 'project_price';
        case 'dates':
          return 'created_at,original_todo_code ASC,number ASC';
        default:
          return snake_case($order);
      }
    };

    $rowsCallback=function(string $orderColumn,bool $ascOrder,int $page,int $size,array $filters):array{
      $statistics=Statistic
        ::orderByRaw($this->orderByRaw($orderColumn,$ascOrder))
        ->offset(($page-1)*$size)
        ->limit($size);
      $this->projectManager->restrictQueryForUser($statistics,user());
      $this->filter($statistics,$filters);
      $this->filterForEmployers($statistics,user());
      $statistics=$statistics->get();

      $rows=[];
      /**
       * @var Statistic $statistic
       */
      foreach($statistics as $statistic)
        $rows[]=[
          'row'=>[
            'row'=>$statistic->numberAndCode(),
            'private'=>$statistic->private
          ],
          'asinMarketplace'=>[
            'asin'=>$statistic->product_asin,
            'marketplace'=>$this->marketplaceInformation->name($statistic->marketplace),
            'url'=>$statistic->product_url
          ],
          'seller'=>[
            'id'=>$statistic->seller_id,
            'name'=>$statistic->seller_name
          ],
          'storefront'=>[
            'name'=>$statistic->storefront_name,
            'url'=>$statistic->storefront_url
          ],
          'freelancer'=>$this->userInfo($statistic->user),
          'currency'=>$this->marketplaceInformation->currency($statistic->marketplace),
          'price'=>number_format($statistic->project_price,2),
          'status'=>$statistic->statusName(),
          'completion'=>[
            'id'=>$statistic->completion_id,
            'url'=>$statistic->completion_url
          ],
          'dates'=>[
            'created'=>$statistic->created_at->format('Y-m-d H:i:s'),
            'completionIdSubmitted'=>$statistic->completion_id_submitted_at?$statistic->completion_id_submitted_at->format('Y-m-d H:i:s'):null,
            'completed'=>$statistic->completed_at?$statistic->completed_at->format('Y-m-d H:i:s'):null
          ],
          'meta'=>[
            'id'=>$statistic->id
          ]
        ];

      return $rows;
    };

    $itemsCallback=function(array $filters):int{
      $count=$this->projectManager->restrictQueryForUser(Statistic::query(),user());
      $this->filter($count,$filters);
      $this->filterForEmployers($count,user());

      return $count->count();
    };

    return $this->makeListResponse($request,$orderCallback,$rowsCallback,$itemsCallback);
  }

  public function download(Request $request,StatisticsExcelExport $statisticsExcelExport):BinaryFileResponse{
    $statistics=Statistic::orderByRaw($this->orderByRaw('code,number',true));
    $this->projectManager->restrictQueryForUser($statistics,user());
    $this->filter($statistics,$this->excelFilters($request));
    $this->filterForEmployers($statistics,user());
    $statistics=$statistics->get();

    $excel=$this->createExcel();

    if($this->separateSheets($request))
      $statisticsExcelExport->exportStatistics($excel->createSheet(),$statistics);
    else
      $statisticsExcelExport->exportStatisticsSeparateSheets($excel,$statistics);

    return $this->sendExcel($excel,'statistics');
  }

  private function filter(Builder $query,array $filters):void{
    switch($filters['dateType']){
      case 'created':
        $dateColumn='created_at';
        break;
      case 'completionIdSubmitted':
        $dateColumn='completion_id_submitted_at';
        break;
      case 'completed':
        $dateColumn='completed_at';
        break;
      default:
        throw new HttpException(400,'Wrong date type');
    }

    foreach($filters as $filter=>$value)
      switch($filter){
        case 'dateType':
          break;
        case 'date':
          if($value[0]||$value[1])
            $query->whereNotNull($dateColumn);
          if($value[0])
            $query->where($dateColumn,'>=',$value[0]);
          if($value[1])
            $query->where($dateColumn,'<=',sprintf('%s 23:59:59',$value[1]));
          break;
        case 'asinCode':
          $search=sprintf('%%%s%%',mb_strtolower($value));
          $query->where(function(Builder $query) use ($search):void{
            $query->where(DB::raw('lower(product_asin)'),'LIKE',$search)
              ->orWhere(DB::raw('lower(code)'),'LIKE',$search);
          });
          break;
        case 'sellerId':
          $search=sprintf('%%%s%%',mb_strtolower($value));
          $query->where(DB::raw('lower(seller_id)'),'LIKE',$search);
          break;
        case 'sellerName':
          $search=sprintf('%%%s%%',mb_strtolower($value));
          $query->where(DB::raw('lower(seller_name)'),'LIKE',$search);
          break;
        case 'freelancer':
          $search=sprintf('%%%s%%',mb_strtolower($value));
          $query->where('freelancer','LIKE',$search);
          break;
        case 'status':
          if(!user()->isManagerOrAdmin())
            $value=array_diff($value,[
              Statistic::STATUS_UNASSIGNED,
              Statistic::STATUS_DELETED
            ]);
          $this->projectManager->filterByStatuses($query,$value);
          break;
        case 'private':
          $query->whereIn('todo_id',Todo
            ::select('id')
            ->where('private',$value==='yes'));
          break;
        default:
          throw new HttpException(400,'Wrong filter');
      }
  }

  private function filterForEmployers(Builder $statistics,User $user):void{
    if($user->isEmployer())
      $statistics->where('private',true);
  }

  /**
   * @var ProjectManager
   */
  private $projectManager;

  /**
   * @var MarketplaceInformation
   */
  private $marketplaceInformation;
}
