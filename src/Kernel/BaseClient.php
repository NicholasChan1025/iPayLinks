<?php

namespace IPayLinks\Kernel;

use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class BaseClient{

    use HasHttpRequests { request as performRequest; }

    /**
     * @var \IPayLinks\Kernel\ServiceContainer
     */
    protected $app;

    /**
     * @var
     */
    protected $baseUri;

    /**
     * BaseClient constructor.
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

}