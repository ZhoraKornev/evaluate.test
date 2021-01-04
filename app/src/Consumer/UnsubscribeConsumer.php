<?php


namespace App\Consumer;


use App\Model\UnsubscribeUserMessage;
use App\Service\UserUnsubscribeService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UnsubscribeConsumer implements MessageHandlerInterface
{
    /**
     * @var UserUnsubscribeService
     */
    private UserUnsubscribeService $unsubscribeService;
    /**
     * UnsubscribeConsumer constructor.
     */
    public function __construct(UserUnsubscribeService $unsubscribeService) {
        $this->unsubscribeService = $unsubscribeService;
    }
    public function __invoke(UnsubscribeUserMessage $message)
    {
        $this->unsubscribeService->deactivateUserSubscription($message);
    }

}
