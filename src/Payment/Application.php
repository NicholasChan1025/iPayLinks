<?php
/**
 * Created by PhpStorm.
 * User: sinchan
 * Date: 2018/12/8
 * Time: 12:34
 */

namespace IPayLinks\Payment;


use IPayLinks\Kernel\ServiceContainer;

/**
 * Class Application
 * @package IPayLinks\Payment
 * @property \IPayLinks\Payment\Order\Client $order
 */
class Application extends ServiceContainer
{
    protected $providers = [
        Order\ServiceProvider::class
    ];
}