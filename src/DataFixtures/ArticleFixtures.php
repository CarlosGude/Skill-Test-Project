<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use App\Services\StringToSlugService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 *
 */
class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        for ($i=0; random_int(10, 20) >= $i;$i++) {
            $article = new Article();
            $article->setTitle('Title article '.$i);
            $article->setSlug(StringToSlugService::transformation($article->getFieldToSlug()));
            $article->setBody('body article '.$i);
            $article->setUser($users[array_rand($users)]);

            $manager->persist($article);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }


}
