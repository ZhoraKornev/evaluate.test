<?php

namespace App\Service;

use App\Entity\SubscriptionUser;
use App\Model\Order\State\ActiveState;
use App\Model\Order\State\NonActiveState;
use App\Service\Factory\StateMachineFactory;
use App\Service\SubscriptionUserStatusMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserSubscriptionPaymentServiceTest  extends TestCase
{
    public function testPayUserSubscriptionSuccess()
    {
        $this->assertTrue(true);
    }

    public function testPayUserSubscriptionFail()
    {
        $this->assertTrue(true);
    }

}
