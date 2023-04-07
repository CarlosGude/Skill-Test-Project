<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\DeletedAtFilter;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleCrudController extends AbstractCrudController
{
    public function __construct(protected TranslatorInterface $translator)
    {

    }

    public function configureActions(Actions $actions): Actions
    {
        // this action executes the 'renderInvoice()' method of the current CRUD controller
        $delete = Action::new('softDeleted', $this->translator->trans('deleted'), 'fa fa-tars')
            ->linkToCrudAction('softDeleted');

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $delete);
    }

    public function softDeleted(AdminContext $context)
    {
        /** @var Article $article */
        $article = $context->getEntity()->getInstance();

        $article->setDeletedAt();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb->andWhere('entity.deletedAt is null');

        if (!in_array(User::ROLE_ADMIN, $this->getUser()->getRoles())) {
            $qb->andWhere('entity.user = :user');
            $qb->setParameter('user', $this->getUser());
        }

        return $qb;
    }
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title',$this->translator->trans('article.title')))
            ->add(DeletedAtFilter::new('deletedAt',$this->translator->trans('article.showDeleted')))
            ;
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('uuid')->setLabel($this->translator->trans('uuid'))->hideOnIndex()->setFormTypeOption('disabled','disabled'),
            TextField::new('title')->setLabel($this->translator->trans('article.title')),
            TextEditorField::new('body')->hideOnIndex()->setLabel($this->translator->trans('article.body')),
            DateTimeField::new('createdAt')->hideOnForm()->setLabel($this->translator->trans('createdAt')),
            DateTimeField::new('updatedAt')->hideOnForm()->setLabel($this->translator->trans('updated')),
            AssociationField::new('user')->hideOnForm()->setLabel($this->translator->trans('article.author')),
        ];
    }
}
