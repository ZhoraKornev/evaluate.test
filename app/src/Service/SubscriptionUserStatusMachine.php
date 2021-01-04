<?php


namespace App\Service;

use App\Entity\SubscriptionUser;
use App\Model\Order\State\State;

class SubscriptionUserStatusMachine
{
    private State $state;

    private SubscriptionUser $subscriptionUser;

    /**
     * OrderContext constructor.
     *
     * @param State $state
     */
    public function __construct(State $state) {
        $this->state = $state;
    }

    public function setState(State $state)
    {
        $this->state = $state;
    }

    public function proceedToNext()
    {
        $this->state->proceedToNext($this);
    }

    /**
     * @return string
     */
    public function toString():string {
        return $this->state->toString();
    }

    /**
     * @return SubscriptionUser
     */
    public function getSubscriptionUser():SubscriptionUser {
        return $this->subscriptionUser;
    }

    /**
     * @param SubscriptionUser $subscriptionUser
     */
    public function setSubscriptionUser(SubscriptionUser $subscriptionUser):void {
        $this->subscriptionUser = $subscriptionUser;
    }


}
