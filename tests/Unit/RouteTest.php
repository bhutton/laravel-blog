<?php

namespace Tests\Unit;

use Tests\TestCase;
use Laravel\Passport\HasApiTokens;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RouteTest extends TestCase
{
    use DatabaseMigrations;
    protected $client;
    protected $fillable = [
        'id', 'name', 'email', 'password',
    ];

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
            'id' => 1,
            'name' => 'yish',
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
            'id' => 1,
            'name' => 'yish',
            'role' => 'author'
        ]);

        $this->actingAs($user)
            ->withSession(['users' => 'fred bloggs'])
            ->get('/new-post')
            ->assertSuccessful();

    }

    protected function asLoginUser(User $user){
        return $this->actingAs($user)
            ->withSession(['applocalemodel' => 'ca']);
    }

//    public function testNewPostPost()
//    {
//        $user = $this->authenticateUser();
//        $id = uniqid();
//
//        $response = $this->actingAs($user)
//            ->withSession(['foo' => 'bar'])
//            ->withHeaders([
//            'X-Header' => 'Value',])->json('POST', 'new-post',
//            [
//                'id' => $id,
//                'title' => 'My Random Test Book',
//                'slug' => 'Slug',
//                'body' => 'Body',
//                'active' => 1,
//                'created_at' => '0000-00-00 00:00:00',
//                'updated_at' => '0000-00-00 00:00:00',
//                'author_id' => '1233'
//            ])->assertSuccessful();
//
////        $response
////            ->assertStatus(200)
////            ->assertJson([
////                'created' => true,
////            ]);
//
//
//
//
//    }

    /**
     * @return ?
     */
    public function authenticateUser()
    {
        $password = '1qazwsx'; // Our password

        $user = factory(User::class)->create([
            'name' => 'fred',
            'email' => 'taylor@laravel.com',
            'password' => bcrypt($password) // Save the encrypted
        ]);
        return $user;
    }
}
