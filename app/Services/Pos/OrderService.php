<?php

namespace App\Services\Pos;


use App\Models\Order;
use App\Models\OrderItem;
use function dd;
use Illuminate\Support\Collection;
use function collect;

class OrderService
{
    /** @var  Order */
    private $order;

    /** @var  Collection */
    private $orderItems;


    /**
     * OrderService constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order      = $order;
        $this->orderItems = collect([]);
    }


    public function addOrderItem(OrderItem $orderItem)
    {
        $this->orderItems->push($orderItem);
    }


    public function removeAllOrderItems()
    {
        $this->orderItems = collect([]);
    }


    public function removeOrderItem($orderItemId)
    {
        $index = $this->getOrderItemById($orderItemId, 'getIndex');
        $this->orderItems->forget($index);
    }


    /**
     * @param      $orderItemId
     * @param null $returnIndex
     * @return mixed
     */
    public function getOrderItemById($orderItemId, $returnIndex = null)
    {
        $index = $this->orderItems->search(function ($item) use ($orderItemId) {
            return $item->id === $orderItemId;
        });


        if ($returnIndex === 'getIndex') {
            return $index;
        }

        return $this->orderItems->get($index);
    }


    public function getTotal()
    {
        $total = 0;
        foreach ($this->orderItems as $orderItem) {
            $orderItemService = new OrderItemService($orderItem);
            $total            += $orderItemService->getSubTotal();
        }

        return $total;
    }


    /**
     * @return Collection
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }


}