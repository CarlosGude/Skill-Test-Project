<?php


namespace App\Dto\Input;


use App\Entity\User;

class UserInputDto
{
    public string $email;

    public string $password;

    public function dtoToEntity(?User $user): User
    {
        if(!$user){
            $user = new User();
        }

        $user->setEmail($this->email);
        $user->setPassword($this->password);

        return $user;
    }
}