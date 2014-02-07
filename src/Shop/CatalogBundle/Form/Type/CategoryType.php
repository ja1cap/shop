<?php
namespace Shop\CatalogBundle\Form\Type;

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
            ));

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'category';
    }

} 