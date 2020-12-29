<?php

namespace App\Entity;

use App\Repository\SubscriptionUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="subscriptions_users")
 * @ORM\Entity(repositoryClass=SubscriptionUserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class SubscriptionUser
{
    use ModifyEntityTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $activateAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity=Content::class)
     */
    private $subscriptionContent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="subscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivateAt(): ?\DateTimeInterface
    {
        return $this->activateAt;
    }

    public function setActivateAt(?\DateTimeInterface $activateAt): self
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

    public function setSubscriptionContent(?Content $subscriptionContent): self
    {
        $this->subscriptionContent = $subscriptionContent;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
