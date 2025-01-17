<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\Contracts\OrderContract;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $orderRepository;

    public function __construct(OrderContract $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status');
            $perPage = $request->query('limit', 10);
            $orders = $status
                ? $this->orderRepository->getOrdersByStatus($status, $perPage)
                : $this->orderRepository->paginate($perPage);

            return $this->resourceCollectionResponse(OrderResource::collection($orders), 'Orders retrieved successfully', 200, 'orders');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(OrderRequest $request): JsonResponse
    {
        try {
            $orderData = $request->validated();
            $orderData['user_id'] = auth()->id();
            $orderData['status'] = 'pending';

            $order = $this->orderRepository->createOrderWithItems(
                $orderData,
                $request->items
            );

            return $this->successResponse(new OrderResource($order), 'Order created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(OrderRequest $request, $id): JsonResponse
    {
        try {
            $order = $this->orderRepository->update($request->validated(), $id);
            return $this->noDatasuccessResponse('Order updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function show($id): JsonResponse
    {
        try {
            $order = $this->orderRepository->find($id);
            return $this->resourceResponse(new OrderResource($order), 'Order retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
    public function destroy($id): JsonResponse
    {
        try {
            if (!$this->orderRepository->canDelete($id)) {
                return $this->errorResponse('Cannot delete order with associated payments or not your order', 422);
            }

            $this->orderRepository->delete($id);
            return $this->noDatasuccessResponse('Order deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
