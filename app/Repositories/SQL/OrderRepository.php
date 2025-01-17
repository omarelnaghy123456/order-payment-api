<?php

namespace App\Repositories\SQL;

use App\Models\Order;
use App\Repositories\Contracts\OrderContract;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderContract
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getOrdersByStatus(string $status, $perPage = 15)
    {
        return $this->model->where('status', $status)->where('user_id', auth()->id())->paginate($perPage);
    }
    public function paginate($perPage = 15)
    {
        return $this->model->where('user_id', auth()->id())->paginate($perPage);
    }

    public function createOrderWithItems(array $orderData, array $items)
    {
        return DB::transaction(function () use ($orderData, $items) {
            $order = $this->create($orderData);
            $order->items()->createMany($items);
            return $order;
        });
    }

    public function canDelete($id): bool
    {
        $order = $this->find($id);
        return $order->payments()->count() === 0 && $order->user_id === auth()->id();
    }
}
