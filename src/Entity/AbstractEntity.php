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


}