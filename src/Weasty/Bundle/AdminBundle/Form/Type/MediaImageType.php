<?php
namespace Weasty\Bundle\AdminBundle\Form\Type;

use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MediaImageType
 * @package Weasty\Bundle\AdminBundle\Form\Type
 */
class MediaImageType extends MediaType {

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'image',
            ))
        ;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);

        $builder
            ->add('save', 'submit', array(
                'label' => 'Загрузить',
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
        return 'weasty_admin_media_image_type';
    }

} 