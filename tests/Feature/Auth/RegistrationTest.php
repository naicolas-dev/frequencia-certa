<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@teste.com',
            'password' => 'password.P1',
            'password_confirmation' => 'password.P1',
            'has_seen_intro' => true, 
            'estado' => 'SP', 
            'ano_letivo_inicio' => date('Y-01-01'),
            'ano_letivo_fim' => date('Y-12-31')
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
