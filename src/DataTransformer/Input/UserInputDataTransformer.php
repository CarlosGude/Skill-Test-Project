<?php


namespace App\DataTransformer\Input;

use App\Dto\Input\InputInterface;
use App\Dto\Input\UserInputDto;
use App\Entity\User;

class UserInputDataTransformer extends AbstractInputDataTransformer
{

    protected function getClass(): string
    {
        return User::class;
    }

    protected function getInputDto($data): InputInterface
    {
        return new UserInputDto($data);
    }
}