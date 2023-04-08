<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

abstract class AbstractEntity
{
    #[ORM\Column]
    protected string $uuid;

    #[ORM\Column(nullable: true)]
    protected ?\DateTime $deletedAt = null;

    #[ORM\Column(nullable: false)]
    protected \DateTime $createdAt;

    #[ORM\Column(nullable: false)]
    protected \DateTime $updatedAt;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getUuid(): UuidV4|string
    {
        return $this->uuid;
    }

    public function setUuid(UuidV4|Uuid $uuid): AbstractEntity
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(): AbstractEntity
    {
        $this->deletedAt = new \DateTime();

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): AbstractEntity
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): AbstractEntity
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }
}
