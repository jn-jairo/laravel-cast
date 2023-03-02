<?php

namespace JnJairo\Laravel\Cast\Tests;

use JnJairo\Laravel\Cast\CastServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            CastServiceProvider::class,
        ];
    }
}
