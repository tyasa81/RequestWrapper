<?php

namespace tyasa81\RequestWrapper\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use tyasa81\RequestWrapper\RequestWrapperServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'tyasa81\\RequestWrapper\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            RequestWrapperServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:0pmem4y2o21dFGegEPfRYVxUILp2W0NCcof+wpsg3ss=');
        /*
        $migration = include __DIR__.'/../database/migrations/create_requestwrapper_table.php.stub';
        $migration->up();
        */
    }
}
