<?php

namespace App\Service;

use App\DTO\FondyPaymentDTO;
use App\DTO\NewSubscriptionRequestDTO;
use App\DTO\NewUserSubscriptionDTO;
use App\Entity\SubscriptionUser;
use App\Entity\User;
use App\Repository\SubscriptionTypeRepository;
use App\Repository\SubscriptionUserRepository;
use App\Repository\UserRepository;
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
    private FondyPaymentCreator $paymentCreator;
    /**
     * @var PaymentSender
     */
    private PaymentSender $paymentSender;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

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
     */
    public function __construct(
        SubscriptionUserRepository $subscriptionUserRepository,
        EntityManagerInterface $entityManager,
        SubscriptionTypeRepository $subscriptionTypeRepository,
        UserSubscriptionsCreator $userSubscriptionsManager,
        UserRepository $userRepository,
        PaymentCreatorInterface $paymentCreator,
        PaymentSender $paymentSender,
        LoggerInterface $subscriptionLogger
    ) {
        $this->usersSubscriptions = $subscriptionUserRepository;
        $this->entityManager = $entityManager;
        $this->subscriptionPlans = $subscriptionTypeRepository;
        $this->userSubscriptionsManager = $userSubscriptionsManager;
        $this->users = $userRepository;
        $this->paymentCreator = $paymentCreator;
        $this->paymentSender = $paymentSender;
        $this->logger = $subscriptionLogger;
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

    public function payUserSubscription(FondyPaymentDTO $paymentDTO) {
        //TODO validate order data
        if (!$paymentDTO->order_status){
            return false;
        }
        $newSubscription = $this->getNewUserSubscriptionByOrderId($paymentDTO->order_id);
        if ($this->usersSubscriptions->count(['user' => $newSubscription->user, 'active' => true])){
            $this->deactivateAllUsersPlans($newSubscription->user);
        }
        $newUserSubscription = $this->userSubscriptionsManager->createForUser(
            $newSubscription->user,
            $newSubscription->subscription
        );
        $this->entityManager->persist($newUserSubscription);
        $this->entityManager->flush();

        return true;
    }

    private function getNewUserSubscriptionByOrderId(string $orderId) {
        //TODO resolve order id user_id|subscription_id and get correct data
        $newUserSubscription = new NewUserSubscriptionDTO();
        $newUserSubscription->user = current($this->users->findAll());
        $newUserSubscription->subscription = current($this->subscriptionPlans->findAll());

        return $newUserSubscription;
    }

    private function deactivateAllUsersPlans(User $user){
        array_map(function (SubscriptionUser $subscriptionUser){
            $subscriptionUser->deactivate();
            $this->entityManager->persist($subscriptionUser);
        },$this->usersSubscriptions->findBy(['user' => $user]));
    }
}
