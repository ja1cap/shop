<?php
namespace Shop\MainBundle\Form\Type;

use Shop\UserBundle\Form\Type\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RegistrationType
 * @package Shop\MainBundle\Form\Type
 */
class RegistrationType extends AbstractType {

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
            'first_name' => 'password',
            'first_options' => array(
                'label' => 'Пароль',
                'attr' => array(
                    'autocomplete' => 'off'
                ),
            ),
            'second_name' => 'confirm',
            'second_options' => array(
                'label' => 'Повторите пароль',
                'attr' => array(
                    'autocomplete' => 'off'
                ),
            ),
            'type' => 'password',
        ));

        $builder->add('Register', 'submit', array(
            'label' => 'Зарегестировать',
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'registration';
    }

} 