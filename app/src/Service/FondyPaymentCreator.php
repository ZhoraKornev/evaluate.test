<?php


namespace App\Service;


use App\DTO\NewSubscriptionRequestDTO;
use App\Model\Order;
use JetBrains\PhpStorm\Pure;

class FondyPaymentCreator implements PaymentCreatorInterface
{
    /**
     * @param NewSubscriptionRequestDTO $requestDTO
     *
     * @return Order
     */
    #[Pure] public function create(NewSubscriptionRequestDTO $requestDTO):Order{
        $order = new Order();
        $order->setOrderId($requestDTO->user->getIdAsString() . '|' . $requestDTO->subscription_id);

        return $order;
    }
}
