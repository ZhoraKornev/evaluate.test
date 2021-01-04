<?php

namespace App\Model\Order\State;


use App\Exception\IllegalStateTransitionException;
use App\Service\SubscriptionUserStatusMachine;
use DateTime;

class ActiveState extends State
{

    private const CURRENT_STATUS = 'active';

    public function toString():string {
        return ActiveState::CURRENT_STATUS;
    }

    /**
     * @param SubscriptionUserStatusMachine $context
     *
     * @throws IllegalStateTransitionException
     */
    public function proceedToNext(SubscriptionUserStatusMachine $context) {
        $subscriptionUser = $context->getSubscriptionUser();
        $subscriptionUser->setActive(false);
        $subscriptionUser->setValidDue(new DateTime());
        $context->setSubscriptionUser($subscriptionUser);

        $context->setState(new NonActiveState());
    }
}
