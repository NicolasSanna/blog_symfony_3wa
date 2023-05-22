<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('label', TextType::class, [
            'label' => 'Label',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
