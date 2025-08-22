<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "<div class='label'>Nombre <spam class='text-danger'>*</spam></div>",
                'label_html' => true,
                'attr' => ['class' => 'form-control',],
                'row_attr' => ['class' => 'form-control',]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
                'attr' => ['class' => 'form-control',],
                'row_attr' => ['class' => 'form-control',]
            ])
            ->add('price', NumberType::class, [
                'label' => "<div class='label'>Precio <spam class='text-danger'>*</spam></div>",
                'label_html' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-control'],
                'constraints' => [new GreaterThan(value: 99, message: '{{ value }} debe ser mayor {{ compared_value }}')]
            ])
            ->add('images', FileType::class, ['label' => 'Imagenes', 'multiple' => true, 'attr' => ['accept' => 'image/*']])
            ->add('tags', CollectionType::class, [
                'label' => 'Etiquetas',
                'entry_type' => TagsType::class,
                'by_reference' => false,
                'entry_options' => ['label' => false,],
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => ['class' => 'tienda-attributes-field attributes_collection'],
            ])
            ->add('tags_btn_add', ButtonType::class, ['label' => 'Nuevo', 'attr' => ['class' => 'tienda-attributes-btn-add btn btn-link', 'target' => 'attributes_collection']])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.id', 'ASC');
                },
                'choice_label' => function (Category $category): string {
                    return "(" . $category->getId() . ")" . $category->getName() . " - " . $category->getDescription();
                },
                'label' => "<div class='label'>Categoria <spam class='text-danger'>*</spam></div>",
                'label_html' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
