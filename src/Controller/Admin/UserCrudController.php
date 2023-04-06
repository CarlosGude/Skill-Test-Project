<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * @param string $pageName
     * @return iterable
     * TODO: AÑANDIR TRADUCCIONES
     * TODO: NO MOSTRAR SI NO ES ADMIN. Si no lo es, mostrar link para editar su usuario.
     * TODO: Cambiar password.
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            IdField::new('uuid')->hideOnIndex()->setFormTypeOption('disabled','disabled'),
            IdField::new('name'),
            IdField::new('email'),
            IdField::new('password')->hideOnIndex()->hideWhenUpdating(),
        ];
    }
}
