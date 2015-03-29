<?php
namespace Shop\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SettingsType
 * @package Shop\MainBundle\Form\Type
 */
class SettingsType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'textarea', array(
                'required' => true,
                'label' => 'Название магазина',
            ))
            ->add('logo', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'logo',
                'label' => 'Логотип магазина',
                'required' => false,
            ))
            ->add('favicon', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'favicon',
                'label' => 'Favicon магазина',
                'required' => false,
            ))
            ->add('title', 'textarea', array(
                'required' => false,
                'label' => 'SEO - заголовок',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                ),
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'SEO - описание',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 200,
                ),
            ))
            ->add('keywords', 'textarea', array(
                'required' => false,
                'label' => 'SEO - ключивые слова',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                )
            ))
//            ->add('vk_url', 'text', array(
//                'required' => false,
//                'label' => 'Вконтакте',
//            ))
//            ->add('fb_url', 'text', array(
//                'required' => false,
//                'label' => 'facebook',
//            ))
//            ->add('google_url', 'text', array(
//                'required' => false,
//                'label' => 'Google+',
//            ))
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
        return 'settings';
    }

} 