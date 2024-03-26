<?php

use Illuminate\Support\Facades\Route;
use PerfectDrive\Referable\Controllers\ReferableController;

it('registers routes for referable models', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->toHaveKey('spa/referable/basic_referable_model');

    $route = $routes['spa/referable/basic_referable_model'];

    expect($route->uri())->toBe('spa/referable/basic_referable_model')
        ->and($route->action['uses'])->toBe('PerfectDrive\Referable\Controllers\ReferableController@__invoke')
        ->and($route->action['controller'])->toBe(ReferableController::class)
        ->and($route->action['middleware'])->toBeArray()->toBe(['test']);
});

it('registers routes for referable scopes', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->toHaveKey('spa/referable/basic_referable_model/active');

    $route = $routes['spa/referable/basic_referable_model/active'];

    expect($route->uri())->toBe('spa/referable/basic_referable_model/active')
        ->and($route->action['uses'])->toBe('PerfectDrive\Referable\Controllers\ReferableController@__invoke')
        ->and($route->action['controller'])->toBe(ReferableController::class);
});

it('does not register routes for non-referable scopes', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->not->toHaveKey('spa/referable/basic_referable_model/inactive');
});

it('does not register routes for non-referable models', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->not->toHaveKey('spa/referable/non_referable_model');
});

it('returns a json response for a referable model', function () {
    $response = $this->get('spa/referable/basic_referable_model');

    $response->assertJson([
        [
            'value' => 1,
            'title' => 'Test Model 1',
        ],
        [
            'value' => 2,
            'title' => 'Test Model 2',
        ],
        [
            'value' => 3,
            'title' => 'Test Model 3',
        ],
    ]);
});

it('returns a json response for a referable scope', function () {
    $response = $this->get('spa/referable/basic_referable_model/active');

    $response->assertJson([
        [
            'value' => 1,
            'title' => 'Test Model 1',
        ],
        [
            'value' => 2,
            'title' => 'Test Model 2',
        ],
    ]);

    $response->assertJsonMissing([
        'value' => 3,
        'title' => 'Test Model 3',
    ]);
});

it('returns the specified key, value, order and additional attributes', function () {
    config()->set('referable.key_name', 'key');
    config()->set('referable.value_name', 'name');

    $response = $this->get('spa/referable/alternative_referable_model');

    $response->assertJson([
        [
            'key' => '00000000-0000-0000-0000-000000000002',
            'name' => 'Alternative Model 2',
            'attribute' => 'Test Attribute 2',
        ],
        [
            'key' => '00000000-0000-0000-0000-000000000001',
            'name' => 'Alternative Model 1',
            'attribute' => 'Test Attribute 1',
        ],
    ]);
});
