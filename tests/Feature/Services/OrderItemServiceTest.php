<?php

namespace Tests\Feature\Services;

use App\Models\OrderItem;
use App\Services\Pos\OrderItemService;
use Tests\TestCase;
use function factory;

class OrderItemServiceTest extends TestCase
{
    /** @test */
    public function it_should_get_sub_total()
    {
        $orderItem        = factory(OrderItem::class)->create(['order_id' => 1]);
        $orderItemService = new OrderItemService($orderItem);
        $actual           = $orderItemService->getSubTotal();
        $expected         = $orderItem->product->price * $orderItem->count;
        self::assertEquals($expected, $actual);
    }
}
