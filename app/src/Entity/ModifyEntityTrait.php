<?php
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Extend entity to create data and modify data field.
 */
trait ModifyEntityTrait
{
    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, name="created_at")
     */
    protected $createdAt;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, name="updated_at")
     */
    protected $updatedAt = null;

    /**
     * Set created date.
     *
     * @ORM\PrePersist()
     */
    public function setCreatedAtValue()
    {
        if (!$this->createdAt) {
            $this->createdAt = new DateTime();
        }
    }

    /**
     * Set updated date.
     *
     * @ORM\PreUpdate()
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new DateTime();
    }

    /**
     * @param DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
}
