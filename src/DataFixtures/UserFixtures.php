<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Services\StringToSlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (self::getUsers() as $userData) {
            $user = new User();
            $user->setName($userData['name']);
            $user->setEmail($userData['email']);
            $user->setSlug(StringToSlugService::transformation($user->getFieldToSlug()));
            $user->setPassword($userData['password']);
            $user->setRoles($userData['roles']);
            $user->setActive($userData['active']);

            if(array_key_exists('deleted', $userData) && $userData['deleted']) {
                $user->setDeletedAt();
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getUsers(): array
    {
        return [
            'admin' => ['name' => 'admin','email' => 'admin@email.test', 'password' => 'password1admin','roles'=> [User::ROLE_ADMIN],'active' => true],
            'anotherUser' =>['name' => 'Test','email' => 'test@email.test', 'password' => 'password1','roles'=> [User::ROLE_USER],'active' => true],
            'deletedUser' =>['name' => 'deletedUser','email' => 'deletedUser@email.test', 'password' => 'deletedUser1','roles'=> [User::ROLE_USER],'active' => true,'deleted' =>true],
            'noActiveUser' =>['name' => 'noActiveUser','email' => 'noActiveUser@email.test', 'password' => 'noActiveUser1','roles'=> [User::ROLE_USER],'active' => false],
        ];
    }
}
