<?php

namespace Hui\Xproject\Http\Helpers\ExcelExport;

use PHPExcel_Cell;
use PHPExcel_Worksheet;

abstract class ExcelExport{
  protected function writeHeader(PHPExcel_Worksheet $sheet,array $cells):void{
    $this->row=1;

    $this->writeRow($sheet,$cells);
    $range=sprintf('%s1:%s1',PHPExcel_Cell::stringFromColumnIndex(0),PHPExcel_Cell::stringFromColumnIndex(count($cells)-1));
    $sheet->getStyle($range)
      ->getFont()
      ->setBold(true);
  }

  protected function writeRow(PHPExcel_Worksheet $sheet,array $cells):void{
    $sheet->fromArray($cells,null,sprintf('%s%u',PHPExcel_Cell::stringFromColumnIndex(0),$this->row));

    // Enable text wrap on columns with line breaks
    foreach($cells as $column=>$cell)
      if(strpos($cell,"\n")!==false)
        $sheet->getStyleByColumnAndRow($column,$this->row)
          ->getAlignment()
          ->setWrapText(true);

    $this->row++;
  }

  protected function autoAdjustCellWidths(PHPExcel_Worksheet $sheet):void{
    $lastColumn=PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());
    for($column=0;$column<$lastColumn;$column++)
      $sheet->getColumnDimensionByColumn($column)
        ->setAutoSize(true);
  }

  protected function resetSelectedCell(PHPExcel_Worksheet $sheet):void{
    $sheet->setSelectedCells('A1');
  }

  protected $row=1;
}
