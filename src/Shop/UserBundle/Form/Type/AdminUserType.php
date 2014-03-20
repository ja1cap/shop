<?php

namespace Shop\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AdminUserType
 * @package Shop\UserBundle\Form\Type
 */
class AdminUserType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('user', new UserType(), array(
            'label' => 'Данные пользователя',
        ));

        $builder->add('plainPassword', 'repeated', array(
            'required' => false,
            'first_name' => 'password',
            'first_options' => array(
                'label' => 'Пароль',
                'attr' => array(
                    'autocomplete' => 'off'
                )
            ),
            'second_name' => 'confirm',
            'second_options' => array(
                'label' => 'Повторите пароль',
                'attr' => array(
                    'autocomplete' => 'off'
                )
            ),
            'type' => 'password',
        ));

        $builder->add('roles', 'entity', array(
            'required' => false,
            'multiple' => true,
            'class' => 'ShopMainBundle:Role',
            'property' => 'name',
            'label' => 'Должности'
        ));

        $builder->add('save', 'submit', array(
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
        return 'admin_user';
    }

} 