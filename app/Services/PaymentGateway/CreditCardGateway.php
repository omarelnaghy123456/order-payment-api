<?php

namespace App\Services\PaymentGateway;

class CreditCardGateway implements PaymentGatewayInterface
{
    private array $config;

    public function __construct()
    {
        $this->config = config('payment_gateways.credit_card');
    }

    /**
     * Process a credit card payment
     *
     * @param array $paymentData
     * @return array
     */
    public function process(array $paymentData): array
    {
        // Validate required fields
        $this->validatePaymentData($paymentData);

        // In a real implementation, we would use the config values
        // $apiKey = $this->config['api_key'];
        // $secret = $this->config['secret'];
        // $isSandbox = $this->config['sandbox'];

        // Simulate credit card payment processing
        $success = rand(0, 1); // Simulate success/failure

        if ($success) {
            return [
                'status' => 'successful',
                'transaction_id' => uniqid('cc_'),
                'message' => 'Payment processed successfully',
                'sandbox' => $this->config['sandbox']
            ];
        }

        return [
            'status' => 'failed',
            'transaction_id' => uniqid('cc_'),
            'message' => 'Payment processing failed'
        ];
    }

    /**
     * Verify a credit card payment
     *
     * @param string $transactionId
     * @return array
     */
    public function verify($transactionId): array
    {
        return [
            'status' => 'verified',
            'transaction_id' => $transactionId,
            'sandbox' => $this->config['sandbox']
        ];
    }


    /**
     * Validate credit card payment data
     *
     * @param array $paymentData
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validatePaymentData(array $paymentData): void
    {
        $requiredFields = ['card_number', 'expiry_month', 'expiry_year', 'cvv'];

        foreach ($requiredFields as $field) {

            if (empty($paymentData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
    }
}
