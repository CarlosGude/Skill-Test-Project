<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 *
 * TODO: Refactorizar esto. Dividirlo en varias clases
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user0 = new User();
        $user0->setName('Carlos Gude');
        $user0->setEmail('carlos@gmail.com');
        $user0->setPassword('carlos@gmail.com');
        $manager->persist($user0);

        $user1 = new User();
        $user1->setName('Another User');
        $user1->setEmail('another@gmail.com');
        $user1->setPassword('testpassword');
        $manager->persist($user1);

        for ($i=0; random_int(10,20) >= $i;$i++){
            $article = new Article();
            $article->setTitle('Title article '.$i);
            $article->setBody('body article '.$i);
            $article->setUser(rand(0,1)? $user0 : $user1);

            $manager->persist($article);
        }

        $manager->flush();
    }


}
