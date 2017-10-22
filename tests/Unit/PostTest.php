<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use App\Posts;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class PostTestWithoutMiddleware extends TestCase
{
    use DatabaseMigrations;

    /**
     * Check user can login and see new-post form
     */
    public function testNewPostGet()
    {
        $user = $this->authenticateUser();

        $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->get('new-post')
            ->assertSuccessful();
    }

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
        $this->withoutMiddleware();

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
     * Check user can update post
     */
    public function testNewPostCreationUpdate()
    {
        $this->withoutMiddleware();

        $user = $this->authenticateUser();

        $case = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'title' => 'test',
                'body' => '123',
                'slug' => 'adkf'
            ]);

        $response = $this->actingAs($user)
            ->post('/new-post', $case);

        $response->assertSee('edit/test');
        $response = $this->actingAs($user)->post('/update', $case);
        $response->assertStatus(302);
        $response->assertSessionHas('message', "Post updated successfully");

        $response = $this->actingAs($user)->get('/test', $case);
        $response->assertSuccessful();
    }

    /**
     * Create a post then delete it
     */
    public function testUserCanDeletePost()
    {
        $this->withoutMiddleware();

        $user = $this->authenticateUser();

        $case = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'id', 1,
                'title' => 'test',
                'body' => '123',
                'slug' => 'adkf'
            ]);

        $response = $this->actingAs($user)
            ->post('/new-post', $case);

        $response->assertSee('/test');
        $response = $this->actingAs($user)->get('delete/1');
        $response->assertStatus(302);
        $response->assertSessionHas('message', "Post deleted Successfully");

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
