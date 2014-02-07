<?php
namespace Shop\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AbstractPriceType
 * @package Shop\MainBundle\Form\Type
 */
abstract class AbstractPriceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('value', 'text', array(
                'required' => true,
                'label' => 'Цена (руб.)',
            ));

        $this->buildPriceFormElements($builder, $options);

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    abstract public function buildPriceFormElements(FormBuilderInterface $builder, array $options);

    /**
     * @return string
     */
    public function getName()
    {
        return 'price';
    }

} 