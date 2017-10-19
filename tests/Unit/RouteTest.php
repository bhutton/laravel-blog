<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RouteTest extends TestCase
{
    use DatabaseMigrations;

    public function testRoot()
    {
        $this->assertTrue(true);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Home');
    }

    public function testHomeShowsLogin()
    {
        $this->assertTrue(true);
        $response = $this->get('/home');
        $response->assertStatus(302);
        $response->assertSee('/login');
    }

    public function testHomeLogsIn()
    {

        $user = factory(User::class)->create([
            'role' => 'author'
        ]);

        $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/home')
            ->assertSuccessful();

    }

    public function testCreateNewPostInvalidUser()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/new-post')
            ->assertStatus(302);

    }

    public function testNewPostGet()
    {
        $user = factory(User::class)->create([
            'role' => 'author'
        ]);

        $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->get('/new-post')
            ->assertSuccessful();

    }

}
