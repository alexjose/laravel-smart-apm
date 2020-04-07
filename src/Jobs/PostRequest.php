<?php

namespace SmartAPM\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PostRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request =  $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Client $client)
    {

        $response = $client->post('http://smart-apm.test/api/request', [
            'json' => [
                'token' => config('smartapm.key')
            ] + $this->request,
            'headers' => [
                'Accept'     => 'application/json',
            ]
        ]);

    }
}
