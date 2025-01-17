<?php

namespace App\Services\PaymentGateway;

interface PaymentGatewayInterface
{
    /**
     * Process a payment transaction
     *
     * @param array $paymentData Payment details including amount and method-specific data
     * @return array{
     *     status: string,
     *     transaction_id?: string,
     *     message: string
     * }
     * @throws \InvalidArgumentException When payment data is invalid
     */
    public function process(array $paymentData): array;

    /**
     * Verify a payment transaction
     *
     * @param string $transactionId The transaction ID to verify
     * @return array{
     *     status: string,
     *     transaction_id: string
     * }
     * @throws \InvalidArgumentException When transaction ID is invalid
     */
    public function verify($transactionId): array;

}
