<?php

namespace Metko\Activiko\Unit\Tests;

use Metko\Activiko\Tests\Post;
use Metko\Activiko\Tests\TestCase;
use Metko\Activiko\Exceptions\ActivityDoesNotExists;

class ActivityTest extends TestCase
{
    public function setup(): void
    {
        parent::setup();
        $this->defineRoutes();
    }

    /** @test */
    public function it_has_lastChanges()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        $this->assertEquals($post->activities->last()->change, $post->lastChanges());
    }

    /** @test */
    public function it_has_lastChanges_only_on_after()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        $this->assertEquals($post->activities->last()->change['after'], $post->lastChanges('after'));
    }

    /** @test */
    public function it_has_lastChanges_only_on_before()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        $this->assertEquals($post->activities->last()->change['before'], $post->lastChanges('before'));
    }

    /** @test */
    public function it_has_lastChanges_only_on_a_given_column()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        $this->assertEquals($post->activities->last()->change['after']['name'], $post->lastChanges('name', 'after'));
    }

    /** @test */
    public function a_unknow_column_will_trigger_an_exception()
    {
        $this->expectException(ActivityDoesNotExists::class);
        Post::create(['name' => 'Title post']);
        Post::find(1)->lastChanges('body');
    }

    /** @test */
    public function a_unknow_argument_for_at_will_trigger_an_exception()
    {
        $this->expectException(ActivityDoesNotExists::class);
        Post::create(['name' => 'Title post']);
        Post::find(1)->lastChanges('name', 'wrong_moment');
    }

    /** @test */
    public function it_has_disableRecord()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        $post->disableRecord();
        $this->assertTrue($post->disableRecord);
    }
}
