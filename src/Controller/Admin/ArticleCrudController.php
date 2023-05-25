<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use \DateTimeImmutable;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
        ;
    }

    public function createEntity(string $entityFqcn)
    {
        $article = new Article();
        $article->setAuthor($this->getUser());
        $article->setCreatedAt(new DateTimeImmutable());

        return $article;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')->setLabel('Titre'),
            TextField::new('description')->setLabel('Description'),
            TextEditorField::new('content')->setLabel('Contenu'),
            AssociationField::new('category')->setLabel('Catégories'),
            ImageField::new('image')->setUploadDir('public/image_directory')->setBasePath('image_directory'),
            DateTimeField::new("createdAt")->setLabel('Créé le')
        ];
    }
}