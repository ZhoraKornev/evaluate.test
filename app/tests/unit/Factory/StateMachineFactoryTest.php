<?php

declare(strict_types = 1);

namespace App\Tests\unit\Factory;

use App\Entity\SubscriptionUser;
use App\Service\Factory\StateMachineFactory;
use App\Service\SubscriptionUserStatusMachine;
use Codeception\Test\Unit;

class StateMachineFactoryTest extends Unit
{
    /**
     * @test
     *
     * @param mixed $randomValue
     */
    public function testActiveCreation():void {
        $service = new StateMachineFactory();
        $subscriptionUSerEntityMock = $this->createMock(SubscriptionUser::class);
        $subscriptionUSerEntityMock->expects($this->once())->method('getActive')->will($this->returnValue(true));

        $newSubscriptionUserStatusMachine = $service->resolveMachine($subscriptionUSerEntityMock);
        $this->assertInstanceOf(SubscriptionUserStatusMachine::class, $newSubscriptionUserStatusMachine);
        $this->assertEquals('active', $newSubscriptionUserStatusMachine->toString());
    }

    /**
     * @test
     *
     */
    public function testNonActiveCreation():void {
        $service = new StateMachineFactory();
        $subscriptionUSerEntityMock = $this->createMock(SubscriptionUser::class);
        $subscriptionUSerEntityMock->expects($this->once())->method('getActive')->will($this->returnValue(false));

        $newSubscriptionUserStatusMachine = $service->resolveMachine($subscriptionUSerEntityMock);
        $this->assertInstanceOf(SubscriptionUserStatusMachine::class, $newSubscriptionUserStatusMachine);
        $this->assertEquals('non_active', $newSubscriptionUserStatusMachine->toString());
    }
}
