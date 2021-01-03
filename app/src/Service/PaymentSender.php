<?php


namespace App\Service;

use App\Model\Order;

class PaymentSender
{
    /**
     * @param Order $order
     */
    public function sendToPaymentGateway(Order $order):void {
        //void function because send ASYNC
    }
}
