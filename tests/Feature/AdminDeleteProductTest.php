<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $product = Product::create([
            'name' => 'Product To Delete',
            'price' => 10000,
            'stock' => 5,
            'category' => 'drink',
            'image' => null,
        ]);

        $this->actingAs($admin)
            ->delete('/admin/products/' . $product->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_kasir_cannot_delete_product(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $product = Product::create([
            'name' => 'Product To Delete',
            'price' => 10000,
            'stock' => 5,
            'category' => 'drink',
            'image' => null,
        ]);

        $this->actingAs($kasir)
            ->delete('/admin/products/' . $product->id)
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }
}
