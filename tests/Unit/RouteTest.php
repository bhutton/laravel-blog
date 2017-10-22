<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RouteTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test landing page
     */

    public function testRoot()
    {
        $this->assertTrue(true);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Home');
    }

    /**
     * Test /home redirects to a login page
     */
    public function testHomeShowsLogin()
    {
        $this->assertTrue(true);
        $response = $this->get('/new-post');
        $response->assertStatus(302);
        $response->assertSee('/login');
    }

    /**
     * Test logging in a going to homepage
     */
    public function testNewPostLogIn()
    {
        $user = $this->authenticateUser();

        $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/new-post')
            ->assertSuccessful();
    }

    /**
     * Test login and logout
     */
    public function testLoginLogout()
    {
        $user = $this->authenticateUser();

        $response = $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/new-post');
        $response->assertSuccessful();
        $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/auth/logout');

        $this->assertTrue(true);
        $response = $this->get('/new-post');
        $response->assertStatus(302);
        $response->assertSee('/login');

    }

    /**
     * Create mock user ensuring role is set to author
     *
     * @return User
     */
    public function authenticateUser(): User
    {
        return factory(User::class)->create([
            'id' => 1,
            'name' => 'fred',
            'role' => 'author']);
    }

}
