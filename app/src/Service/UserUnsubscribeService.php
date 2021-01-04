<?php


namespace App\Service;

use App\Entity\SubscriptionUser;
use App\Model\UnsubscribeUserMessage;
use App\Repository\SubscriptionUserRepository;
use DateTime;
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
     * UserUnsubscribeService constructor.
     *
     * @param MessageBusInterface        $bus
     * @param SubscriptionUserRepository $subscriptionUserRepository
     */
    public function __construct(
        MessageBusInterface $bus,
        SubscriptionUserRepository $subscriptionUserRepository) {
        $this->producer = $bus;
        $this->usersSubscriptions = $subscriptionUserRepository;
    }

    public function unsubscribeUserWhenSubscriptionEnds(SubscriptionUser $newSubscriptionUser) {
        $period = (new DateTime())->modify("+ {$newSubscriptionUser->getSubscription()->getPeriod()} days");
        $this->producer->dispatch(new UnsubscribeUserMessage($newSubscriptionUser->getIdAsString()), [
            new DelayStamp($period->getTimestamp() - time())
        ]);
    }

    public function deactivateUserSubscription(UnsubscribeUserMessage $unsubscribeUserMessage) {
        $userSubscription = $this->usersSubscriptions->find($unsubscribeUserMessage->getContent());
        $userSubscription->setActive(false);
    }
}
