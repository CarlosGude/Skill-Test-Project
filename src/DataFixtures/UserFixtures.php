<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        foreach (self::getUsers() as $userData){
            $user = new User();
            $user->setName($userData['name']);
            $user->setEmail($userData['email']);
            $user->setPassword($userData['password']);
            $user->setRoles($userData['roles']);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getUsers(): array
    {
        return [
            'admin' => ['name' => 'admin','email' => 'admin@email.test', 'password' => 'password1admin','roles'=> [User::ROLE_ADMIN]],
            'anotherUser' =>['name' => 'Test','email' => 'test@email.test', 'password' => 'password1','roles'=> [User::ROLE_USER]],
        ];
    }
}