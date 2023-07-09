<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider regUser
     */
    public function test_sign_up_validation_error_required_fields(array $data, array $error, array $noError = []): void
    {
        $response = $this->post('/register', $data)
            ->assertSessionHasErrors($error)
            ->assertRedirect('/');

        if ($noError) {
            $response->assertSessionDoesntHaveErrors($noError);
        }
    }

    public static function regUser(): \Generator
    {
        yield 'required fields' => [
            ['email', 'name', 'password'],
            [
                'email' => 'The email field is required.',
                'name' => 'The name field is required.',
                'password' => 'The password field is required.',
            ],
        ];

        yield 'valid email, and min password length' => [
            ['email' => 'a', 'name' => 'a', 'password' => 'a'],
            [
                'email' => 'The email field must be a valid email address.',
                'password' => 'The password must be at least 8 characters.',
            ],
            ['name']
        ];

        yield 'password not matched' => [
            ['email' => 'a@a.com', 'name' => 'a', 'password' => '12345678'],
            [
                'password' => 'The password field confirmation does not match.',
            ],
            ['email', 'name']
        ];

        yield 'password not matched 2' => [
            ['email' => 'a@a.com', 'name' => 'a', 'password' => '12345678', 'password_confirmation' => '87654321'],
            [
                'password' => 'The password field confirmation does not match.',
            ],
            ['email', 'name']
        ];
    }

    public function test_sign_up_success(): void
    {
        $response = $this->post('/register', [
            'email' => 'oleg@petrov.com',
            'name' => 'Oleg Petrov',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->followRedirects($response)
            ->assertStatus(200)
            ->assertSeeText('Sign out as Oleg Petrov');

        $this->assertAuthenticated();

        $this->assertDatabaseHas(User::class, [
            'email' => 'oleg@petrov.com', 'name' => 'Oleg Petrov',
        ]);
    }
}
