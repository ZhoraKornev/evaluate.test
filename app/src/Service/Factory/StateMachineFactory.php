<?php


namespace App\Service\Factory;

use App\Entity\SubscriptionUser;
use App\Model\Order\State\ActiveState;
use App\Model\Order\State\NonActiveState;
use App\Service\SubscriptionUserStatusMachine;

class StateMachineFactory
{
    /**
     * @param SubscriptionUser $type
     *
     * @return SubscriptionUserStatusMachine
     */
    public function resolveMachine(SubscriptionUser $type):SubscriptionUserStatusMachine {
        if ($type->getActive()) {
            $machine = new SubscriptionUserStatusMachine(new ActiveState());
            $machine->setSubscriptionUser($type);
            return $machine;
        }
        $machine = new SubscriptionUserStatusMachine(new NonActiveState());
        $machine->setSubscriptionUser($type);

        return $machine;
    }
}
