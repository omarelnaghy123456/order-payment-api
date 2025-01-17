# Payment Processing API

A robust Laravel-based payment processing API that supports multiple payment gateways and provides a flexible architecture for handling orders and payments.

## Features

- Multiple payment gateway support (Credit Card, PayPal)
- JWT Authentication
- Order management
- Payment processing and verification
- Extensible payment gateway architecture
- Comprehensive test coverage
- API documentation with Postman collection  

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL
- Laravel 11.x

## Installation

1. Clone the repository:
```bash
git clone https://github.com/omarelnaghy123456/order-payment-api.git
cd order-payment-api
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Configure your environment variables in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

CREDIT_CARD_API_KEY=your_api_key
CREDIT_CARD_SECRET=your_secret
CREDIT_CARD_SANDBOX=true

PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
PAYPAL_SANDBOX=true
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```
7. Generate jwt secret:
```bash
php artisan jwt:secret
```
8. Start the development server:
```bash
php artisan serve
```

## Payment Gateway Extensibility

The payment system is designed with extensibility in mind using the Strategy pattern. Here's how to add a new payment gateway:

1. Create a new gateway class in `app/Services/PaymentGateway`:
```php
namespace App\Services\PaymentGateway;

class NewGateway implements PaymentGatewayInterface
{
    public function process(array $paymentData): array
    {
        // Implement payment processing
    }

    public function verify($transactionId): array
    {
        // Implement payment verification
    }
    private function validatePaymentData(array $paymentData): void
    {
       // Implement payment data validation
    }
}
```

2. Add gateway configuration in `config/payment_gateways.php`:
```php
'new_gateway' => [
    'api_key' => env('NEW_GATEWAY_API_KEY'),
    'secret' => env('NEW_GATEWAY_SECRET'),
    'sandbox' => env('NEW_GATEWAY_SANDBOX', true),
],
```

3. Update the `PaymentGatewayFactory`:
```php
public static function create(string $gateway): PaymentGatewayInterface
{
    return match ($gateway) {
        'credit_card' => new CreditCardGateway(),
        'paypal' => new PayPalGateway(),
        'new_gateway' => new NewGateway(),
        default => throw new \InvalidArgumentException('Invalid payment gateway'),
    };
}
```

4. Add validation rules in `ProcessPaymentRequest` for the new gateway's specific fields.

## Testing

Run the test suite:
```bash
php artisan test
```

The test suite includes:
- Unit tests for payment gateways
- Feature tests for API endpoints
- Integration tests for order and payment flows

## API Documentation

- Postman collection is provided in `payment_api.postman_collection.json`

## Architecture Notes

### Repository Pattern
- Separation of concerns between controllers and data access
- Easy to switch between different data storage implementations
- Centralized data access logic

### Service Layer
- Payment gateway abstraction using Strategy pattern
- Easy to add new payment methods
- Consistent interface across different gateways

### Error Handling
- Consistent error response format
- Proper HTTP status codes
- Detailed error messages for debugging

## Security Considerations

1. Authentication
   - JWT-based authentication
   - Token expiration and refresh mechanism
   - Protected routes using middleware

2. Payment Data
   - Sensitive data validation
   - Payment gateway sandbox environments
   - No storage of sensitive payment information

3. Input Validation
   - Request validation using form requests
   - Data sanitization
   - Type checking and constraints

## Assumptions

1. Order Flow
   - Orders start in 'pending' status
   - Payment can only be processed for 'confirmed' orders
   - Orders cannot be deleted if they have associated payments
   - The implementation of items in order creation was designed as a proof of concept and may not align with best practices. However, the underlying database structure has been optimized to adhere closely to established best practices,

2. Payment Processing
   - Currently simulated for demonstration
   - Real implementation would integrate with actual payment providers
   - Sandbox mode available for testing

3. Error Handling
   - All errors return JSON responses
   - Validation errors include detailed field errors
   - System errors are logged but not exposed to clients