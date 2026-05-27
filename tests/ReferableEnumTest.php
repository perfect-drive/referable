<?php

use Illuminate\Support\Facades\Route;
use PerfectDrive\Referable\Controllers\ReferableController;

it('registers routes for referable enums', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->toHaveKey('spa/referable/basic_referable_enum');

    $route = $routes['spa/referable/basic_referable_enum'];

    expect($route->uri())->toBe('spa/referable/basic_referable_enum')
        ->and($route->action['uses'])->toBe('PerfectDrive\Referable\Controllers\ReferableController@__invoke')
        ->and($route->action['controller'])->toBe(ReferableController::class)
        ->and($route->action['middleware'])->toBeArray()->toBe(['test']);
});

it('registers routes for referable enum scope methods', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->toHaveKey('spa/referable/basic_referable_enum/active');

    $route = $routes['spa/referable/basic_referable_enum/active'];

    expect($route->uri())->toBe('spa/referable/basic_referable_enum/active')
        ->and($route->action['uses'])->toBe('PerfectDrive\Referable\Controllers\ReferableController@__invoke')
        ->and($route->action['controller'])->toBe(ReferableController::class);
});

it('does not register routes for non-referable enum scope methods', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->not->toHaveKey('spa/referable/basic_referable_enum/inactive');
});

it('does not register routes for non-referable enums', function () {
    $routes = Route::getRoutes()->getRoutesByMethod()['GET'];

    expect($routes)->not->toHaveKey('spa/referable/non_referable_enum');
});

it('returns a json response for a referable enum', function () {
    $response = $this->get('spa/referable/basic_referable_enum');

    $response->assertJson([
        [
            'value' => 1,
            'title' => 'First',
        ],
        [
            'value' => 2,
            'title' => 'Second',
        ],
        [
            'value' => 3,
            'title' => 'Third',
        ],
    ]);
});

it('returns a json response for a referable enum scope method', function () {
    $response = $this->get('spa/referable/basic_referable_enum/active');

    $response->assertJson([
        [
            'value' => 1,
            'title' => 'First',
        ],
        [
            'value' => 2,
            'title' => 'Second',
        ],
    ]);

    $response->assertJsonMissing([
        'value' => 3,
        'title' => 'Third',
    ]);
});

it('prefers name() over label() when an enum defines both', function () {
    // Pin the precedence decision: when an enum defines BOTH name() and
    // label(), name() wins. This keeps the default strictly backwards-
    // compatible — enums that already work via name() never silently flip to
    // label(). A future "fix" that reverses this should fail this test loudly.
    $response = $this->get('spa/referable/fully_defined_referable_enum');

    $response->assertJson([
        ['value' => 1, 'title' => 'NAME_ONE'],
        ['value' => 2, 'title' => 'NAME_TWO'],
    ]);

    $response->assertJsonMissing(['title' => 'Label One']);
    $response->assertJsonMissing(['title' => 'Label Two']);
});

it('returns a json response for an enum that exposes label() instead of name()', function () {
    // Auto-detect: when an enum defines `label()` but not `name()`, the trait
    // should call $case->label() to populate the human-readable value. Keeps
    // PHP's built-in `name` property unshadowed.
    $response = $this->get('spa/referable/label_referable_enum');

    $response->assertJson([
        [
            'value' => 1,
            'title' => 'Alpha',
        ],
        [
            'value' => 2,
            'title' => 'Bravo',
        ],
        [
            'value' => 3,
            'title' => 'Charlie',
        ],
    ]);
});

it('returns the specified key, value, order and additional attributes', function () {
    config()->set('referable.key_name', 'key');
    config()->set('referable.value_name', 'name');

    $response = $this->get('spa/referable/alternative_referable_enum');

    $response->assertJson([
        [
            'key' => '00000000-0000-0000-0000-000000000002',
            'name' => 'Alternative Enum 2',
            'attribute' => 'Test Attribute 2',
        ],
        [
            'key' => '00000000-0000-0000-0000-000000000001',
            'name' => 'Alternative Enum 1',
            'attribute' => 'Test Attribute 1',
        ],
    ]);
});
