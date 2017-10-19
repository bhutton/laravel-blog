<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $password = '1qazwsx'; // Our password

        $user = factory(User::class)->create([
            'name' => 'fred',
            'email' => 'taylor@laravel.com',
            'password' => bcrypt($password) // Save the encrypted
        ]);

        $this->browse(function ($browser) use ($user, $password) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', $password) // Enter plain password
                ->press('Login')
                ->assertPathIs('/')
                ->assertSee('You are logged in!')
                ->assertSee($user->name);
        });
    }
}