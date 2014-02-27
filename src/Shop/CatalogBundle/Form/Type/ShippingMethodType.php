<?php
namespace Shop\CatalogBundle\Form\Type;

use Shop\CatalogBundle\Entity\ShippingMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ManufacturerType
 * @package Shop\CatalogBundle\Form\Type
 */
class ShippingMethodType extends AbstractType {

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
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => ShippingMethod::$statuses,
                'label' => 'Статус',
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'Описание',
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
        return 'shipping_method';
    }

} 