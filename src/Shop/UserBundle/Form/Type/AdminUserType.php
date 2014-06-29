<?php

namespace Shop\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AdminUserType
 * @package Shop\UserBundle\Form\Type
 */
class AdminUserType extends UserType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        parent::buildForm($builder, $options);

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

        $builder->add('enabled', 'choice', array(
            'label' => 'Активирован',
            'choices' => array(
                'Нет',
                'Да',
            ),
        ));

//        $builder->add('roles', 'choice', array(
//            'required' => false,
//            'multiple' => true,
//            'label' => 'Должности',
//            'choices' => array(
//                'ROLE_MANAGER' => 'Менеджер',
//                'ROLE_ADMIN' => 'Администратор',
//            ),
//        ));

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