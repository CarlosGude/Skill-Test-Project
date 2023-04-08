<?php

namespace App\DataTransformer\Output;

use App\Dto\Output\OutputInterface;
use App\Dto\Output\UserOutputDto;
use App\Entity\User;

class UserOutputDataTransformer extends AbstractOutputDataTransformer
{
    protected function getOutputDto(): OutputInterface
    {
        return new UserOutputDto();
    }

    protected function getClass(): string
    {
        return User::class;
    }
}
