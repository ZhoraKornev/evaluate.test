<?php
declare(strict_types = 1);

namespace App\Tests\unit\Service;

use App\DTO\FondyPaymentDTO;
use App\DTO\NewSubscriptionRequestDTO;
use App\Entity\SubscriptionType;
use App\Entity\SubscriptionUser;
use App\Entity\User;
use App\Model\Order\Order;
use App\Repository\SubscriptionTypeRepository;
use App\Repository\SubscriptionUserRepository;
use App\Repository\UserRepository;
use App\Service\Factory\StateMachineFactory;
use App\Service\Factory\UserSubscriptionsCreator;
use App\Service\PaymentCreatorInterface;
use App\Service\PaymentSender;
use App\Service\SubscriptionUserStatusMachine;
use App\Service\UserSubscriptionPaymentService;
use App\Service\UserUnsubscribeService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\MockObject\MockObject;
use LogicException;
use Codeception\Test\Unit;
use Psr\Log\LoggerInterface;

class UserSubscriptionPaymentServiceTest  extends Unit
{
    /**
     * @var SubscriptionUserRepository|MockObject
     */
    private MockObject|SubscriptionUserRepository $subscriptionUserRepositoryMock;
    /**
     * @var EntityManagerInterface|MockObject
     */
    private EntityManagerInterface|MockObject $entityManagerInterfaceMock;
    /**
     * @var SubscriptionTypeRepository|MockObject
     */
    private SubscriptionTypeRepository|MockObject $subscriptionTypeRepositoryMock;
    /**
     * @var UserSubscriptionsCreator|MockObject
     */
    private UserSubscriptionsCreator|MockObject $userSubscriptionsCreatorMock;
    /**
     * @var UserRepository|MockObject
     */
    private MockObject|UserRepository $userRepositoryMock;
    /**
     * @var PaymentCreatorInterface|MockObject
     */
    private PaymentCreatorInterface|MockObject $paymentCreatorInterfaceMock;
    /**
     * @var PaymentSender|MockObject
     */
    private PaymentSender|MockObject $paymentSenderMock;
    /**
     * @var MockObject|LoggerInterface
     */
    private MockObject|LoggerInterface $loggerInterfaceMock;
    /**
     * @var StateMachineFactory|MockObject
     */
    private MockObject|StateMachineFactory $stateMachineFactoryMock;
    /**
     * @var UserUnsubscribeService|MockObject
     */
    private UserUnsubscribeService|MockObject $userUnsubscribeServiceMock;
    /**
     * @var UserSubscriptionPaymentService
     */
    private UserSubscriptionPaymentService $service;
    /**
     * @var FondyPaymentDTO
     */
    private FondyPaymentDTO $paymentDTO;

    protected function _before() {
        $this->subscriptionUserRepositoryMock = $this->createMock(SubscriptionUserRepository::class);
        $this->entityManagerInterfaceMock = $this->createMock(EntityManagerInterface::class);
        $this->subscriptionTypeRepositoryMock = $this->createMock(SubscriptionTypeRepository::class);
        $this->userSubscriptionsCreatorMock = $this->createMock(UserSubscriptionsCreator::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->paymentCreatorInterfaceMock = $this->createMock(PaymentCreatorInterface::class);
        $this->paymentSenderMock = $this->createMock(PaymentSender::class);
        $this->loggerInterfaceMock = $this->createMock(LoggerInterface::class);
        $this->stateMachineFactoryMock = $this->createMock(StateMachineFactory::class);
        $this->userUnsubscribeServiceMock = $this->createMock(UserUnsubscribeService::class);
        $this->paymentDTO = $this->initPaymentDTO();

        $this->service = new UserSubscriptionPaymentService(
            $this->subscriptionUserRepositoryMock,
            $this->entityManagerInterfaceMock,
            $this->subscriptionTypeRepositoryMock,
            $this->userSubscriptionsCreatorMock,
            $this->userRepositoryMock,
            $this->paymentCreatorInterfaceMock,
            $this->paymentSenderMock,
            $this->loggerInterfaceMock,
            $this->stateMachineFactoryMock,
            $this->userUnsubscribeServiceMock
        );

    }

    /**
     * @test
     * @throws EntityNotFoundException
     */
    public function testPayUserSubscriptionSuccess()
    {
        $subscriptionUserMock = $this->createMock(SubscriptionUser::class);
        $this->subscriptionUserRepositoryMock->expects($this->atLeastOnce())->method('findOneBy')->willReturn($subscriptionUserMock);
        $statusMachineMock = $this->createMock(SubscriptionUserStatusMachine::class);
        $statusMachineMock->expects($this->atLeastOnce())->method('proceedToNext');
        $this->stateMachineFactoryMock->expects($this->atLeastOnce())->method('resolveMachine')->willReturn($statusMachineMock);
        $userMock = $this->createMock(User::class);
        $this->userRepositoryMock->expects($this->atLeastOnce())->method('find')->willReturn($userMock);
        $subscriptionUserArrMock = [$subscriptionUserMock];
        $this->subscriptionUserRepositoryMock->expects($this->atLeastOnce())->method('findBy')->with(['user' => $userMock, 'active' => true])->willReturn($subscriptionUserArrMock);
        $this->entityManagerInterfaceMock->expects($this->atLeastOnce())->method('persist')->with($subscriptionUserMock);
        $this->userUnsubscribeServiceMock->expects($this->atLeastOnce())->method('unsubscribeUserWhenSubscriptionEnds')->with($subscriptionUserMock);
        $this->entityManagerInterfaceMock->expects($this->atLeastOnce())->method('flush');

        $this->assertTrue($this->service->payUserSubscription($this->paymentDTO));
    }

    /**
     * @test
     */
    public function testPayUserSubscriptionFailWithOutOrderStatus()
    {
        $this->paymentDTO->order_status = "";

        $this->assertFalse($this->service->payUserSubscription($this->paymentDTO));
    }



    public function testCreateOrderSuccess() {
        $dto = new NewSubscriptionRequestDTO();
        $dto->user = $this->createMock(User::class);
        $dto->subscription_id = '123';
        $this->subscriptionTypeRepositoryMock->expects($this->atLeastOnce())->method('count')->with(['id' => $dto->subscription_id])->willReturn(1);
        $this->subscriptionUserRepositoryMock->expects($this->atLeastOnce())->method('count')->with(
            [
                'user' => $dto->user,
                'subscription' => $dto->subscription_id,
                'active' => false,
                'activateAt' => null
            ]

        )->willReturn(0);
        $orderMock = $this->createMock(Order::class);
        $this->paymentCreatorInterfaceMock->expects($this->atLeastOnce())->method('create')->willReturn($orderMock);
        $this->paymentSenderMock->expects($this->atLeastOnce())->method('sendToPaymentGateway')->with($orderMock);
        $subscriptionUserMock = $this->createMock(SubscriptionUser::class);
        $subscriptionTypeMock = $this->createMock(SubscriptionType::class);
        $this->subscriptionTypeRepositoryMock->expects($this->atLeastOnce())->method('findOneBy')->with(['id' => $dto->subscription_id])->willReturn($subscriptionTypeMock);
        $this->userSubscriptionsCreatorMock->expects($this->atLeastOnce())->method('createForUser')->with($dto->user,$subscriptionTypeMock)->willReturn($subscriptionUserMock);
        $this->entityManagerInterfaceMock->expects($this->atLeastOnce())->method('persist')->with($subscriptionUserMock);
        $this->entityManagerInterfaceMock->expects($this->atLeastOnce())->method('flush');

        $this->service->createOrder($dto);
    }

    public function testCreateOrderFail() {
        $dto = new NewSubscriptionRequestDTO();
        $dto->user = $this->createMock(User::class);
        $dto->subscription_id = '123';
        $this->subscriptionTypeRepositoryMock->expects($this->atLeastOnce())->method('count')->with(['id' => $dto->subscription_id])->willReturn(1);
        $this->subscriptionUserRepositoryMock->expects($this->atLeastOnce())->method('count')->with(
            [
                'user' => $dto->user,
                'subscription' => $dto->subscription_id,
                'active' => false,
                'activateAt' => null
            ]

        )->willReturn(1);
        $this->expectException(LogicException::class);

        $this->service->createOrder($dto);
    }

    /**
     * @return FondyPaymentDTO
     */
    #[Pure] private function initPaymentDTO():FondyPaymentDTO {
        $paymentDTO = new FondyPaymentDTO();
        $paymentDTO->response_signature_string = '**********|3324000|RUB|3324000|027440|444455|VISA|RUB|444455XXXXXX6666|1396424|14#1500639628|approved|21.07.2017 15:20:27|51247263|card|success|0|429417347068|test@FONDY.eu|0|purchase';
        $paymentDTO->response_status = 'success';
        $paymentDTO->reversal_amount = '';
        $paymentDTO->settlement_amount = '3324000';
        $paymentDTO->actual_amount = '3324000';
        $paymentDTO->order_status = 'approved';
        $paymentDTO->response_description = '';
        $paymentDTO->currency = 'RUB';
        $paymentDTO->actual_currency = 'RUB';
        $paymentDTO->order_id = '829e9da4-e793-44f2-8fd2-0a31e61882f4|2c83017f-2e96-4704-b976-0d5fb89fb51c';
        $paymentDTO->signature = '47bdcaf61b99edd31e3ec7913225a14d2ce07575';
        $paymentDTO->amount = '3324000';
        $paymentDTO->sender_cell_phone = '';
        $paymentDTO->sender_email = 'test@FONDY.eu';
        $paymentDTO->merchant_data = '';
        $paymentDTO->verification_status = '';

        return $paymentDTO;

    }
}
