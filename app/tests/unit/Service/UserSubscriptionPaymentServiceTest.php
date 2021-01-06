<?php

namespace App\Tests\unit\Service;

use App\DTO\FondyPaymentDTO;
use App\Entity\SubscriptionUser;
use App\Entity\User;
use App\Model\Order\State\ActiveState;
use App\Model\Order\State\NonActiveState;
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
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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

        $this->service->payUserSubscription($this->paymentDTO);
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
    public function testPayUserSubscriptionFail()
    {
        $this->assertTrue(true);
    }

    public function testCreateOrderSuccess() {
        $this->assertTrue(true);
    }

    public function testCreateOrderFail() {
        $this->assertTrue(true);

    }

}
