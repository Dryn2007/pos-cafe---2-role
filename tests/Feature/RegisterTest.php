<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_register_form(): void
    {
        $this->get('/register')->assertOk();
    }

    public function test_guest_can_register_and_becomes_kasir(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Kasir',
            'email' => 'newkasir@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            // Even if someone tries to pass role, it must be ignored.
            'role' => 'admin',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticated();

        $user = User::where('email', 'newkasir@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('kasir', $user->role);
    }
}
