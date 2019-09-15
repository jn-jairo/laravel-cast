<?php

namespace JnJairo\Laravel\Cast\Tests;

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\CastServiceProvider;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;
use JnJairo\Laravel\Cast\Facades\Cast as CastFacade;
use JnJairo\Laravel\Cast\Tests\OrchestraTestCase as TestCase;

/**
 * @testdox Cast service provider
 */
class CastServiceProviderTest extends TestCase
{
    public function test_boot_config() : void
    {
        $this->assertArrayHasKey(CastServiceProvider::class, CastServiceProvider::$publishes, 'Publish class');
        $this->assertContains(
            config_path('cast.php'),
            CastServiceProvider::$publishes[CastServiceProvider::class],
            'Publish path'
        );
        $this->assertArrayHasKey('config', CastServiceProvider::$publishGroups, 'Publish group class');
        $this->assertContains(
            config_path('cast.php'),
            CastServiceProvider::$publishGroups['config'],
            'Publish group path'
        );
    }

    public function test_register_config() : void
    {
        $this->assertSame(config('cast'), require realpath(__DIR__ . '/../config/cast.php'), 'Configuration content');
    }

    public function test_register_bind() : void
    {
        $cast = app('cast');
        $this->assertInstanceOf(Cast::class, $cast, 'Bind tag');

        $cast = app(CastContract::class);
        $this->assertInstanceOf(Cast::class, $cast, 'Bind contract');

        $cast = CastFacade::getFacadeRoot();
        $this->assertInstanceOf(Cast::class, $cast, 'Bind facade');
    }
}
