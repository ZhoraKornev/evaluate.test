<?php

namespace App\Model\Order;


interface OrderInterface
{
    /**
     * @return int
     */
    public function getAmount():int;

    /**
     * @return string
     */
    public function getOrderId():string;

}
