<?php

namespace App\Service;

use App\DTO\FondyPaymentDTO;
use App\DTO\NewSubscriptionRequestDTO;
use App\Entity\SubscriptionUser;
use App\Entity\User;
use App\Repository\SubscriptionTypeRepository;
use App\Repository\SubscriptionUserRepository;
use App\Repository\UserRepository;
use App\Service\Factory\StateMachineFactory;
use App\Service\Factory\UserSubscriptionsCreator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use LogicException;
use Psr\Log\LoggerInterface;

class UserSubscriptionPaymentService
{
    /**
     * @var SubscriptionUserRepository
     */
    private SubscriptionUserRepository $usersSubscriptions;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var SubscriptionTypeRepository
     */
    private SubscriptionTypeRepository $subscriptionPlans;
    /**
     * @var UserSubscriptionsCreator
     */
    private UserSubscriptionsCreator $userSubscriptionsManager;
    /**
     * @var UserRepository
     */
    private UserRepository $users;
    /**
     * @var FondyPaymentCreator
     */
    private PaymentCreatorInterface $paymentCreator;
    /**
     * @var PaymentSender
     */
    private PaymentSender $paymentSender;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var StateMachineFactory
     */
    private StateMachineFactory $stateFactory;
    /**
     * @var UserUnsubscribeService
     */
    private UserUnsubscribeService $delayedUnsubscribeService;

    /**
     * UserSubscriptionService constructor.
     *
     * @param SubscriptionUserRepository $subscriptionUserRepository
     * @param EntityManagerInterface     $entityManager
     * @param SubscriptionTypeRepository $subscriptionTypeRepository
     * @param UserSubscriptionsCreator   $userSubscriptionsManager
     * @param UserRepository             $userRepository
     * @param PaymentCreatorInterface    $paymentCreator
     * @param PaymentSender              $paymentSender
     * @param LoggerInterface            $subscriptionLogger
     * @param StateMachineFactory        $stateFactory
     * @param UserUnsubscribeService     $unsubscribeService
     */
    public function __construct(
        SubscriptionUserRepository $subscriptionUserRepository,
        EntityManagerInterface $entityManager,
        SubscriptionTypeRepository $subscriptionTypeRepository,
        UserSubscriptionsCreator $userSubscriptionsManager,
        UserRepository $userRepository,
        PaymentCreatorInterface $paymentCreator,
        PaymentSender $paymentSender,
        LoggerInterface $subscriptionLogger,
        StateMachineFactory $stateFactory,
        UserUnsubscribeService $unsubscribeService
    ) {
        $this->usersSubscriptions = $subscriptionUserRepository;
        $this->entityManager = $entityManager;
        $this->subscriptionPlans = $subscriptionTypeRepository;
        $this->userSubscriptionsManager = $userSubscriptionsManager;
        $this->users = $userRepository;
        $this->paymentCreator = $paymentCreator;
        $this->paymentSender = $paymentSender;
        $this->logger = $subscriptionLogger;
        $this->stateFactory = $stateFactory;
        $this->delayedUnsubscribeService = $unsubscribeService;
    }


    /**
     * @param NewSubscriptionRequestDTO $requestDTO
     *
     * @return string
     * @throws EntityNotFoundException
     */
    public function createOrder(NewSubscriptionRequestDTO $requestDTO):string
    {
        if (!$this->subscriptionPlans->count(['id' => $requestDTO->subscription_id])) {
            $this->logger->warning('USER TRY BUY non existing subscription', ['id' => $requestDTO->subscription_id]);
            throw new EntityNotFoundException('Subscription with this id not exists');
        }
        if ($this->usersSubscriptions->count([
            'user' => $requestDTO->user,
            'subscription' => $requestDTO->subscription_id,
            'active' => false,
            'activateAt' => null
        ])) {
            $this->logger->warning('USER try buy twice same order', ['id' => $requestDTO->subscription_id]);
            throw new LogicException('You have not payed subscription');
        }
        $order = $this->paymentCreator->create($requestDTO);
        $this->logger->info('SUBSCRIPTION LOG NEW ORDER', ['id' => $order->getOrderId()]);
        $this->paymentSender->sendToPaymentGateway($order);
        $newSubscriptionUser = $this->userSubscriptionsManager->createForUser(
            $requestDTO->user,
            $this->subscriptionPlans->findOneBy(['id' => $requestDTO->subscription_id])
        );
        $this->entityManager->persist($newSubscriptionUser);
        $this->entityManager->flush();

        return $order->getOrderId();
    }

    /**
     * @param FondyPaymentDTO $paymentDTO
     *
     * @return bool
     * @throws EntityNotFoundException
     */
    public function payUserSubscription(FondyPaymentDTO $paymentDTO):bool
    {
        if (!$paymentDTO->order_status){
            $this->logger->critical('SUBSCRIPTION LOG payment status data is empty');
            return false;
        }
        $subscription = $this->getUserSubscriptionByOrderId($paymentDTO->order_id);
        $statusMachine =  $this->stateFactory->resolveMachine($subscription);
        $statusMachine->proceedToNext();
        $this->entityManager->persist($subscription);

        $this->deactivateAllUsersPlans($this->users->find($this->resolveUserIdFromOrderId($paymentDTO->order_id)));
        $this->delayedUnsubscribeService->unsubscribeUserWhenSubscriptionEnds($subscription);
        $this->entityManager->flush();
        return true;
    }

    /**
     * @param string $orderId
     *
     * @return SubscriptionUser|null
     * @throws EntityNotFoundException
     */
    private function getUserSubscriptionByOrderId(string $orderId):?SubscriptionUser {

        if (!$userSubscription = $this->usersSubscriptions->findOneBy([
            'user' => $this->resolveUserIdFromOrderId($orderId),
            'subscription' => $this->resolveSubscriptionIdFromOrderId($orderId),
            'active' => false,
            'activateAt' => null
        ])) {
            $this->logger->warning('SUBSCRIPTION LOG RECEIVE payment for non existing order', ['order_id' => $orderId]);
            throw new EntityNotFoundException('Users does not have orders');
        }
        return $userSubscription;
    }

    private function deactivateAllUsersPlans(User $user){
        array_map(function (SubscriptionUser $subscriptionUser){
            $subscriptionUser->deactivate();
            $this->entityManager->persist($subscriptionUser);
        }, $this->usersSubscriptions->findBy(['user' => $user, 'active' => true]));
    }

    private function resolveUserIdFromOrderId(string $orderId) {
        //order_id = user_id|subscription_id
        $tmp = explode('|', $orderId);

        return $tmp[0];
    }
    private function resolveSubscriptionIdFromOrderId(string $orderId) {
        //order_id = user_id|subscription_id
        $tmp = explode('|', $orderId);

        return $tmp[1];
    }
}
