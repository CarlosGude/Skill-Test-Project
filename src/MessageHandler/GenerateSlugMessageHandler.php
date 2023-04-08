<?php


namespace App\MessageHandler;

use App\Email\AbstractEmail;
use App\Entity\Article;
use App\Entity\Interfaces\SlugInterface;
use App\Entity\User;
use App\Message\EntityEvent;
use App\Services\StringToSlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenerateSlugMessageHandler
{
    protected EntityEvent $message;

    public function __construct(protected EntityManagerInterface $manager){}

    public function __invoke(EntityEvent $message)
    {
        $this->message = $message;

        /** @var SlugInterface $slugEntity */
        $slugEntity = $this->manager->getRepository($message->class)->findOneBy(['uuid' => $message->uuid]);

        if(!$slugEntity instanceof SlugInterface){
            return;
        }

        $slug = StringToSlugService::transformation($slugEntity->getFieldToSlug());

        if(!$this->slugNeedAnUpdate($message,$slugEntity)){
            return;
        }

        //I love this line.
        !empty($existSlug = $this->existSlug($slug)) && $slug .='-'.count($existSlug);

        $slugEntity->setSlug($slug);

        $this->manager->flush();
    }

    protected function existSlug(string $slug): array
    {
        return $this->manager->createQueryBuilder()
            ->select(['entity'])
            ->from($this->message->getClass(),'entity')
            ->where('entity.slug like :slug')
            ->andWhere('entity.slug != :uuid')
            ->setParameter(':slug','%'.$slug.'%')
            ->setParameter(':uuid',$this->message->uuid)
            ->getQuery()->getResult();
    }

    protected function slugNeedAnUpdate(EntityEvent $message, SlugInterface $entity): bool
    {
        if($message->getEvent() === EntityEvent::EVENT_CREATE){
            return true;
        }

        return $message->getEvent() === EntityEvent::EVENT_UPDATE && $message->hasChangeField($entity->getFieldName());
    }
}