<?php

namespace Metko\Activiko\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->defineRoutes();
    }

    protected function getPackageProviders($app)
    {
        return ['Metko\Activiko\ActivikoServiceProvider'];
    }

    public function signIn($user = false)
    {
        if (!$user) {
            $user = $this->userTest1;
        }

        $this->actingAs($user);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('activiko', require(dirname(__DIR__).'/config/activiko.php'));
        // $app['config']->set('view.paths', [dirname(__DIR__).'/tests/views']);
        //dd(config('activiko'));
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    public function setupDatabase()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadMigrationsFrom('migrations');
        $this->userTest1 = User::create();
        $this->userTest2 = User::create();
    }

    public function defineRoutes()
    {
        app('router')->post('/posts', "Metko\Activiko\Tests\PostsController@store");
        app('router')->patch('/posts/{post}', "Metko\Activiko\Tests\PostsController@update");
        app('router')->delete('/posts/{post}', "Metko\Activiko\Tests\PostsController@destroy");
    }
}
