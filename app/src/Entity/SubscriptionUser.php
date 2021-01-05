<?php

namespace App\Entity;

use App\Model\Id;
use App\Repository\SubscriptionUserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Table (name="subscription_users")
 * @ORM\Entity(repositoryClass=SubscriptionUserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class SubscriptionUser
{
    use ModifyEntityTrait;

    /**
     * @var Id
     * @ORM\Column(type="identifier")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $activateAt;

    //при оформлении подписки через систему заказов переделать свойство под модель status и уже через StateMachine переводить из статуса в статус
    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $active;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=SubscriptionType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private SubscriptionType $subscription;

    /**
     * @ORM\Column(type="datetime", nullable=true,name="valid_due")
     */
    private ?DateTimeInterface $validDue;

    /**
     * SubscriptionUser constructor.
     *
     * @param                  $id
     * @param SubscriptionType $subscriptionType
     * @param User             $user
     */
    public function __construct($id, SubscriptionType $subscriptionType, User $user) {
        $this->id = $id;
        $this->subscription = $subscriptionType;
        $this->user = $user;
        $this->active = false;
    }
    /**
     * @return Id
     */
    public function getId():Id {
        return $this->id;
    }


    /**
     * @return string
     */
    #[Pure] public function getIdAsString():string {
        return $this->id->getValue();
    }



    public function getActivateAt(): ?DateTimeInterface
    {
        return $this->activateAt;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSubscription(): ?SubscriptionType
    {
        return $this->subscription;
    }

    public function getValidDue(): ?DateTimeInterface
    {
        return $this->validDue;
    }

    public function setValidDue(?DateTimeInterface $validDue): self
    {
        $this->validDue = $validDue;

        return $this;
    }
    public function deactivate() {
        $this->validDue = new DateTime();
        $this->active = false;
    }
    /**
     * @param DateTimeInterface|null $activateAt
     */
    public function setActivateAt(?DateTimeInterface $activateAt):void {
        $this->activateAt = $activateAt;
    }

}
