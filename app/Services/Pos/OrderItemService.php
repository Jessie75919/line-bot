<?php
namespace App\Services\Pos;

use App\Models\OrderItem;

class OrderItemService
{
    /** @var  OrderItem */
    private $orderItem;


    /**
     * OrderItemService constructor.
     * @param $orderItem
     */
    public function __construct($orderItem)
    {
        $this->orderItem = $orderItem;
    }


    public function getSubTotal()
    {
        $price = $this->orderItem->product->price;
        return $price * $this->orderItem->count;
    }


}