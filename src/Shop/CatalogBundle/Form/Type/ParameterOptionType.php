<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ParameterOptionType
 * @package Shop\CatalogBundle\Form\Type
 */
class ParameterOptionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', 'text', array(
            'required' => true,
            'label' => 'Название',
        ));

        $builder->add('image', 'sonata_media_type', array(
            'provider' => 'sonata.media.provider.image',
            'context'  => 'parameter_option',
            'label' => 'Изображение',
            'required' => false,
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
        return 'parameter_option';
    }

} 