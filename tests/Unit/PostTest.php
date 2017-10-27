<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use App\Posts;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class PostTest extends TestCase
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
                'post_id' => 1,
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
     * Check user can update post
     */
    public function testNewPostCreationUpdateWithDate()
    {

        factory(Posts::class)->create(
            [
                'author_id' => 1,
                'id' => 1,
                'title' => 'test34534',
                'body' => '123',
                'slug' => 'test234234',

                'active' => True,
            ]);

        $this->withoutMiddleware();

        $user = $this->authenticateUser();


        $case = factory(Posts::class)->raw(
            [
                'author_id' => 1,
                'post_id' => 1,
                'title' => 'test',
                'body' => 'this is my updated article',
                'slug' => 'adkf'
            ]);

        $response = $this->actingAs($user)->post('/update', $case);
        $response->assertStatus(302);
        $response->assertSessionHas('message', "Post updated successfully");

        $response = $this->actingAs($user)->get('/test234234', $case);
        $response->assertSee('this is my updated article');
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
     * Test listing posts
     */
    public function testCanListPosts()
    {
        $this->withoutMiddleware();

        $user = $this->authenticateUser();

        $case1 = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'id', 1,
                'title' => 'test',
                'body' => '123',
                'slug' => 'adkf'
            ]);

        $this->actingAs($user)
            ->post('/new-post', $case1);

        $case2 = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'id', 2,
                'title' => 'test2',
                'body' => '12332423',
                'slug' => 'adkf1'
            ]);

        $this->actingAs($user)
            ->post('/new-post', $case2);


        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('test');
        $response->assertSee('test2');
    }

    /**
     * Test listing posts
     */
    public function testUserCanListPosts()
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

        $this->actingAs($user)
            ->post('/new-post', $case);

        $case = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'id', 2,
                'title' => 'test2',
                'body' => '123',
                'slug' => 'adkf'
            ]);

        $this->actingAs($user)
            ->post('/new-post', $case);

        $response = $this->actingAs($user)->get('user/1/posts');
        $response->assertSuccessful();
        $response->assertSee('test');
        $response->assertSee('test2');
    }

    public function testIdenticalPostsGetError()
    {
        $this->withoutMiddleware();
        $user = $this->authenticateUser();

        $case = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'id', 1,
                'title' => 'test123',
                'body' => '123',
                'slug' => 'adkf123'
            ]);

        $this->actingAs($user)
            ->post('/new-post', $case);

        $case = factory(Posts::class)->raw(
            [
                'author_id', 1,
                'id', 2,
                'title' => 'test123',
                'body' => '123',
                'slug' => 'adkf123'
            ]);

        $response = $this->actingAs($user)
            ->post('/new-post', $case);

        $response->assertStatus(500);
    }

    public function testAnyoneCanViewPost()
    {
        factory(Posts::class)->create(
            [
                'author_id' => 1,
                'id' => 1,
                'title' => 'test',
                'body' => '123',
                'slug' => 'test',
                'active' => True,
            ]);

        $response = $this->get('/test');
        $response->assertSee('/test');
    }

    public function testAddComment()
    {
        $this->withoutMiddleware();
        $user = $this->authenticateUser();

        factory(Posts::class)->create(
            [
                'author_id' => 1,
                'id' => 1,
                'title' => 'test34534',
                'body' => '123',
                'slug' => 'test234234',
                'active' => True,
            ]);

        $comment = factory(Posts::class)->raw(
            [
                'body' => 'some text',
                'on_post' => 1
            ]);

        $response = $this->actingAs($user)->post('/comment/add', $comment);
        $response->assertStatus(302);
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
