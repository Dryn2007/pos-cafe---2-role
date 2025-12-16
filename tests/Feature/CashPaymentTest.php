<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_checkout_fails_when_amount_paid_is_less_than_total(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10000,
            'stock' => 10,
            'category' => 'Test',
            'image' => null,
        ]);

        $payload = [
            'cart' => [
                ['id' => $product->id, 'qty' => 1, 'price' => 10000],
            ],
            'total_amount' => 10000,
            'customer_name' => 'Test',
            'payment_method' => 'cash',
            'amount_paid' => 9000,
        ];

        $this->actingAs($user)
            ->postJson('/checkout', $payload)
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Uang diterima kurang.']);
    }

    public function test_cash_checkout_succeeds_and_stores_payment_fields(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);
        $product = Product::create([
            'name' => 'Test Product',
            'price' => 10000,
            'stock' => 10,
            'category' => 'Test',
            'image' => null,
        ]);

        $payload = [
            'cart' => [
                ['id' => $product->id, 'qty' => 1, 'price' => 10000],
            ],
            'total_amount' => 10000,
            'customer_name' => 'Test',
            'payment_method' => 'cash',
            'amount_paid' => 15000,
        ];

        $response = $this->actingAs($user)
            ->postJson('/checkout', $payload)
            ->assertOk()
            ->assertJsonFragment(['status' => 'success']);

        $this->assertDatabaseHas('orders', [
            'payment_method' => 'cash',
            'amount_paid' => 15000,
            'change_amount' => 5000,
            'total_price' => 10000,
            'customer_name' => 'Test',
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10000,
        ]);
    }
}
