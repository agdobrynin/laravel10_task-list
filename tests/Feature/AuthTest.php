<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success(): void
    {
        $user = User::factory(['password' => 'password'])->create();

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/');

        $this->followRedirects($response)
            ->assertStatus(200)
            ->assertSeeText('Sign out as ' . $user->name);
    }

    public function test_login_bad_credential(): void
    {
        $this->post('/login', ['email' => 'test@test.com', 'password' => 'pass'])
            ->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
    }

    public function test_login_validation_error(): void
    {
        $this->post('/login', [])
            ->assertSessionHasErrors([
                'email' => 'The email field is required.',
                'password' => 'The password field is required.'
            ], ':message');
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();
        Auth::setUser($user);

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')
            ->assertRedirect('/');

        $this->assertNull(Auth::user());
    }
}
