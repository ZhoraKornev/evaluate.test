<?php

namespace App\Entity;

use App\Repository\SubscriptionUserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="subscription_users")
 * @ORM\Entity(repositoryClass=SubscriptionUserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class SubscriptionUser
{
    use ModifyEntityTrait;

    /**
     * @ORM\Column(type="identifier")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $activateAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $active;

    /**
     * @ORM\ManyToOne(targetEntity=Content::class)
     */
    private Content $subscriptionContent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    public function getActivateAt(): ?DateTimeInterface
    {
        return $this->activateAt;
    }

    public function setActivateAt(?DateTimeInterface $activateAt): self
    {
        $this->activateAt = $activateAt;

        return $this;
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

    public function getSubscriptionContent(): ?Content
    {
        return $this->subscriptionContent;
    }

    public function setSubscriptionContent(Content $subscriptionContent): self
    {
        $this->subscriptionContent = $subscriptionContent;

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
}
