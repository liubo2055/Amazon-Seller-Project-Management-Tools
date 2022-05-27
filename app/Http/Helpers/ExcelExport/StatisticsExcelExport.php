<?php

namespace Hui\Xproject\Http\Helpers\ExcelExport;

use Hui\Xproject\Entities\Synthetic\Statistic;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Illuminate\Database\Eloquent\Collection;
use PHPExcel;
use PHPExcel_Worksheet;

class StatisticsExcelExport extends ExcelExport{
  /**
   * @param PHPExcel_Worksheet     $sheet
   * @param Collection|Statistic[] $statistics
   */
  public function exportStatistics(PHPExcel_Worksheet $sheet,Collection $statistics):void{
    $this->exportStatisticsIntoSheet($sheet,_ix('Statistics','Statistics'),$statistics->all());
  }

  /**
   * @param PHPExcel               $excel
   * @param Collection|Statistic[] $statistics
   */
  public function exportStatisticsSeparateSheets(PHPExcel $excel,Collection $statistics):void{
    $statisticsByFreelancer=[];
    foreach($statistics as $statistic){
      $statisticsByFreelancer[$statistic->freelancer]=$statisticsByFreelancer[$statistic->freelancer]??[];
      $statisticsByFreelancer[$statistic->freelancer][]=$statistic;
    }

    ksort($statisticsByFreelancer);

    foreach($statisticsByFreelancer as $freelancer=>$statisticsForFreelancer){
      $sheetName=mb_substr($freelancer,0,31);
      if($sheetName==='')
        $sheetName=_ix('(Other)','Statistics');
      $this->exportStatisticsIntoSheet($excel->createSheet(),$sheetName,$statisticsForFreelancer);
    }
  }

  /**
   * @param PHPExcel_Worksheet $sheet
   * @param string             $title
   * @param Statistic[]        $statistics
   */
  private function exportStatisticsIntoSheet(PHPExcel_Worksheet $sheet,string $title,array $statistics):void{
    $sheet->setTitle($title);
    $this->writeHeader($sheet,[
      _ix('To-Do ID','Statistics'),
      _ix('To-Do ASIN','Statistics'),
      _ix('To-Do marketplace','Statistics'),
      _ix('Seller name','Statistics'),
      _ix('Seller ID','Statistics'),
      _ix('Storefront name','Statistics'),
      _ix('Storefront URL','Statistics'),
      _ix('To-Do initial price','Statistics'),
      _ix('Freelancer\'s name','Statistics'),
      _ix('Project currency','Statistics'),
      _ix('Project price','Statistics'),
      _ix('Project status','Statistics'),
      _ix('Project completion ID','Statistics'),
      _ix('Project completion URL','Statistics'),
      _ix('Project created date','Statistics'),
      _ix('Completion ID submit date','Statistics'),
      _ix('Project completion date','Statistics'),
      _ix('Keywords','Statistics')
    ]);

    /**
     * @var MarketplaceInformation $marketplaceInformation
     */
    $marketplaceInformation=app(MarketplaceInformation::class);

    foreach($statistics as $statistic){
      $row=[
        $statistic->numberAndCode(),
        $statistic->product_asin,
        $marketplaceInformation->name($statistic->marketplace),
        $statistic->seller_name,
        $statistic->seller_id,
        $statistic->storefront_name,
        $statistic->storefront_url,
        $statistic->original_project_price,
        $statistic->freelancer,
        $marketplaceInformation->currency($statistic->marketplace),
        $statistic->project_price,
        $statistic->statusName(),
        $statistic->completion_id,
        $statistic->completion_url,
        $statistic->created_at->format('Y-m-d H:i:s'),
        $statistic->completion_id_submitted_at?$statistic->completion_id_submitted_at->format('Y-m-d H:i:s'):null,
        $statistic->completed_at?$statistic->completed_at->format('Y-m-d H:i:s'):null,
        implode("\n",$statistic->keywords??[])
      ];

      $this->writeRow($sheet,$row);
    }

    $this->autoAdjustCellWidths($sheet);
    $this->resetSelectedCell($sheet);
  }
}
