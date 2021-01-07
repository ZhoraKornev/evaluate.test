<?php


namespace App\Service;

use App\Entity\SubscriptionUser;
use App\Model\UnsubscribeUserMessage;
use App\Repository\SubscriptionUserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class UserUnsubscribeService
{
    private const TIME_MULTIPLIER = 1000;
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $producer;
    /**
     * @var SubscriptionUserRepository
     */
    private SubscriptionUserRepository $usersSubscriptions;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;


    /**
     * UserUnsubscribeService constructor.
     *
     * @param MessageBusInterface        $bus
     * @param SubscriptionUserRepository $subscriptionUserRepository
     * @param EntityManagerInterface     $entityManager
     */
    public function __construct(
        MessageBusInterface $bus,
        SubscriptionUserRepository $subscriptionUserRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->producer = $bus;
        $this->usersSubscriptions = $subscriptionUserRepository;
        $this->entityManager = $entityManager;
    }

    public function unsubscribeUserWhenSubscriptionEnds(SubscriptionUser $newSubscriptionUser):void {
        $period = (new DateTime())->modify("+ {$newSubscriptionUser->getSubscription()?->getPeriod()} days");
        $this->producer->dispatch(new UnsubscribeUserMessage($newSubscriptionUser->getIdAsString()), [
            new DelayStamp($period->getTimestamp() - time())
        ]);
    }

    public function deactivateUserSubscription(UnsubscribeUserMessage $unsubscribeUserMessage):void {
        $userSubscription = $this->usersSubscriptions->find($unsubscribeUserMessage->getContent());
        if (!$userSubscription){
            return;
        }
        $userSubscription->setActive(false);
        $this->entityManager->persist($userSubscription);
        $this->entityManager->flush();
    }
}
