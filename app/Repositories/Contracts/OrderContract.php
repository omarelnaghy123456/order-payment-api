<?php

namespace App\Repositories\Contracts;

interface OrderContract extends BaseContract
{
    public function getOrdersByStatus(string $status);
    public function createOrderWithItems(array $orderData, array $items);
    public function canDelete($id): bool;
}