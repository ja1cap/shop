<?php
namespace Shop\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserType
 * @package Shop\MainBundle\Form\Type
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Shop\MainBundle\Entity\User'
        ));
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