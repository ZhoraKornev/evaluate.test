<?php


namespace App\Model\Order\State;

use App\Exception\IllegalStateTransitionException;
use App\Service\SubscriptionUserStatusMachine;
use DateTime;

class NonActiveState extends State
{

    private const CURRENT_STATUS = 'non_active';

    public function toString():string {
        return NonActiveState::CURRENT_STATUS;
    }

    /**
     * @param SubscriptionUserStatusMachine $context
     *
     * @throws IllegalStateTransitionException
     */
    public function proceedToNext(SubscriptionUserStatusMachine $context) {
        $subscriptionUser = $context->getSubscriptionUser();
        $subscriptionUser->setActive(true);
        $subscriptionUser->setActivateAt(new DateTime());
        $subscriptionUser->setValidDue((new DateTime())->modify("+{$subscriptionUser->getSubscription()?->getPeriod()} days"));
        $context->setSubscriptionUser($subscriptionUser);
        $context->setState(new ActiveState());
    }
}
