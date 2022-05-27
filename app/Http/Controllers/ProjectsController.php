<?php

namespace Hui\Xproject\Http\Controllers;

use Hui\Xproject\Entities\Image;
use Hui\Xproject\Entities\Media;
use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Http\Controllers\Traits\CloudUploadTrait;
use Hui\Xproject\Http\Controllers\Traits\ListTrait;
use Hui\Xproject\Http\Controllers\Traits\UserInfoTrait;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Hui\Xproject\Services\ProjectManager\ProjectManager;
use Hui\Xproject\Services\TodoManager\TodoManager;
use Hui\Xproject\Services\UploadManager\UploadManager;
use Hui\Xproject\Services\ZipArchiver\InlineFile;
use Hui\Xproject\Services\ZipArchiver\ZipArchive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProjectsController extends Controller{
  use ListTrait,
    CloudUploadTrait,
    UserInfoTrait;

  public function __construct(ProjectManager $projectManager,UploadManager $uploadManager,MarketplaceInformation $marketplaceInformation,TodoManager $todoManager){
    $this->projectManager=$projectManager;
    $this->uploadManager=$uploadManager;
    $this->marketplaceInformation=$marketplaceInformation;
    $this->todoManager=$todoManager;
  }

  public function index(?Todo $todo):View{
    return view('projects',[
      'todoId'=>$todo->id
    ]);
  }

  public function list(Request $request):array{
    $orderCallback=function(string $order):string{
      switch($order){
        case 'rowAsinSeller':
          return 'original_todo_code,number';
        case 'freelancer':
          return 'user_id';
        case 'todoTitleDescription':
          return 'product_description';
        case 'projectTitleDescription':
          return 'project_description';
        case 'dates':
          return 'created_at,original_todo_code ASC,number ASC';
        default:
          return snake_case($order);
      }
    };

    $rowsCallback=function(string $orderColumn,bool $ascOrder,int $page,int $size,array $filters):array{
      $user=user();

      $projects=Project
        ::orderByRaw($this->orderByRaw($orderColumn,$ascOrder))
        ->offset(($page-1)*$size)
        ->limit($size)
        ->with([
          'todo',
          'todo.user',
          'user',
          'images',
          'medias'
        ]);
      $this->projectManager->restrictQueryForUser($projects,$user);
      $this->filter($projects,$filters);
      $projects=$projects->get();

      $todosWithLimitReached=[];

      $accountsConfig=config('accounts');
      $freelancerUser=$user->isFreelancer();
      $managerUser=$user->isManagerOrAdmin();
      $limit=config('limits.accepted_projects_without_completion_id');

      $rows=[];
      /**
       * @var Project $project
       */
      foreach($projects as $project){
        $todo=$project->todo;

        if($user->can('accept',$project)&&!isset($todosWithLimitReached[$project->todo_id]))
          $todosWithLimitReached[$project->todo_id]=$this->todoManager->limitReachedForTodo($todo);

        $productUrl=$todo->product_url;
        if($freelancerUser&&isset($accountsConfig[$todo->marketplace])){
          if($todo->keywords)
            $keyword=array_random($todo->keywords);
          else
            $keyword='';
          $productUrl=$this->marketplaceInformation->addTag($productUrl,$accountsConfig[$todo->marketplace]['subscriptionId'],
            $accountsConfig[$todo->marketplace]['tag'],$keyword);
        }

        $rowAsinSeller=[
          'row'=>$project->numberAndCode(),
          'asin'=>$todo->product_asin,
          'url'=>$productUrl,
          'marketplace'=>$this->marketplaceInformation->name($todo->marketplace),
          'storefrontName'=>$todo->storefront_name,
          'storefrontUrl'=>$todo->storefront_url,
          'sellerId'=>$todo->seller_id,
          'private'=>$todo->private
        ];
        if(!$freelancerUser)
          $rowAsinSeller['sellerName']=$todo->seller_name;
        if($managerUser)
          $rowAsinSeller['sellerPhone']=$todo->user->isEmployer()?$todo->user->phone:null;

        $imageMedia=[
          'downloadUrl'=>null,
          'images'=>$project->images->count(),
          'medias'=>$project->medias->count()
        ];
        if($project->images->isNotEmpty()||$project->medias->isNotEmpty())
          $imageMedia['downloadUrl']=route('projectsDownload',$project->id);

        $status=[
          'status'=>$project->statusName()
        ];
        if($freelancerUser){
          if($project->statusWithDeleted()===Project::STATUS_ACCEPTED)
            $status['idDeadline']=$project->accepted_at->addHour($limit)
              ->format('H:i');

          if($project->statusWithDeleted()===Project::STATUS_UNASSIGNED){
            $similar=array_map(function(array $attr) use ($project,$user):int{
              $count=$this->projectManager->similarProjectsQuery($project,$user,$attr['minDate'],$attr['attr']);
              $this->projectManager->restrictQueryForUser($count,user());

              return $count->count();
            },$this->similarProjectsAttrs());

            // Exclude similar labels without any similar projects for all three attributes
            if(array_sum($similar))
              $status+=[
                'similar'=>$similar,
                'similarUrl'=>route('projectsSimilarProjects',$project->id)
              ];
          }
        }

        $rows[]=[
          'rowAsinSeller'=>$rowAsinSeller,
          'todoTitleDescription'=>[
            'productTitle'=>$project->product_title,
            'productDescription'=>$project->product_description
          ],
          'projectTitleDescription'=>[
            'projectTitle'=>$project->project_title,
            'projectDescription'=>$project->project_description,
            'storeDescription'=>$project->store_description
          ],
          'projectPrice'=>sprintf('%s %s',
            $this->marketplaceInformation->currency($todo->marketplace),
            number_format($project->project_price,2)),
          'imageMedia'=>$imageMedia,
          'status'=>$status,
          'completion'=>[
            'id'=>$project->completion_id,
            'url'=>$project->completion_url,
            // Employers can only see the freelancer for private projects
            'freelancer'=>($managerUser||$todo->private)&&$project->user?$this->userInfo($project->user):null
          ],
          'dates'=>[
            'created'=>$project->created_at->format('Y-m-d H:i:s'),
            'completionIdSubmitted'=>$project->completion_id_submitted_at?$project->completion_id_submitted_at->format('Y-m-d H:i:s'):null,
            'completed'=>$project->completed_at?$project->completed_at->format('Y-m-d H:i:s'):null
          ],
          'operations'=>$this->operations($project,$todosWithLimitReached),
          'meta'=>[
            'id'=>$project->id,
            'waitingForDescription'=>$project->statusWithDeleted()===Project::STATUS_WAITING_DESCRIPTION
          ]
        ];
      }

      return $rows;
    };

    $itemsCallback=function(array $filters):int{
      $count=$this->projectManager->restrictQueryForUser(Project::query(),user());
      $this->filter($count,$filters);

      return $count->count();
    };

    return $this->makeListResponse($request,$orderCallback,$rowsCallback,$itemsCallback);
  }

  public function view(Project $project):array{
    return [
      'project'=>[
        'row'=>$project->numberAndCode(),
        'productTitle'=>$project->product_title,
        'productDescription'=>$project->product_description,
        'projectTitle'=>$project->project_title,
        'projectDescription'=>$project->project_description,
        'storeDescription'=>$project->store_description,
        'projectPrice'=>$project->project_price,
        'projectCurrency'=>$this->marketplaceInformation->currency($project->todo->marketplace),
        'status'=>$project->statusName(),
        'storefrontUrl'=>$project->todo->storefront_url,
        'storefrontName'=>$project->todo->storefront_name,
        'creation'=>$project->created_at->format('Y-m-d H:i:s'),
        'completionIdSubmission'=>$project->completion_id_submitted_at?$project->completion_id_submitted_at->format('Y-m-d H:i:s'):null,
        'completion'=>$project->completed_at?$project->completed_at->format('Y-m-d H:i:s'):null
      ],
      'images'=>$project->images->pluck('url'),
      'medias'=>$project->medias->pluck('content')
    ];
  }

  public function download(Project $project):BinaryFileResponse{
    $zip=new ZipArchive;

    foreach($project->images as $image)
      $zip->addFile($image->path);

    $index=1;
    foreach($project->medias as $media){
      $file=new InlineFile;
      $file->name=sprintf('media_%u.txt',$index++);
      $file->content=$media->content;

      $zip->addInlineFile($file);
    }

    $path=$this->tempPath('images-medias-zip');
    $filename=sprintf('%s.zip',$project->numberAndCode());

    $zip->archive($path);

    return response()->download($path,$filename);
  }

  public function accept(Project $project):void{
    $this->projectManager->accept($project,user());
    $project->save();
  }

  public function submitId(Project $project,Request $request):void{
    $this->validate($request,[
      'completionId'=>'required|max:20'
    ]);

    $this->projectManager->submitId($project,$request->input('completionId'));
    $project->save();
  }

  public function submitUrl(Project $project,Request $request):void{
    $this->validate($request,[
      'completionUrl'=>'required|url|max:255'
    ]);

    $this->projectManager->submitUrl($project,$request->input('completionUrl'));
    $project->save();
  }

  public function loadTodoAttrs(Project $project):array{
    return [
      'project'=>[
        'productTitle'=>$project->product_title,
        'productDescription'=>$project->product_description,
        'projectPrice'=>$project->project_price,
        'notes'=>$project->notes
      ],
      'saveUrl'=>route('projectsSaveTodoAttrs',$project->id)
    ];
  }

  public function saveTodoAttrs(Project $project,Request $request):void{
    $this->validate($request,[
      'productTitle'=>'nullable|max:255',
      'productDescription'=>'nullable|max:255',
      'projectPrice'=>'required|numeric|min:0.01'
    ]);

    $project->product_title=$request->input('productTitle');
    $project->product_description=$request->input('productDescription');
    $project->project_price=$request->input('projectPrice');
    $project->notes=$request->input('notes');

    $project->save();
  }

  public function loadProjectAttrs(Project $project):array{
    $showStoreDescription=user()->can('editStoreDescription',$project);

    $projectData=[
      'projectTitle'=>$project->project_title,
      'projectDescription'=>$project->project_description
    ];
    if($showStoreDescription)
      $projectData['storeDescription']=$project->store_description;

    return [
      'project'=>$projectData,
      'images'=>$project->images->map(function(Image $image):array{
        return [
          'id'=>$image->id,
          'url'=>$image->url
        ];
      }),
      'medias'=>$project->medias->map(function(Media $media):array{
        return [
          'id'=>$media->id,
          'content'=>$media->content
        ];
      }),
      'saveUrl'=>route('projectsSaveProjectAttrs',$project->id),
      'showStoreDescription'=>$showStoreDescription
    ];
  }

  public function saveProjectAttrs(Project $project,Request $request):void{
    $data=$request->input('project');

    $saveStoreDescription=user()->can('editStoreDescription',$project);

    $rules=[
      'projectTitle'=>'nullable|max:255',
      'projectDescription'=>'nullable|max:5000'
    ];
    if($saveStoreDescription)
      $rules['storeDescription']='nullable|max:255';

    validator($data,$rules)->validate();

    $project->project_title=$data['projectTitle'];
    $project->project_description=$data['projectDescription'];
    if($saveStoreDescription)
      $project->store_description=$data['storeDescription'];

    $this->projectManager->changeStatusBasedOnDescription($project);

    $uploadCodes=$this->saveImages($project,$request->input('images'));
    $this->saveMedias($project,$request->input('medias'));

    DB::beginTransaction();

    $exists=$project->exists;

    $project->save();
    $project->images()
      ->saveMany($project->images);
    $project->medias()
      ->saveMany($project->medias);

    if($exists)
      $this->deleteRelationships($project);

    DB::commit();

    $this->deleteTempUploads($uploadCodes);
  }

  public function fail(Project $project):void{
    $this->projectManager->fail($project);
    $project->save();
  }

  public function cancel(Project $project):void{
    $this->projectManager->cancel($project);
    $project->save();
  }

  public function complete(Project $project):void{
    $this->projectManager->complete($project);
    $project->save();
  }

  public function delete(Project $project):void{
    $project->delete();
  }

  public function restore(Project $project):void{
    $project->restore();
  }

  public function similarProjects(Project $project):array{
    if($project->statusWithDeleted()!==Project::STATUS_UNASSIGNED)
      throw new HttpException(400,'Wrong project status');

    $todo=$project->todo;

    return [
      [
        'name'=>_ix('Store name','Projects'),
        'value'=>$todo->storefront_name,
        'url'=>route('projectsSimilarProjectsList',[
          $project->id,
          'storefrontName'
        ])
      ],
      [
        'name'=>_ix('Seller ID','Projects'),
        'value'=>$todo->seller_id,
        'url'=>route('projectsSimilarProjectsList',[
          $project->id,
          'sellerId'
        ])
      ],
      [
        'name'=>_ix('ASIN','Projects'),
        'value'=>$todo->product_asin,
        'url'=>route('projectsSimilarProjectsList',[
          $project->id,
          'asin'
        ])
      ]
    ];
  }

  public function similarProjectsList(Project $project,string $attr,Request $request):array{
    if($project->statusWithDeleted()!==Project::STATUS_UNASSIGNED)
      throw new HttpException(400,'Wrong project status');

    $attrMinDate=$this->similarProjectsAttrs()[$attr]??null;
    if(!$attrMinDate)
      throw new HttpException(400,'Wrong attribute');
    $minDate=$attrMinDate['minDate'];
    $todoAttr=$attrMinDate['attr'];

    $orderCallback=function(string $order):string{
      switch($order){
        case 'rowAsinSeller':
          return 'original_todo_code,number';
        case 'freelancer':
          return 'user_id';
        case 'todoTitleDescription':
          return 'product_description';
        case 'projectTitleDescription':
          return 'project_description';
        case 'dates':
          return 'created_at,original_todo_code ASC,number ASC';
        default:
          return snake_case($order);
      }
    };

    $rowsCallback=function(string $orderColumn,bool $ascOrder,int $page,int $size,array $filters) use ($project,$minDate,$todoAttr):array{
      $user=user();

      $projects=$this->projectManager->similarProjectsQuery($project,$user,$minDate,$todoAttr)
        ->orderByRaw($this->orderByRaw($orderColumn,$ascOrder))
        ->offset(($page-1)*$size)
        ->limit($size)
        ->with([
          'todo',
          'user'
        ]);
      $this->projectManager->restrictQueryForUser($projects,$user);
      $this->filter($projects,$filters);
      $projects=$projects->get();

      $todosWithLimitReached=[];

      $accountsConfig=config('accounts');
      $limit=config('limits.accepted_projects_without_completion_id');

      $rows=[];
      /**
       * @var Project $project
       */
      foreach($projects as $project){
        $todo=$project->todo;

        if($user->can('accept',$project)&&!isset($todosWithLimitReached[$project->todo_id]))
          $todosWithLimitReached[$project->todo_id]=$this->todoManager->limitReachedForTodo($todo);

        $productUrl=$todo->product_url;
        if(isset($accountsConfig[$todo->marketplace])){
          if($todo->keywords)
            $keyword=array_random($todo->keywords);
          else
            $keyword='';
          $productUrl=$this->marketplaceInformation->addTag($productUrl,$accountsConfig[$todo->marketplace]['subscriptionId'],
            $accountsConfig[$todo->marketplace]['tag'],$keyword);
        }

        $rowAsinSeller=[
          'row'=>$project->numberAndCode(),
          'asin'=>$todo->product_asin,
          'url'=>$productUrl,
          'marketplace'=>$this->marketplaceInformation->name($todo->marketplace),
          'storefrontName'=>$todo->storefront_name,
          'storefrontUrl'=>$todo->storefront_url,
          'sellerId'=>$todo->seller_id
        ];

        $status=[
          'status'=>$project->statusName()
        ];
        if($project->statusWithDeleted()===Project::STATUS_ACCEPTED)
          $status['idDeadline']=$project->accepted_at->addHour($limit)
            ->format('H:i');

        $rows[]=[
          'rowAsinSeller'=>$rowAsinSeller,
          'todoTitleDescription'=>[
            'productTitle'=>$project->product_title,
            'productDescription'=>$project->product_description
          ],
          'projectPrice'=>sprintf('%s %s',
            $this->marketplaceInformation->currency($todo->marketplace),
            number_format($project->project_price,2)),
          'status'=>$status,
          'completion'=>[
            'id'=>$project->completion_id,
            'url'=>$project->completion_url
          ],
          'dates'=>[
            'created'=>$project->created_at->format('Y-m-d H:i:s'),
            'completionIdSubmitted'=>$project->completion_id_submitted_at?$project->completion_id_submitted_at->format('Y-m-d H:i:s'):null,
            'completed'=>$project->completed_at?$project->completed_at->format('Y-m-d H:i:s'):null
          ],
          'meta'=>[
            'id'=>$project->id,
            'waitingForDescription'=>$project->statusWithDeleted()===Project::STATUS_WAITING_DESCRIPTION
          ]
        ];
      }

      return $rows;
    };

    $itemsCallback=function(array $filters) use ($project,$minDate,$todoAttr):int{
      $count=$this->projectManager->similarProjectsQuery($project,user(),$minDate,$todoAttr);
      $this->projectManager->restrictQueryForUser($count,user());
      $this->filter($count,$filters);

      return $count->count();
    };

    return $this->makeListResponse($request,$orderCallback,$rowsCallback,$itemsCallback);
  }

  private function filter(Builder $query,array $filters):void{
    foreach($filters as $filter=>$value)
      if($value!==null)
        switch($filter){
          case 'todo':
            if($value)
              $query->where('todo_id',$value);
            break;
          case 'description':
            if($value==='yes')
              $query->whereRaw('COALESCE(project_description,\'\')!=\'\'');
            else if($value==='no')
              $query->whereRaw('COALESCE(project_description,\'\')=\'\'');
            break;
          case 'status':
            $this->projectManager->filterByStatuses($query,$value);
            break;
          case 'asin':
            $this->projectManager->filterByTodoAttrs($query,['product_asin'],$value);
            break;
          case 'sellerIdStorefrontName':
            $this->projectManager->filterByTodoAttrs($query,[
              'seller_id',
              'storefront_name'
            ],$value);
            break;
          case 'sellerName':
            if(!user()->isFreelancer())
              $this->projectManager->filterByTodoAttrs($query,['seller_name'],$value);
            else
              throw new HttpException(400,'Wrong filter');
            break;
          case 'freelancer':
            if(user()->isManagerOrAdmin())
              $this->projectManager->filterByFreelancerName($query,$value);
            else
              throw new HttpException(400,'Wrong filter');
            break;
          case 'date':
            if($value[0])
              $query->where('created_at','>=',$value[0]);
            if($value[1])
              $query->where('created_at','<=',sprintf('%s 23:59:59',$value[1]));
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

  private function operations(Project $project,array $todosWithLimitReached):array{
    $user=user();

    $operations=[];
    if($user->can('view',$project))
      $operations['view']=[
        'url'=>route('projectsView',$project->id)
      ];
    if($user->can('accept',$project))
      $operations['accept']=[
        'url'=>route('projectsAccept',$project->id),
        'limitReached'=>$todosWithLimitReached[$project->todo_id]
      ];
    if($user->can('submitCompletionId',$project))
      $operations['submitId']=[
        'saveUrl'=>route('projectsSubmitId',$project->id)
      ];
    if($user->can('submitCompletionUrl',$project))
      $operations['submitUrl']=[
        'saveUrl'=>route('projectsSubmitUrl',$project->id)
      ];
    if($user->can('editTodoAttrs',$project))
      $operations['editTodoAttrs']=[
        'url'=>route('projectsLoadTodoAttrs',$project->id)
      ];
    if($user->can('editProjectAttrs',$project))
      $operations['editProjectAttrs']=[
        'url'=>route('projectsLoadProjectAttrs',$project->id)
      ];
    if($user->can('complete',$project))
      $operations['complete']=[
        'url'=>route('projectsComplete',$project->id)
      ];
    if($user->can('fail',$project))
      $operations['fail']=[
        'url'=>route('projectsFail',$project->id)
      ];
    if($user->can('cancel',$project))
      $operations['cancel']=[
        'url'=>route('projectsCancel',$project->id)
      ];
    if($user->can('delete',$project))
      $operations['delete']=[
        'url'=>route('projectsDelete',$project->id)
      ];
    if($user->can('restore',$project))
      $operations['restore']=[
        'url'=>route('projectsRestore',$project->id)
      ];

    return $operations;
  }

  private function saveImages(Project $project,array $imagesData):array{
    $notDeleted=array_column($imagesData,'id');
    $notDeleted=array_filter($notDeleted,function(int $id):bool{
      return $id>0;
    });
    $project->setRelation('images',$project->images->filter(function(Image $image) use ($notDeleted):bool{
      return in_array($image->id,$notDeleted);
    }));

    $uploadCodes=[];

    foreach($imagesData as $imageData){
      // An empty image component
      if(empty($imageData['url'])&&empty($imageData['code']))
        continue;

      $id=$imageData['id'];
      if($id>0){
        /**
         * @var Image $image
         */
        $image=$project->images->whereStrict('id',$id)
          ->first();

        if(!$image)
          throw new HttpException(400,'The image does not belong to the project');
      }
      else{
        $image=new Image;

        $project->images->push($image);
      }

      if(!empty($imageData['code'])){
        $code=$imageData['code'];

        [
          $image->path,
          $image->url
        ]=$this->uploadFile($code,user(),true);
        $uploadCodes[]=$code;
      }
    }

    return $uploadCodes;
  }

  private function saveMedias(Project $project,array $mediasData):void{
    $notDeleted=array_column($mediasData,'id');
    $notDeleted=array_filter($notDeleted,function(int $id):bool{
      return $id>0;
    });
    $project->setRelation('medias',$project->medias->filter(function(Media $media) use ($notDeleted):bool{
      return in_array($media->id,$notDeleted);
    }));

    foreach($mediasData as $mediaData){
      // An empty media component
      if(empty($mediaData['content']))
        continue;

      $id=$mediaData['id'];
      if($id>0){
        /**
         * @var Media $media
         */
        $media=$project->medias->whereStrict('id',$id)
          ->first();

        if(!$media)
          throw new HttpException(400,'The media does not belong to the project');
      }
      else{
        $media=new Media;

        $project->medias->push($media);
      }

      $media->content=$mediaData['content'];
    }
  }

  private function deleteRelationships(Project $project):void{
    Image
      ::whereNotIn('id',$project->images->modelKeys())
      ->where('project_id',$project->id)
      ->delete();

    Media
      ::whereNotIn('id',$project->medias->modelKeys())
      ->where('project_id',$project->id)
      ->delete();
  }

  private function similarProjectsAttrs():array{
    $minDate=today()->subMonths(6);

    return [
      'storefrontName'=>[
        'attr'=>'storefront_name',
        'minDate'=>$minDate
      ],
      'sellerId'=>[
        'attr'=>'seller_id',
        'minDate'=>$minDate
      ],
      'asin'=>[
        'attr'=>'product_asin',
        'minDate'=>null
      ]
    ];
  }

  /**
   * @var ProjectManager
   */
  private $projectManager;

  /**
   * @var MarketplaceInformation
   */
  private $marketplaceInformation;

  /**
   * @var TodoManager
   */
  private $todoManager;
}
