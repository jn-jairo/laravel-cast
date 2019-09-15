<?php

namespace JnJairo\Laravel\Cast\Tests;

use JnJairo\Laravel\Cast\CastServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class OrchestraTestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CastServiceProvider::class,
        ];
    }
}
