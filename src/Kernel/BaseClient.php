<?php

namespace IPayLinks\Kernel;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use IPayLinks\Kernel\Http\Response;
use IPayLinks\Kernel\Traits\HasHttpRequests;
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

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface|\IPayLinks\Kernel\Support\Collection|array|object|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    public function httpGet(string $url, array $query = [])
    {
        return $this->request($url, 'GET', ['query' => $query]);
    }
    /**
     * POST request.
     *
     * @param string $url
     * @param array  $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\IPayLinks\Kernel\Support\Collection|array|object|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }
    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     *
     * @return \Psr\Http\Message\ResponseInterface|\IPayLinks\Kernel\Support\Collection|array|object|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }
    /**
     * Upload file.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     * @param array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface|\IPayLinks\Kernel\Support\Collection|array|object|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    public function httpUpload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $multipart = [];
        foreach ($files as $name => $path) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($path, 'r'),
            ];
        }
        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }
        return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart, 'connect_timeout' => 30, 'timeout' => 30, 'read_timeout' => 30]);
    }


    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     * @param bool   $returnRaw
     *
     * @return \Psr\Http\Message\ResponseInterface|\IPayLinks\Kernel\Support\Collection|array|object|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    public function request(string $url, string $method = 'GET', array $options = [], $returnRaw = false)
    {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddlewares();
        }
        $response = $this->performRequest($url, $method, $options);
        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
    }
    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @return \IPayLinks\Kernel\Http\Response
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    public function requestRaw(string $url, string $method = 'GET', array $options = [])
    {
        return Response::buildFromPsrResponse($this->request($url, $method, $options, true));
    }
    /**
     * Return GuzzleHttp\ClientInterface instance.
     *
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        if (!($this->httpClient instanceof ClientInterface)) {
            $this->httpClient = $this->app['http_client'] ?? new Client();
        }
        return $this->httpClient;
    }
    /**
     * Register Guzzle middlewares.
     */
    protected function registerHttpMiddlewares()
    {
        // retry
        //$this->pushMiddleware($this->retryMiddleware(), 'retry');

        // log
        $this->pushMiddleware($this->logMiddleware(), 'log');
    }
    /**
     * Log the request.
     *
     * @return \Closure
     */
    protected function logMiddleware()
    {
        $formatter = new MessageFormatter($this->app['config']['http.log_template'] ?? MessageFormatter::DEBUG);
        return Middleware::log($this->app['logger'], $formatter);
    }
    /**
     * Return retry middleware.
     *
     * @return \Closure
     */
//    protected function retryMiddleware()
//    {
//        return Middleware::retry(function (
//            $retries,
//            RequestInterface $request,
//            ResponseInterface $response = null
//        ) {
//            // Limit the number of retries to 2
//            if ($retries < $this->app->config->get('http.max_retries', 1) && $response && $body = $response->getBody()) {
//                // Retry on server errors
//                $response = json_decode($body, true);
////                if (!empty($response['errcode']) && in_array(abs($response['errcode']), [40001, 40014, 42001], true)) {
////                    $this->accessToken->refresh();
////                    $this->app['logger']->debug('Retrying with refreshed access token.');
////                    return true;
////                }
//            }
//            return false;
//        }, function () {
//            return abs($this->app->config->get('http.retry_delay', 500));
//        });
//    }

}