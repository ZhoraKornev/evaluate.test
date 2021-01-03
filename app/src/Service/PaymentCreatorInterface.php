<?php


namespace App\Service;


use App\DTO\NewSubscriptionRequestDTO;
use App\Model\OrderInterface;

interface PaymentCreatorInterface
{
    public function create(NewSubscriptionRequestDTO $requestDTO):OrderInterface;
}
