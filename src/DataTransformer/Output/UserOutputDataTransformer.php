<?php


namespace App\DataTransformer\Output;

use App\Dto\Output\UserOutputDto;
use App\Entity\User;

class UserOutputDataTransformer extends AbstractOutputDataTransformer
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