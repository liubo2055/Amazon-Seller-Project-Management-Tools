<?php

namespace Hui\Xproject\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LogsController extends Controller{
  public function index():View{
    $logs=$this->logsList();

    return view('logs.index',compact('logs'));
  }

  public function view(Request $request):JsonResponse{
    $files=array_column($this->logsList(),'name');
    $views=[
      'end',
      'errors',
      'info',
      'all',
      'search'
    ];

    $file=$request->input('file');
    if(!in_array($file,$files))
      throw new HttpException(404,'File not found');

    $view=$request->input('view');
    if(!in_array($view,$views))
      throw new HttpException(400,'Wrong view');

    $path=storage_path(sprintf('logs/%s',$file));

    if($view!=='end')
      $contents=@file_get_contents($path);
    else{
      $size=filesize($path);

      if($size<=20000)
        $contents=@file_get_contents($path);
      else{
        $contents=@file_get_contents($path,null,null,$size-20000,20000);
        $nextLine=strpos($contents,"\n");
        if($nextLine)
          $contents=substr($contents,$nextLine);
      }
    }

    $search=$request->input('search');
    if($view==='search'&&(string)$search!=='')
      $searchRegexp=sprintf('|%s|i',preg_quote($search));
    else
      $searchRegexp=null;

    $lines=str_replace("\r",'',$contents);
    $lines=preg_replace('|\n(\[\d{4})|',"\r$1",$lines);
    $lines=explode("\r",$lines);
    $lines=array_map(function(string $line) use ($view,$searchRegexp):string{
      return $this->parseLogLine($line,$view,$searchRegexp);
    },$lines);
    $lines=array_filter($lines,'strlen');

    $html=view('logs.contents',compact('lines'))->render();

    return response()->json($html);
  }

  public function download(Request $request):BinaryFileResponse{
    $files=array_column($this->logsList(),'name');

    $file=$request->input('file');
    if(!in_array($file,$files))
      throw new HttpException(404,'File not found');

    $path=storage_path(sprintf('logs/%s',$file));

    return response()->download($path);
  }

  private function logsList():array{
    $logs=[];

    foreach(glob(storage_path('logs/*.log')) as $path)
      $logs[]=[
        'name'=>basename($path),
        'date'=>Carbon::createFromTimestamp(filemtime($path))
          ->format('Y-m-d'),
        'size'=>filesize($path)
      ];

    $logs=array_sort($logs,'date');
    $logs=array_reverse($logs);

    return $logs;
  }

  private function parseLogLine(string $line,string $view,?string $searchRegexp):string{
    $line=trim($line);

    if(!preg_match('|^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] .+?\.([A-Z]+):|',$line,$level))
      return $line;
    $level=$level[1];

    $error=false;
    $info=false;

    /**
     * @see \Monolog\Logger::$levels
     */
    switch($level){
      case 'DEBUG':
        break;
      case 'INFO':
        $info=true;
        break;
      case 'NOTICE':
      case 'WARNING':
      case 'ERROR':
      case 'CRITICAL':
      case 'ALERT':
      case 'EMERGENCY':
        $error=true;
        break;
    }

    if($view==='errors'&&!$error)
      return '';
    else if($view==='info'&&!$info)
      return '';
    else if($view==='search'&&(!$searchRegexp||!preg_match($searchRegexp,$line)))
      return '';

    if($error)
      $line=sprintf('<span class="log-error">%s</span>',$line);
    else if($info)
      $line=sprintf('<span class="log-info">%s</span>',$line);

    if($view==='search')
      $line=preg_replace($searchRegexp,'<span class="log-search">\0</span>',$line);

    $line=preg_replace('|^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|','<span class="log-date">\0</span>',$line);
    $line=preg_replace('| +\[.+?\]$|','<span class="log-extra">\0</span>',$line);

    return $line;
  }
}
