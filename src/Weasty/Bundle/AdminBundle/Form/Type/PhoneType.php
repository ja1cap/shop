<?php
namespace Weasty\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PhoneType
 * @package Weasty\Bundle\AdminBundle\Form\Type
 */
class PhoneType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('countryCode', 'text', array(
                'required' => false,
                'label' => 'Код страны',
            ))
            ->add('code', 'text', array(
                'required' => false,
                'label' => 'Код оператора',
            ))
            ->add('number', 'text', array(
                'required' => true,
                'label' => 'Номер',
            ))
//            ->add('icon', 'sonata_media_type', array(
//                'provider' => 'sonata.media.provider.image',
//                'context'  => 'icon',
//                'label' => 'Иконка',
//                'required' => false,
//            ))
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
        return 'weasty_admin_phone_type';
    }

}