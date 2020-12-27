<?php

namespace App\Entity;

use App\Model\User\Email;
use App\Model\User\Role;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    private const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    use ModifyEntityTrait;

    /**
     * @ORM\Column(type="identifier")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var Email|null
     * @ORM\Column(type="user_user_email", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=SubscriptionUser::class, mappedBy="user")
     */
    private $subscriptions;

    /**
     * @var Role
     * @ORM\Column(type="user_user_role", length=16)
     */
    private $role;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|SubscriptionUser[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(SubscriptionUser $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setUser($this);
        }

        return $this;
    }

    public function removeSubscription(SubscriptionUser $subscription): self
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getUser() === $this) {
                $subscription->setUser(null);
            }
        }

        return $this;
    }
}
