<?php

namespace Tests\Unit;

use App\Services\PaymentGateway\CreditCardGateway;
use App\Services\PaymentGateway\PayPalGateway;
use App\Services\PaymentGateway\PaymentGatewayFactory;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    public function test_payment_gateway_factory_creates_credit_card_gateway()
    {
        $gateway = PaymentGatewayFactory::create('credit_card');
        $this->assertInstanceOf(CreditCardGateway::class, $gateway);
    }

    public function test_payment_gateway_factory_creates_paypal_gateway()
    {
        $gateway = PaymentGatewayFactory::create('paypal');
        $this->assertInstanceOf(PayPalGateway::class, $gateway);
    }

    public function test_payment_gateway_factory_throws_exception_for_invalid_gateway()
    {
        $this->expectException(\InvalidArgumentException::class);
        PaymentGatewayFactory::create('invalid_gateway');
    }

    public function test_credit_card_gateway_process_payment()
    {
        $gateway = new CreditCardGateway();
        $result = $gateway->process([
            'card_number' => '4242424242424242',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'cvv' => '123'
        ]);

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        if ($result['status'] === 'successful') {
            $this->assertArrayHasKey('transaction_id', $result);
        }
    }

    public function test_paypal_gateway_process_payment()
    {
        $gateway = new PayPalGateway();
        $result = $gateway->process([
            'paypal_email' => 'test@example.com'
        ]);

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        if ($result['status'] === 'successful') {
            $this->assertArrayHasKey('transaction_id', $result);
        }
    }
    public function test_credit_card_gateway_validates_missing_fields()
    {
        $gateway = new CreditCardGateway();
    
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required field: expiry_year");
    
        $gateway->process([
            'card_number' => '4242424242424242',
            'expiry_month' => '12'
            // Missing expiry_year and cvv
        ]);
    }
    
}
