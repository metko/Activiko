<?php

namespace Metko\Activiko\Tests;

use Metko\Activiko\Models\Activiko;

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
    public function it_record_only_events_given_by_the_property_model()
    {
        Post2::create(['name' => 'Title post']);
        Post2::find(1)->update(['name' => 'New title']);
        $this->assertCount(1, Activiko::all());
    }

    /** @test */
    public function it_record_only_events_given_by_activiko()
    {
        app('activiko')->onlyRecordsEvents(['updated']);
        Post::create(['name' => 'Title post', 'body' => 'blabla']);
        $post = Post::find(1);
        $post->update(['name' => 'New title', 'body' => 'hello']);
        $this->assertEquals(1, $post->activities->count());
    }

    /** @test */
    public function it_do_not_records_if_recording_is_disable_by_activiko()
    {
        Post::create(['name' => 'Title post']);
        $post = Post::find(1);
        app('activiko')->disable();
        $post->update(['name' => 'New title', 'body' => 'body']);
        $this->assertCount(1, Activiko::all());
    }

    /** @test */
    public function it_do_not_records_if_recording_is_disable_by_property_model()
    {
        // TODO
        $this->assertTrue(true);
    }

    /** @test */
    public function it_do_not_records_if_recording_is_disable_by_method_model()
    {
        // TODO
        $this->assertTrue(true);
    }

    //TODO
    // It can enable recording by model
    // It can enable recording by activiko

    /** @test */
    public function it_do_not_record_the_fields_given_by_the_property_model()
    {
        // TODO
        // Add Disable fields method an array or a string
        Post2::create(['name' => 'Title post']);
        $post = Post2::find(1);
        $post->update(['name' => 'New title', 'body' => 'body']);
        $this->assertFalse(array_key_exists('body', $post->lastChanges('before')));
        $this->assertFalse(array_key_exists('body', $post->lastChanges('after')));
    }

    /** @test */
    public function it_do_not_record_the_fields_given_by_the_method_model()
    {
        // TODO
        // Add Disable fields method an array or a string
        Post::create(['name' => 'Title post', 'body' => 'blabla']);
        $post = Post::find(1);
        $post->disableFields(['body']);
        $post->update(['name' => 'New title', 'body' => 'hello']);
        $this->assertFalse(array_key_exists('body', $post->lastChanges('before')));
        $this->assertFalse(array_key_exists('body', $post->lastChanges('before')));
    }

    /** @test */
    public function it_do_not_record_the_fields_given_by_activiko()
    {
        // TODO
        $this->assertTrue(true);
    }

    //TODO
    // it_record_only_the_fields_given_by_the_method_model
    // it_record_only_the_fields_given_by_the_property_model
    // it_record_only_the_fields_given_by_activiko
}
