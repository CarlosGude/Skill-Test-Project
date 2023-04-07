<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(protected TranslatorInterface $translator)
    {

    }

    public static function getEntityFqcn(): string
    {
        return User::class;
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
        /** @var User $user */
        $user = $context->getEntity()->getInstance();

        $user->setDeletedAt();
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel($this->translator->trans('id'))->onlyOnIndex(),
            IdField::new('uuid')->setLabel($this->translator->trans('uuid'))->hideOnIndex()->setFormTypeOption('disabled','disabled'),
            IdField::new('name')->setLabel($this->translator->trans('user.name')),
            IdField::new('email')->setLabel($this->translator->trans('user.email')),
            IdField::new('password')->setLabel($this->translator->trans('user.password'))->hideOnIndex()->hideWhenUpdating(),
        ];
    }
}
