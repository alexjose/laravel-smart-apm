<?php

namespace SmartAPM;

use DateTime;
use SmartAPM\Jobs\PostRequest;
// use Illuminate\Console\Events\CommandFinished;
// use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class APMCollector
{
    private $collectors = [
        'queries' => [],
    ];


    private $request = [];

    private $responseCode;

    private $requestAt;

    private $isConsoleCommand = false;

    private $memoryUsage;

    function __construct()
    {

        $this->request['requested_at'] = Carbon::parse(DateTime::createFromFormat('U.u', $_SERVER['REQUEST_TIME_FLOAT']));

        app()->booted(function(){
            $this->request['booted_at'] = now();
        });

        app()->terminating(function(){

            $request = request();

            $this->request['completed_at'] = now();
            $this->request += [
                'path' => $request->getPathInfo(),
                'uri' => $request->getRequestUri(),
                'query_string' => $request->getQueryString(),
                // 'server' => $request->server(),
                'route_action' => Route::currentRouteAction(),
                'memory_usage' => memory_get_peak_usage(),
                'php_version' => phpversion(),
                'auth'=> Auth::check(),
                'auth_id' => Auth::id(),
            ];

            PostRequest::dispatch($this->request);
            // dump($this->request);
        });


        if (app()->runningInConsole()) {
            
            $this->isConsoleCommand = true;
            
            // app()->events->listen(CommandStarting::class, function () {
            //     // TODO
            // });
            // app()->events->listen(CommandFinished::class, function () {
            //     // TODO
            // });
        } else {
            app()->make('Illuminate\Contracts\Http\Kernel')
                ->prependMiddleware('SmartAPM\Middleware\APMMiddleware');
        }

        app()->events->listen(QueryExecuted::class, function (QueryExecuted $query) {
            // dump($query);
            $this->request['collectors']['queries'][] =[
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
                // 'table' =>  explode('`',$query->sql,3)[1],
                'connection_name' => $query->connectionName
            ];
            // dump((array)$query);
        });
    }

    public function setResponseCode($statusCode)
    {
        $this->request['response_code'] = $statusCode;
    }
}
