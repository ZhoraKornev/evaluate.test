<?php
declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Entity\SubscriptionUser;
use App\Model\Order\State\ActiveState;
use App\Model\Order\State\NonActiveState;
use App\Service\Factory\StateMachineFactory;
use App\Service\SubscriptionUserStatusMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StateMachineFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider testSuccessDataProvider
     *
     * @param mixed $randomValue
     */
    public function testSuccessCreation(bool|int|string $randomValue): void {
        $subscriptionMock = $this->getMockBuilder(SubscriptionUser::class)->disableOriginalConstructor()->getMock();;
        $subscriptionMock->expects($this->atLeastOnce())->method('getActive')->willReturn($randomValue);
        $service = new StateMachineFactory();
        $newSubscriptionUserStatusMachine = $service->resolveMachine($subscriptionMock);
        $this->assertInstanceOf(SubscriptionUserStatusMachine::class, $newSubscriptionUserStatusMachine);

//        if ($type->getActive()) {
//            $machine = new SubscriptionUserStatusMachine(new ActiveState());
//            $machine->setSubscriptionUser($type);
//            return $machine;
//        }
//        $machine = new SubscriptionUserStatusMachine(new NonActiveState());
//        $machine->setSubscriptionUser($type);
//
//        return $machine;

        $this->assertTrue(true);
    }

    public function testSuccessDataProvider() {
        return [true, false, true, 1, 0, 10, 'test'];
    }
}
