<?php

namespace PerfectDrive\Referable\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use PerfectDrive\Referable\ReferableServiceProvider;
use PerfectDrive\Referable\Tests\Middleware\TestMiddleware;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ReferableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        config()->set(
            'referable.directories',
            [
                __DIR__.'/Enums',
                __DIR__.'/Models',
            ]
        );

        config()->set(
            'referable.base_path',
            realpath(__DIR__)
        );

        config()->set(
            'referable.base_namespace',
            'PerfectDrive\\Referable\\Tests'
        );

        // Create an alias for the test middleware
        Route::aliasMiddleware('test', TestMiddleware::class);

        config()->set(
            'referable.middleware',
            ['test']
        );

        Schema::create('basic_referable_models', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active');
        });

        DB::table('basic_referable_models')->insert([
            [
                'id' => 1,
                'name' => 'Test Model 1',
                'active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Test Model 2',
                'active' => true,
            ],
            [
                'id' => 3,
                'name' => 'Test Model 3',
                'active' => false,
            ],
        ]);

        Schema::create('alternative_referable_models', static function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('title');
            $table->string('additional_attribute');
            $table->integer('ordering');
        });

        DB::table('alternative_referable_models')->insert([
            [
                'id' => 1,
                'uuid' => '00000000-0000-0000-0000-000000000001',
                'title' => 'Alternative Model 1',
                'additional_attribute' => 'Test Attribute 1',
                'ordering' => 2,
            ],
            [
                'id' => 2,
                'uuid' => '00000000-0000-0000-0000-000000000002',
                'title' => 'Alternative Model 2',
                'additional_attribute' => 'Test Attribute 2',
                'ordering' => 1,
            ],
        ]);
    }
}
