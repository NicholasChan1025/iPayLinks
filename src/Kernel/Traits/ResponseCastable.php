<?php
/**
 * Created by PhpStorm.
 * User: sinchan
 * Date: 2018/12/4
 * Time: 14:54
 */

namespace IPayLinks\Kernel\Traits;

use IPayLinks\Kernel\Contracts\Arrayable;
use IPayLinks\Kernel\Exceptions\InvalidArgumentException;
use IPayLinks\Kernel\Exceptions\InvalidConfigException;
use IPayLinks\Kernel\Http\Response;
use IPayLinks\Kernel\Support\Collection;
use Psr\Http\Message\ResponseInterface;

trait ResponseCastable
{
    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string|null                         $type
     *
     * @return array|\IPayLinks\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    protected function castResponseToType(ResponseInterface $response, $type = null)
    {
        $response = Response::buildFromPsrResponse($response);
        $response->getBody()->rewind();
        switch ($type ?? 'array') {
            case 'collection':
                return $response->toCollection();
            case 'array':
                return $response->toArray();
            case 'object':
                return $response->toObject();
            case 'raw':
                return $response;
            default:
                if (!is_subclass_of($type, Arrayable::class)) {
                    throw new InvalidConfigException(sprintf(
                        'Config key "response_type" classname must be an instanceof %s',
                        Arrayable::class
                    ));
                }
                return new $type($response);
        }
    }
    /**
     * @param mixed       $response
     * @param string|null $type
     *
     * @return array|\IPayLinks\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \IPayLinks\Kernel\Exceptions\InvalidArgumentException
     * @throws \IPayLinks\Kernel\Exceptions\InvalidConfigException
     */
    protected function detectAndCastResponseToType($response, $type = null)
    {
        switch (true) {
            case $response instanceof ResponseInterface:
                $response = Response::buildFromPsrResponse($response);
                break;
            case $response instanceof Arrayable:
                $response = new Response(200, [], json_encode($response->toArray()));
                break;
            case ($response instanceof Collection) || is_array($response) || is_object($response):
                $response = new Response(200, [], json_encode($response));
                break;
            case is_scalar($response):
                $response = new Response(200, [], $response);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported response type "%s"', gettype($response)));
        }
        return $this->castResponseToType($response, $type);
    }
}