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
     * @dataProvider testActiveDataProvider
     *
     * @param mixed $randomValue
     */
    public function testActiveCreation(bool|int|string $randomValue): void {
        $subscriptionMock = $this->getMockBuilder(SubscriptionUser::class)->disableOriginalConstructor()->getMock();;
        $subscriptionMock->expects($this->atLeastOnce())->method('getActive')->willReturn($randomValue);
        $service = new StateMachineFactory();
        $newSubscriptionUserStatusMachine = $service->resolveMachine($subscriptionMock);
        $this->assertInstanceOf(SubscriptionUserStatusMachine::class, $newSubscriptionUserStatusMachine);
        $this->assertInstanceOf(ActiveState::class, $newSubscriptionUserStatusMachine->setState());
    }

    /**
     * @test
     * @dataProvider testNonActiveDataProvider
     *
     * @param mixed $randomValue
     */
    public function testNonActiveCreation(bool|int|string $randomValue): void {
        $subscriptionMock = $this->getMockBuilder(SubscriptionUser::class)->disableOriginalConstructor()->getMock();;
        $subscriptionMock->expects($this->atLeastOnce())->method('getActive')->willReturn($randomValue);
        $service = new StateMachineFactory();
        $newSubscriptionUserStatusMachine = $service->resolveMachine($subscriptionMock);
        $this->assertInstanceOf(SubscriptionUserStatusMachine::class, $newSubscriptionUserStatusMachine);
        $this->assertInstanceOf(NonActiveState::class, $newSubscriptionUserStatusMachine->setState());
    }

    public function testActiveDataProvider() {
        return [true, true, true, 1, 10, 'test'];
    }

    public function testNonActiveDataProvider() {
        return [false, false, false, 0, 00, 0];
    }
}
