<?php


namespace App\Model\Order\State;

use App\Service\SubscriptionUserStatusMachine;

interface StateInterface
{
    public function validateProceedToNext();

    public function proceedToNext(SubscriptionUserStatusMachine $context);
}
