<?php

namespace Hui\Xproject\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait ListTrait{
  protected function makeListResponse(Request $request,callable $orderCallback,callable $rowsCallback,callable $itemsCallback):array{
    $page=$request->input('page');
    $size=$request->input('size');
    $order=$request->input('order','');
    $ascOrder=(bool)$request->input('ascOrder');

    if($page<1)
      throw new HttpException(400,'Wrong page');
    if($size<1)
      throw new HttpException(400,'Wrong size');

    $orderColumn=$orderCallback($order);
    if($orderColumn===null)
      throw new HttpException(400,'Wrong order');

    $filters=$request->input('filters',[]);

    $rows=$rowsCallback($orderColumn,$ascOrder,(int)$page,(int)$size,$filters);

    $items=$itemsCallback($filters);
    $pages=(int)ceil($items/$size);

    return compact(
      'rows',
      'items',
      'pages'
    );
  }

  protected function splitMultiRowCells(array $rows,array $multiRowColumns):array{
    $commonColumns=[];
    if($rows)
      foreach(array_values($rows)[0] as $column=>$value)
        if(!in_array($column,$multiRowColumns)&&$column!=='meta')
          $commonColumns[]=$column;

    $newRows=[];
    foreach($rows as $row){
      $newRow=[
        'meta'=>$row['meta']
      ];
      $extraRows=[];

      foreach($row as $column=>$value)
        if(!in_array($column,$multiRowColumns)&&$column!=='meta')
          $newRow[$column]=$value;

      $count=null;
      foreach($multiRowColumns as $column){
        if(isset($row[$column]))
          $items=array_values((array)$row[$column]);
        else
          $items=[];

        // Initialize $count and $extraRows for this $row
        if($count===null){
          $count=count($items);

          for($i=1;$i<$count;$i++)
            $extraRows[]=[
              'meta'=>[
                'id'=>sprintf('%s_%u',$row['meta']['id'],$i),
                'skipColumns'=>$commonColumns
              ]
            ];
        }

        if($items){
          [$firstItem]=array_splice($items,0,1);

          $newRow[$column]=$firstItem;

          // Populate $extraRows for this $column
          foreach(array_values($items) as $index=>$item)
            $extraRows[$index][$column]=$item;
        }
      }

      $newRow['meta']+=[
        'multiRowsCount'=>$count,
        'rowspanColumns'=>$commonColumns
      ];

      $newRows[]=$newRow;
      $newRows=array_merge($newRows,$extraRows);
    }

    return $newRows;
  }

  protected function orderByRaw(string $orderColumns,bool $ascOrder):string{
    $orderRaw=array_map(function(string $column) use ($ascOrder):string{
      if(ends_with($column,[
        ' ASC',
        ' DESC'
      ]))
        return $column;
      else
        return $ascOrder?$column:sprintf('%s DESC',$column);
    },explode(',',$orderColumns));

    return implode(',',$orderRaw);
  }
}
