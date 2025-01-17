<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Repositories\Contracts\PaymentContract;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected $paymentRepository;

    public function __construct(PaymentContract $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $orderId = $request->query('order_id');
            $payments = $orderId
                ? $this->paymentRepository->getPaymentsByOrder($orderId)
                : $this->paymentRepository->paginate();

            return $this->resourceCollectionResponse(PaymentResource::collection($payments), 'Payments retrieved successfully', 200, 'payments');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function process(ProcessPaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->processPayment($request->validated());
            return $this->successResponse(new PaymentResource($payment), 'Payment processed successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
    public function show($id): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->find($id);
            return $this->resourceResponse(new PaymentResource($payment), 'Payment retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function verify($transactionId): JsonResponse
    {
        try {
            $result = $this->paymentRepository->verifyPayment($transactionId);
            return $this->successResponse($result, 'Payment verification successful');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }
}
