<?php

namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Dto\Input\UserDto;
use App\Entity\User;

class UserDataTransformer extends AbstractDataTransformer
{
    protected function getClass(): string
    {
        return User::class;
    }

    protected function getInputDto(array $data): InputInterface
    {
        return new UserDto($data);
    }
}
