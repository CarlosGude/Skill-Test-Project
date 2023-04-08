<?php

namespace App\Dto\Input;

use App\Entity\AbstractEntity;

interface InputInterface
{
    public function __construct(array $data);
    public function post(): AbstractEntity;
    public function put(AbstractEntity $entity, array $data): AbstractEntity;
    public function initialized(AbstractEntity $entity): void;
}
