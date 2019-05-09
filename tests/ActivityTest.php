<?php

namespace Metko\Activiko\Tests;

use Metko\Activiko\Models\Activiko;
use Metko\Activiko\Exceptions\ActivityDoesNotExists;

class ActivityTest extends TestCase
{
    public function setup(): void
    {
        parent::setup();
    }

    /** @test */
    public function an_activity_belongs_to_the_right_model()
    {
        Post::create(['name' => 'Title post']);
        $this->assertInstanceOf(Activiko::class, Post::find(1)->activities->first());
    }

    /** @test */
    public function creating_a_model_log_an_activity()
    {
        Post::create(['name' => 'Title post']);
        $this->assertCount(1, Activiko::all());
    }

    /** @test */
    public function creating_a_model_does_not_records_before_changes()
    {
        Post::create(['name' => 'Title post']);
        $this->assertTrue(empty(Post::first()->lastChanges('before')));
    }

    /** @test */
    public function updating_a_model_log_an_activity()
    {
        Post::create(['name' => 'Title post']);
        Post::find(1)->update(['name' => 'New title', 'body' => 'blabla']);
        $this->assertCount(2, Activiko::all());
    }

    /** @test */
    public function deleting_a_model_log_an_activity()
    {
        Post::create(['name' => 'Title post']);
        Post::find(1)->delete();
        $this->assertCount(2, Activiko::all());
    }

    /** @test */
    public function it_records_only_the_specified_events()
    {
        Post2::create(['name' => 'Title post']);
        Post2::find(1)->update(['name' => 'New title']);
        $this->assertCount(1, Activiko::all());
    }

    /** @test */
    public function it_do_not_records_given_column()
    {
        //$this->expectException(ActivityDoesNotExists::class);
        Post2::create(['name' => 'Title post']);
        $post = Post2::find(1);
        $post->update(['name' => 'New title', 'body' => 'body']);
        $this->assertFalse(array_key_exists('body', $post->lastChanges('before')));
        $this->assertFalse(array_key_exists('body', $post->lastChanges('after')));
    }

    /** @test */
    public function it_do_not_records_if_its_disable()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        $post->disableRecord();
        $post->update(['name' => 'New title', 'body' => 'body']);
        $this->assertCount(1, Activiko::all());
    }
}
