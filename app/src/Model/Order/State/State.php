<?php

namespace App\Model\Order\State;

use App\Exception\IllegalStateTransitionException;
use App\Service\SubscriptionUserStatusMachine;

abstract class State implements StateInterface
{
    private const UNDEFINED_STATE = 'undefined';

    /**
     * @var SubscriptionUserStatusMachine
     */
    protected SubscriptionUserStatusMachine $context;

    public function setContext(SubscriptionUserStatusMachine $context) {
        $this->context = $context;
    }

    /**
     * @param SubscriptionUserStatusMachine $context
     *
     * @throws IllegalStateTransitionException
     */
    public function proceedToNext(SubscriptionUserStatusMachine $context) {
        if (!$this->validateProceedToNext()){
            throw new IllegalStateTransitionException();
        }
        $this->proceedToNext($context);
    }

    /**
     * @return bool
     */
    public function validateProceedToNext():bool {
        return true;
    }

    public function toString():string {
        return State::UNDEFINED_STATE;
    }
}
