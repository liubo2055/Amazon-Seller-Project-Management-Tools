<?php

namespace Hui\Xproject\Providers;

use Illuminate\Log\Writer;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class LogServiceProvider extends ServiceProvider{
  public function boot():void{
    $logger=logger();
    /**
     * @var Writer $logger
     */
    if(!is_callable([
      $logger,
      'getMonolog'
    ]))
      return;

    /**
     * @var Logger $monolog
     */
    $monolog=$logger->getMonolog();

    // Include the PID of the process in each log line
    // This can be achieved with $this->app->configureMonologUsing(), but doing so prevents Laravel from using the default handler according to the app.log
    // configuration entry
    $monolog->pushProcessor(function(array $register):array{
      $trace=debug_backtrace(0,8);
      if(count($trace)===8)
        // If using the logger() helper method...
        if($trace[6]['function']==='logger'){
          $previous=$trace[7];
          $previous+=[
            'class'=>'N/A',
            'line'=>'N/A'
          ];

          $source=sprintf('%s::%s(), %s',
            $previous['class'],
            $previous['function'],
            $trace[6]['line']);

          $register['extra'][]=$source;
        }

      if(app()->runningInConsole())
        $register['channel'].=sprintf('-%u',getmypid());

      return $register;
    });
  }
}
