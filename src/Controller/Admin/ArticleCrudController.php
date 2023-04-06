<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    /**
     * @param string $pageName
     * @return iterable
     * TODO AÑANDIR TRADUCCIONES. Si es rol ADMIN listar todo, sino los del usuario
     * TODO Borrar marca deteledAt
     * TODO Listar solo los deletedAt null, ¿Filtro para los deleted?
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            IdField::new('uuid')->hideOnIndex()->setFormTypeOption('disabled','disabled'),
            TextField::new('title'),
            TextEditorField::new('body'),
        ];
    }
}
