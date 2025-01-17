<?php

namespace App\Repositories\Contracts;

interface PaymentContract extends BaseContract
{
    public function processPayment(array $paymentData);
    public function getPaymentsByOrder($orderId);
    public function verifyPayment($transactionId);
}