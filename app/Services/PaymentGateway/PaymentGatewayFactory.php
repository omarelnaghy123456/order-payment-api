<?php

namespace App\Services\PaymentGateway;

class PaymentGatewayFactory
{
    /**
     * Create a new payment gateway instance
     *
     * @param string $gateway The gateway identifier
     * @return PaymentGatewayInterface
     * @throws \InvalidArgumentException When gateway is not supported
     */
    public static function create(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'credit_card' => new CreditCardGateway(),
            'paypal' => new PayPalGateway(),
            default => throw new \InvalidArgumentException('Invalid payment gateway'),
        };
    }
}
