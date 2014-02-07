<?php
namespace Shop\MainBundle\Form\Type;

use Shop\MainBundle\Entity\BedBasePrice;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BedBasePriceType
 * @package Shop\MainBundle\Form\Type
 */
class BedBasePriceType extends AbstractPriceType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    public function buildPriceFormElements(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('sizeId', 'choice', array(
                'choices' => BedBasePrice::$sizes,
                'label' => 'Размер (см x см)',
            ));


    }

} 