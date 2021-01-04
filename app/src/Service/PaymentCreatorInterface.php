<?php


namespace App\Service;


use App\DTO\NewSubscriptionRequestDTO;
use App\Model\Order\OrderInterface;

interface PaymentCreatorInterface
{
    public function create(NewSubscriptionRequestDTO $requestDTO):OrderInterface;
}
