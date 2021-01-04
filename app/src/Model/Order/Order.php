<?php


namespace App\Model\Order;


use App\DTO\NewSubscriptionRequestDTO;

class Order implements OrderInterface
{
    private string $orderId;
    private int $amount;

    /**
     * @inheritDoc
     */
    public function getAmount():int {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function getOrderId():string{
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId):void {
        $this->orderId = $orderId;
    }


}
