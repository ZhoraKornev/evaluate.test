<?php

namespace App\Entity;

use App\Model\Id;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Model\User\Email;
use App\Model\User\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Table(name="user_users")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="user_users", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"email"}),
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{
    private const STATUS_WAIT = 'wait';
    public const STATUS_ACTIVE = 'active';

    use ModifyEntityTrait;

    /**
     * @var Id
     * @ORM\Column(type="identifier")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @var Email
     * @ORM\Column(type="user_user_email")
     */
    private Email $email;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="password_hash")
     */
    private ?string $passwordHash;
    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    /**
     * @var Role
     * @ORM\Column(type="user_user_role", length=16)
     */
    private Role $role;

    /**
     * @ORM\OneToMany(targetEntity=SubscriptionUser::class, mappedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $subscriptions;

    public function __construct(Id $id, Email $email)
    {
        $this->id = $id;
        $this->email = $email;
        $this->status = User::STATUS_WAIT;
        $this->role = Role::user();
        $this->subscriptions = new ArrayCollection();
    }
    /**
     * @return Email
     */
    public function getEmail():Email {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[Pure] public function getUsername(): string
    {
        return (string) $this->email->getValue();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->role;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->passwordHash;
    }

    public function setPassword(string $password): self
    {
        $this->passwordHash = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function setActive() {
        $this->status = User::STATUS_ACTIVE;
    }
}
