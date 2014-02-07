<?php
namespace Shop\MainBundle\Form\Type;

use Shop\MainBundle\Entity\Mattress;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MattressType
 * @package Shop\MainBundle\Form\Type
 */
class MattressType extends AbstractProposalType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    public function buildProposalFormElements(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('manufacturerId', 'choice', array(
                'choices' => Mattress::$manufacturers,
                'label' => 'Производитель',
            ));

        $builder->add('bedBases', 'entity', array(
            'class' => 'ShopMainBundle:BedBase',
            'property' => 'title',
            'multiple' => true,
//            'expanded' => true,
            'required' => false,
            'label' => 'Подходящие основания',
            'attr' => array(
                'data-placeholder'=>'Выберите основания...'
            )
        ));

    }

} 