<?php

namespace App\Repositories\SQL;

use App\Models\Payment;
use App\Models\Order;
use App\Repositories\Contracts\PaymentContract;
use App\Services\PaymentGateway\PaymentGatewayFactory;
use Illuminate\Support\Facades\DB;

class PaymentRepository extends BaseRepository implements PaymentContract
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function processPayment(array $paymentData)
    {
        return DB::transaction(function () use ($paymentData) {
            // Verify order status
            $order = Order::findOrFail($paymentData['order_id']);
            if ($order->status !== 'confirmed') {
                throw new \Exception('Payment can only be processed for confirmed orders');
            }

            // Process payment through gateway
            $gateway = PaymentGatewayFactory::create($paymentData['payment_method']);
            $result = $gateway->process($paymentData);

            // Create payment record
            $payment = $this->create([
                'order_id' => $paymentData['order_id'],
                'amount' => $paymentData['amount'],
                'payment_method' => $paymentData['payment_method'],
                'status' => $result['status'],
                'transaction_id' => $result['transaction_id'] ?? null,
                'gateway_response' => json_encode($result)
            ]);
            return $payment;
        });
    }

    public function getPaymentsByOrder($orderId)
    {
        return $this->model->where('order_id', $orderId)->get();
    }

    public function verifyPayment($transactionId)
    {
        $payment = $this->model->where('transaction_id', $transactionId)->firstOrFail();
        $gateway = PaymentGatewayFactory::create($payment->payment_method);
        return $gateway->verify($transactionId);
    }
    public function paginate($perPage = 15)
    {
        return $this->model->whereHas('order', function ($query) {
            $query->where('user_id', auth()->id());
        })->paginate($perPage);
    }
}
