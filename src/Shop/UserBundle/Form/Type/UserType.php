<?php
namespace Shop\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserType
 * @package Shop\UserBundle\Form\Type
 */
class UserType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', 'email', array(
                'label' => 'Электорнный адрес',
                'attr' => array(
                    'autocomplete' => 'off'
                ),
            ))
            ->add('username', 'text', array(
                'label' => 'Логин',
                'attr' => array(
                    'autocomplete' => 'off'
                ),
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
        return 'user';
    }

} 