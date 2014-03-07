<?php
namespace Shop\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ImageType
 * @package Shop\MainBundle\Form\Type
 */
class ImageType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('thumbImage', 'file', array(
                'required' => false,
                'label' => 'Маленькая картинка',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Большая картинка',
            ))
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
        return 'image';
    }

} 