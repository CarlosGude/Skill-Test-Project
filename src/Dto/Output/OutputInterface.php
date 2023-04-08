<?php

namespace App\Dto\Output;

use App\Entity\AbstractEntity;

interface OutputInterface
{
    public function get(AbstractEntity $entity): self;

}
