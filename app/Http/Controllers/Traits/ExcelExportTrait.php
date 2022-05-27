<?php

namespace Hui\Xproject\Http\Controllers\Traits;

use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_Settings;
use PHPExcel_Style_Alignment;
use PHPExcel_Writer_Excel2007;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @method string tempPath(string $base)
 */
trait ExcelExportTrait{
  protected function excelFilters(Request $request):array{
    $filters=$request->input('filters');
    $filters=@json_decode($filters,true);
    if($filters===null)
      throw new HttpException(400,'Wrong filters parameter');

    return $filters;
  }

  protected function separateSheets(Request $request):bool{
    return $request->input('separate')!=='true';
  }

  protected function createExcel():PHPExcel{
    if(config('hui.excel_use_pclzip'))
      PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);

    return new PHPExcel;
  }

  protected function sendExcel(PHPExcel $excel,string $baseName):BinaryFileResponse{
    $excel->removeSheetByIndex(0);
    $excel->setActiveSheetIndex(0);

    $excel->getDefaultStyle()
      ->getAlignment()
      ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $path=$this->tempPath(sprintf('%s-excel',$baseName));
    $writer=new PHPExcel_Writer_Excel2007($excel);
    $writer->save($path);

    $filename=sprintf('%s-%s.xlsx',
      $baseName,
      now()->format('Ymd-His'));

    return response()->download($path,$filename);
  }
}
