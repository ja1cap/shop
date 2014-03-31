<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CreatePriceListType
 * @package Shop\CatalogBundle\Form\Type
 */
class CreatePriceListType extends AbstractType {

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
        ;

        $builder->add('category', 'entity', array(
            'required' => true,
            'empty_value' => 'Выберите категорию',
            'class' => 'ShopCatalogBundle:Category',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            'label' => 'Категория',
        ));

        $builder->add('manufacturers', 'entity', array(
            'required' => false,
            'multiple' => true,
            'attr' => array(
                'data-placeholder' => 'Выберите производителей'
            ),
            'class' => 'ShopCatalogBundle:Manufacturer',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name', 'ASC');
                },
            'label' => 'Производители',
        ));

        $builder->add('contractors', 'entity', array(
            'required' => false,
            'multiple' => true,
            'attr' => array(
                'data-placeholder' => 'Выберите контрагентов'
            ),
            'class' => 'ShopCatalogBundle:Contractor',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            'label' => 'Контрагенты',
        ));

        $builder
            ->add('save', 'submit', array(
                'label' => 'Создать',
            ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'create_price_list';
    }

} 