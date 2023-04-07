<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;


abstract class AbstractEntity
{

    #[ORM\Column]
    protected string $uuid;

    #[ORM\Column(nullable: true)]
    protected ?DateTime $deletedAt = null;

    #[ORM\Column(nullable: false)]
    protected DateTime $createdAt;

    #[ORM\Column(nullable: false)]
    protected DateTime $updatedAt ;

    public function __construct(){
        $this->uuid = Uuid::v4();
    }


    public function getUuid(): UuidV4|string
    {
        return $this->uuid;
    }

    /**
     * @param Uuid|UuidV4 $uuid
     * @return AbstractEntity
     */
    public function setUuid(UuidV4|Uuid $uuid): AbstractEntity
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @return AbstractEntity
     */
    public function setDeletedAt(): AbstractEntity
    {
        $this->deletedAt = new DateTime();
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
     * @param DateTime $createdAt
     * @return AbstractEntity
     */
    public function setCreatedAt(): AbstractEntity
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return AbstractEntity
     */
    public function setUpdatedAt(): AbstractEntity
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

}