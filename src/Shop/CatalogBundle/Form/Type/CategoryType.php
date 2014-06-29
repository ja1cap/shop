<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CategoryType
 * @package Shop\CatalogBundle\Form\Type
 */
class CategoryType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('singularName', 'text', array(
                'required' => true,
                'label' => 'Название товара в единстенном числе',
            ))
            ->add('multipleName', 'text', array(
                'required' => true,
                'label' => 'Название товара в множественном числе',
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'default',
                'label' => 'Изображение',
                'required' => false,
            ))
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => Category::$statuses,
                'label' => 'Статус',
            ))
            ->add('additionalCategories', 'entity', array(
                'required' => false,
                'class' => 'ShopCatalogBundle:Category',
                'multiple' => true,
                'attr' => array(
                    'data-placeholder' => 'Выберите'
                ),
                'label' => 'Категории дополнительных товаров',
                'query_builder' => function(EntityRepository $er) {

                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');

                    },
            ))
        ;

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
        ;

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'shop_catalog_category';
    }

} 