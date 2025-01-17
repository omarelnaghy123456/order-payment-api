<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentTest extends TestCase
{
    use DatabaseTransactions; // Rollback database changes after each test

    private $user;
    private $token;
    private $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = JWTAuth::fromUser($this->user);
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'confirmed'
        ]);
    }

    public function test_can_process_credit_card_payment()
    {
        $paymentData = [
            'order_id' => $this->order->id,
            'amount' => 100,
            'payment_method' => 'credit_card',
            'card_number' => '4242424242424242',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'cvv' => '123',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/payments/process', $paymentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'order_id',
                    'amount',
                    'payment_method',
                    'status',
                    'transaction_id',
                ],
            ]);
    }


    public function test_cannot_process_payment_for_pending_order()
    {
        $pendingOrder = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $paymentData = [
            'order_id' => $pendingOrder->id,
            'amount' => 100,
            'payment_method' => 'credit_card',
            'card_number' => '4242424242424242',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'cvv' => '123'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/payments/process', $paymentData);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Payment can only be processed for confirmed orders'
            ]);
    }

    public function test_can_verify_payment()
    {
        $payment = $this->order->payments()->create([
            'amount' => 100,
            'payment_method' => 'credit_card',
            'status' => 'successful',
            'transaction_id' => 'test_transaction'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson("/api/payments/{$payment->transaction_id}/verify");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'status',
                    'transaction_id'
                ]
            ]);
    }
}
