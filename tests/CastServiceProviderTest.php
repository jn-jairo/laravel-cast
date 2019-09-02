<?php

namespace JnJairo\Laravel\Cast\Tests;

use ArrayAccess;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\CastServiceProvider;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;
use JnJairo\Laravel\Cast\Facades\Cast as CastFacade;
use JnJairo\Laravel\Cast\Tests\TestCase;

/**
 * @testdox Cast service provider
 */
class CastServiceProviderTest extends TestCase
{
    /**
     * @requires function \Illuminate\Contracts\Foundation\Application::configPath
     */
    public function test_boot_config() : void
    {
        $app = $this->prophesize(Application::class);
        $app->runningInConsole()->willReturn(true)->shouldBeCalled();
        $app->configPath('cast.php', \Prophecy\Argument::any())->willReturnArgument(0)->shouldBeCalled();

        $serviceProvider = new CastServiceProvider($app->reveal());
        $serviceProvider->boot();
    }

    public function test_register_config() : void
    {
        $app = $this->prophesize(Application::class);
        $app->willImplement(ArrayAccess::class);

        $config = $this->prophesize(Config::class);
        $config->get('cast', [])->willReturn([])->shouldBeCalled();
        $config->set('cast', require realpath(__DIR__ . '/../config/cast.php'))->shouldBeCalled();

        $app->offsetGet('config')->willReturn($config)->shouldBeCalled();
        $app->singleton('cast', \Prophecy\Argument::any())->shouldBeCalled();
        $app->singleton(CastContract::class, \Prophecy\Argument::any())->shouldBeCalled();

        $serviceProvider = new CastServiceProvider($app->reveal());
        $serviceProvider->register();
    }

    public function test_register_bind() : void
    {
        $config = $this->prophesize(Config::class);
        $config->willImplement(ArrayAccess::class);
        $config->offsetGet('cast')->willReturn([])->shouldBeCalled();
        $config->get('cast', [])->willReturn([])->shouldBeCalled();
        $config->set('cast', require realpath(__DIR__ . '/../config/cast.php'))->shouldBeCalled();

        Container::getInstance()->singleton('config', function ($app) use (&$config) {
            return $config->reveal();
        });

        $serviceProvider = new CastServiceProvider(Container::getInstance());
        $serviceProvider->register();

        $cast = Container::getInstance()->make('cast');
        $this->assertInstanceOf(Cast::class, $cast, 'Bind tag');

        $cast = Container::getInstance()->make(CastContract::class);
        $this->assertInstanceOf(Cast::class, $cast, 'Bind contract');

        CastFacade::setFacadeApplication(Container::getInstance());
        $cast = CastFacade::getFacadeRoot();
        $this->assertInstanceOf(Cast::class, $cast, 'Bind facade');
    }
}
