<?php


namespace App\Model;


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
