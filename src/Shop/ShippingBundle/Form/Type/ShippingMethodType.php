<?php
namespace Shop\ShippingBundle\Form\Type;

use Shop\ShippingBundle\Entity\ShippingMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ShippingMethodType
 * @package Shop\ShippingBundle\Form\Type
 */
class ShippingMethodType extends AbstractType {

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param $locale
     */
    function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => ShippingMethod::$statuses,
                'label' => 'Статус',
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'Описание',
            ))
//            ->add('countries', 'country', array(
//                'choices' => $countriesChoices,
//                'multiple' => true,
//                'required' => true,
//                'label' => 'Страны',
//                'mapped' => false
//            ))
            ->add('countryCode', 'hidden')
            ->add('states', 'weasty_geonames_state', array(
                'multiple' => true,
                'required' => false,
                'label' => 'Регионы',
            ))
            ->add('cities', 'weasty_geonames_city', array(
                'multiple' => true,
                'required' => false,
                'group_by' => 'stateName(' . $this->getLocale() . ')',
                'label' => 'Города',
            ))
        ;


        $builder
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
        return 'shipping_method';
    }

}
