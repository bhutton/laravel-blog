<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use App\User;
use App\Posts;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PostTest extends TestCase
{
    use WithoutMiddleware;
    use DatabaseMigrations;

    /**
     * Check invalid login/nologin cannot open /new-post
     */
    public function testCreateNewPostInvalidUser()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/new-post')
            ->assertStatus(302);
    }


    /**
     * Check valid user can open /new-post
     */
    public function testNewPostCreation()
    {
        $user = $this->authenticateUser();

        $case = factory(Posts::class)->raw(
            [
                'title' => 'test',
                'body' => '123',
                'slug' => 'adkf'
            ]);

        $response = $this->actingAs($user)
            ->post('/new-post', $case);

        $response->assertStatus(302);
        $response->assertSee('edit/test');
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
