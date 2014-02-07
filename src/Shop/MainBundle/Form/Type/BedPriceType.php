<?php
namespace Shop\MainBundle\Form\Type;

use Shop\MainBundle\Entity\BedPrice;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BedPriceType
 * @package Shop\MainBundle\Form\Type
 */
class BedPriceType extends AbstractPriceType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    public function buildPriceFormElements(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('sizeId', 'choice', array(
                'choices' => BedPrice::$sizes,
                'label' => 'Размер (см x см)',
            ));


    }

} 