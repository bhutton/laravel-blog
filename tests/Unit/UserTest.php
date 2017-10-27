<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class UserTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Check user can login and see new-post form
     */
    public function testProfileView()
    {
        $this->authenticateUser();
        $response = $this->get('/user/1');
        $response->assertViewIs('admin.profile');
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
