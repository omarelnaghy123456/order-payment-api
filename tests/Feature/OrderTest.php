<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderTest extends TestCase
{
    use DatabaseTransactions; // Rollback database changes after each test

    private $user;
    private $token;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
    }
    public function test_can_create_order()
    {
        $orderData = [
            'items' => [
                [
                    'product_name' => 'Test Product',
                    'quantity' => 2,
                    'price' => 100,
                ]
            ],
            'total_amount' => 200,
            'status' => 'pending',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'total_amount',
                    'status',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_can_get_orders()
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'orders' => [
                        '*' => [
                            'id',
                            'user_id',
                            'total_amount',
                            'status',
                            'created_at',
                            'updated_at',
                        ],
                       
                    ]
                ]
            ]);
    }

    public function test_cannot_delete_order_with_payments()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed'
        ]);

        // Create a payment for the order
        $order->payments()->create([
            'amount' => 100,
            'payment_method' => 'credit_card',
            'status' => 'successful',
            'transaction_id' => 'test_transaction'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Cannot delete order with associated payments or not your order'
            ]);
    }
}
