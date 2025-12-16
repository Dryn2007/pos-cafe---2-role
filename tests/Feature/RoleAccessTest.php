<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected_to_login_when_access_pos(): void
    {
        $this->get('/')
            ->assertRedirect('/login');
    }

    public function test_kasir_can_access_pos_but_cannot_access_admin_products(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($kasir)
            ->get('/')
            ->assertOk();

        $this->actingAs($kasir)
            ->get('/admin/products')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_products_but_cannot_access_pos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/')
            ->assertForbidden();
    }
}
