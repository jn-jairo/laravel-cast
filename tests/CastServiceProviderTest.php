<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\CastServiceProvider;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;
use JnJairo\Laravel\Cast\Facades\Cast as CastFacade;

it('was published', function () {
    expect(CastServiceProvider::$publishes)
        ->toHaveKey(CastServiceProvider::class);

    expect(CastServiceProvider::$publishes[CastServiceProvider::class])
        ->toContain(config_path('cast.php'));

    expect(CastServiceProvider::$publishGroups)
        ->toHaveKey('config');

    expect(CastServiceProvider::$publishGroups['config'])
        ->toContain(config_path('cast.php'));
});

it('has registered the configuration file', function () {
    expect(config('cast'))
        ->toBe(require realpath(__DIR__ . '/../config/cast.php'));
});

it('has registered the bindings', function () {
    expect(app('cast'))
        ->toBeInstanceOf(Cast::class);

    expect(app(CastContract::class))
        ->toBeInstanceOf(Cast::class);

    expect(CastFacade::getFacadeRoot())
        ->toBeInstanceOf(Cast::class);
});
