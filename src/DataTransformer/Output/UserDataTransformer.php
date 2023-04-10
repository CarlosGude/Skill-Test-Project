<?php

namespace App\DataTransformer\Output;

use App\Dto\Output\OutputInterface;
use App\Dto\Output\UserDto;
use App\Entity\User;

class UserDataTransformer extends AbstractDataTransformer
{
    protected function getOutputDto(): OutputInterface
    {
        return new UserDto();
    }

    protected function getClass(): string
    {
        return User::class;
    }
}
