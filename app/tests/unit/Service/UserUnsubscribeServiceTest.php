<?php
declare(strict_types = 1);

namespace App\Tests\unit\Service;

use App\Entity\SubscriptionType;
use App\Entity\SubscriptionUser;
use App\Model\UnsubscribeUserMessage;
use App\Repository\SubscriptionUserRepository;
use App\Service\UserUnsubscribeService;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\MessageBusInterface;

class UserUnsubscribeServiceTest extends Unit
{

    /**
     * @var MockObject|MessageBusInterface
     */
    private MessageBusInterface|MockObject $messageBusMock;
    /**
     * @var SubscriptionUserRepository|MockObject
     */
    private MockObject|SubscriptionUserRepository $usersSubscriptionsMock;
    /**
     * @var EntityManagerInterface|MockObject
     */
    private EntityManagerInterface|MockObject $entityManagerMock;
    /**
     * @var UserUnsubscribeService
     */
    private UserUnsubscribeService $service;

    protected function _before() {
        $this->messageBusMock = $this->createMock(MessageBusInterface::class);
        $this->usersSubscriptionsMock = $this->createMock(SubscriptionUserRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $this->service = new UserUnsubscribeService(
            $this->messageBusMock,
            $this->usersSubscriptionsMock,
            $this->entityManagerMock
        );

    }
    /**
     * @test
     * @skip
     * @throws \Exception
     */
    public function testUnsubscribeUserWhenSubscriptionEnds(){
        $subscriptionTypeMock = $this->makeEmpty(SubscriptionType::class,['getPeriod' => 7,]);
        $newUserSubscriptionsMock = $this->makeEmpty(SubscriptionUser::class,['getSubscription' => $subscriptionTypeMock]);
        $this->messageBusMock->expects($this->atLeastOnce())->method('dispatch')->with($this->any());
        $this->service->unsubscribeUserWhenSubscriptionEnds($newUserSubscriptionsMock);
    }

    /**
     * @param string $contentId
     *
     * @throws \Exception
     * @dataProvider testDeactivateUserSubscriptionSuccessProvider
     */
    public function testDeactivateUserSubscriptionSuccess(string $contentId) {
        $unsubscribeUserMessageMock = $this->makeEmpty(UnsubscribeUserMessage::class,['getContent' => $contentId]);
        $subscriptionUserMock = $this->makeEmpty(SubscriptionUser::class);
        $subscriptionUserMock->expects($this->once())->method('setActive')->with(false);
        $this->entityManagerMock->expects($this->once())->method('persist');
        $this->entityManagerMock->expects($this->once())->method('flush');
        $this->usersSubscriptionsMock->expects($this->once())->method('find')->with($contentId)->willReturn($subscriptionUserMock);
        $this->service->deactivateUserSubscription($unsubscribeUserMessageMock);
    }

    /**
     * @return \string[][]
     */
    public function testDeactivateUserSubscriptionSuccessProvider():array {
        return [
            ['D6F640B4-84DB-4473-BFD3-BFB15A5C7FD7'],
            ['63A6F666-110D-4BD1-8B28-87F560238850'],
            ['23EE7176-6F61-49E0-BDF4-364AB3658C69'],
            ['1FE2FA18-8095-4C0D-AEB2-6C4196ABE56D'],
            ['67EBC4C5-6FEB-48C3-A46C-A7EBB3257DBD'],
            ['C1C69AC5-8F42-4677-871E-C69B7DA0602D'],
            ['93510AB2-1F82-4D0E-80BE-F08FCFE68199'],
            ['8E40FD6D-3C46-477D-8F76-B908C543BDDE'],
            ['7B9A4A7B-61E1-48E7-A3F0-54D192ED40E2'],
        ];
    }
}
