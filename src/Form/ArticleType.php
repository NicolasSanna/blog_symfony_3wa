<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control', 'maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ ne doit pas être vide',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 255,
                        'minMessage' => 'le champ doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le champ doit contenir moins de  {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control', 'maxlength' => 255],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le champ ne doit pas être vide',
                    ]),
                    new Length([
                        'min' => 6,
                        'max' => 255,
                        'minMessage' => 'le champ doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le champ doit contenir moins de  {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'attr' => ['accept' => 'image/*', 'class' => 'form-control'],
                'data_class' => null
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'label',
                'label' => 'Catégorie'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
