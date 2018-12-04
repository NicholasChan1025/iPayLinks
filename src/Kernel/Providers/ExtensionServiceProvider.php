<?php


namespace IPayLinks\Kernel\Providers;

use EasyWeChatComposer\Extension;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ExtensionServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class ExtensionServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['extension'] = function ($app) {
            return new Extension($app);
        };
    }
}
