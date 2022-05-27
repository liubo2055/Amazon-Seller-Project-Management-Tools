<?php

namespace Hui\Xproject\Http\ViewComposers;

use Illuminate\View\View;

class LogsComposer{
  public function compose(View $view):void{
    $logs=$view->offsetGet('logs');
    $options=[];
    foreach($logs as $log)
      $options[$log['name']]=sprintf('%s: %s, %u kB',
        $log['name'],
        $log['date'],
        $log['size']/1000);

    if($logs)
      $selectedLog=array_values($logs)[0]['name'];
    else
      $selectedLog=null;

    $loadUrl=route('logsView');
    $downloadUrl=route('logsDownload');
    $filters=[
      [
        'title'=>_ix('File','Log Viewer'),
        'name'=>'file',
        'options'=>$options,
        'value'=>$selectedLog
      ],
      [
        'title'=>_ix('View','Log Viewer'),
        'name'=>'view',
        'options'=>[
          'end'=>_ix('Last 20000 bytes','Log Viewer'),
          'errors'=>_ix('Errors only','Log Viewer'),
          'info'=>_ix('Info only','Log Viewer'),
          'all'=>_ix('All','Log Viewer'),
          'search'=>_ix('Search...','Log Viewer')
        ],
        'value'=>'end'
      ],
      [
        'title'=>_ix('Search text','Log Viewer'),
        'name'=>'search'
      ]
    ];
    $filtersWithoutSearch=$filters;
    unset($filtersWithoutSearch[2]);

    $view->with(compact(
      'loadUrl',
      'downloadUrl',
      'filters',
      'filtersWithoutSearch'
    ));
  }
}
