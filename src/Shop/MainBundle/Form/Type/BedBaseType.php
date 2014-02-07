<?php
namespace Shop\MainBundle\Form\Type;

use Shop\MainBundle\Entity\BedBase;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BedBaseType
 * @package Shop\MainBundle\Form\Type
 */
class BedBaseType extends AbstractProposalType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    public function buildProposalFormElements(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('manufacturerId', 'choice', array(
                'choices' => BedBase::$manufacturers,
                'label' => 'Производитель',
            ));

    }

} 