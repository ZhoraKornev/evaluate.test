<?php
declare(strict_types = 1);

namespace App\Tests\unit\Service;
use App\Entity\SubscriptionUser;
use App\Model\Order\State\ActiveState;
use App\Model\Order\State\NonActiveState;
use App\Service\Factory\StateMachineFactory;
use App\Service\SubscriptionUserStatusMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Codeception\Test\Unit;


class UserSubscriptionServiceTest extends Unit
{

}
