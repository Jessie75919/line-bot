<?php

namespace Tests\Feature\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Pos\OrderService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{

    /** @test */
    public function it_can_add_order_item()
    {
        list($orderService, $orderItemCount) = $this->getOrderService();

        $actual   = count($orderService->getOrderItems());
        $expected = $orderItemCount;
        $this->assertEquals($expected, $actual);
    }


    /** @test */
    public function it_can_delete_order_item_by_id()
    {
        /** OrderService */
        list($orderService) = $this->getOrderService();

        $orderItemId1 = $orderService->getOrderItems()->random()->id;
        $orderItemId2 = $orderService->getOrderItems()->random()->id;

        $orderService->removeOrderItem($orderItemId1);
        $orderService->removeOrderItem($orderItemId2);

        /** @var Collection $orderItems */
        $orderItems = $orderService->getOrderItems();
        $result     = $orderItems->search(function ($item) use ($orderItemId1, $orderItemId2) {
            return $item->id === $orderItemId1 || $item->id === $orderItemId2;
        });

        $this->assertFalse($result);
    }


    /** @test */
    public function it_can_get_order_item_by_id()
    {
        /** OrderService */
        list($orderService) = $this->getOrderService();
        $expected = $orderService->getOrderItems()->random();
        $actual   = $orderService->getOrderItemById($expected->id);

        $this->assertEquals($expected, $actual);
    }


    /**
     * @return array
     */
    private function getOrderService(): array
    {
        $order          = factory(Order::class)->create();
        $orderService   = new OrderService($order);
        $orderItemCount = random_int(2, 10);

        foreach (range(1, $orderItemCount) as $item) {
            $orderItem = factory(OrderItem::class)->create(['order_id' => $order->id]);
            $orderService->addOrderItem($orderItem);
        }
        return [$orderService, $orderItemCount];
    }
}
