<?php


namespace App\DataTransformer\Output;


use App\DataTransformer\AbstractDataTransformer;
use App\Dto\Output\UserOutputDto;
use App\Entity\User;

class UserDataTransformerOutput extends AbstractDataTransformer
{

    protected function getOutputDto()
    {
        return new UserOutputDto();
    }

    protected function getClass()
    {
        return User::class;
    }
}