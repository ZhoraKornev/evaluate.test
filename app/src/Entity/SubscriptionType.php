<?php

namespace App\Entity;

use App\Model\Id;
use App\Repository\SubscriptionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table (name="subscription_types")
 * @ORM\Entity(repositoryClass=SubscriptionTypeRepository::class,readOnly=true)
 * @ORM\HasLifecycleCallbacks()
 */
class SubscriptionType
{
    use ModifyEntityTrait;

    /**
     * @var Id
     * @ORM\Column(type="identifier")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private string $name;

    /**
     * @ORM\Column(type="integer",options={"comment":"Present data in coins of current currency"})
     */
    private int $price;

    /**
     * @ORM\Column(type="integer",options={"comment":"Present data in days"})
     */
    private int $period;

    /**
     * @ORM\ManyToMany(targetEntity=Content::class)
     */
    private $contents;

    /**
     * SubscriptionType constructor.
     *
     * @param Id     $id
     * @param string $name
     * @param int    $price
     * @param int    $period
     */
    public function __construct(Id $id, string $name, int $price, int $period)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->period = $period;
        $this->contents = new ArrayCollection();
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPeriod(): ?int
    {
        return $this->period;
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @return Collection|Content[]
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(Content $content): self
    {
        if (!$this->contents->contains($content)) {
            $this->contents[] = $content;
        }

        return $this;
    }

    public function removeContent(Content $content): self
    {
        $this->contents->removeElement($content);

        return $this;
    }
}
