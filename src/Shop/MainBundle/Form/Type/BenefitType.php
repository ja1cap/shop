<?php
namespace Shop\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BenefitType
 * @package Shop\MainBundle\Form\Type
 */
class BenefitType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'image',
                'label' => 'Картинка',
                'required' => false,
            ))
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
        return 'shop_main_benefit';
    }

} 