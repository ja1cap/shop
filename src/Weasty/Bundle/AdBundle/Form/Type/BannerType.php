<?php
namespace Weasty\Bundle\AdBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BannerType
 * @package Weasty\Bundle\AdBundle\Form\Type
 */
class BannerType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', 'textarea', array(
                'required' => false,
                'label' => 'Название',
            ))
            ->add('url', 'text', array(
                'label' => 'Ссылка с баннера',
                'required' => true,
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'image',
                'label' => 'Изображение',
                'required' => true,
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
        return 'weasty_ad_banner';
    }

}