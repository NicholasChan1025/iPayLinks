<?php

/**
 * This file is part of iPayLinks.
 * (c) sinchan <651906195@qq.com>
 */

namespace IPayLinks;

/**
 * Class Factory
 * @package IPayLinks
 * @method static \IPayLinks\Payment\Application payment(array $config)
 */
class Factory
{
    /**
     * @param string $name
     * @param array  $config
     *
     * @return \IPayLinks\Kernel\ServiceContainer
     */
    public static function make($name, array $config)
    {
        $namespace = Kernel\Support\Str::studly($name);
        $application = "\\IPayLinks\\{$namespace}\\Application";

        return new $application($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
} 
