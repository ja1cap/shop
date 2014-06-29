<?php
namespace Shop\DiscountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActionConditionProposalType
 * @package Shop\DiscountBundle\Form\Type
 */
class ActionConditionProposalType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('proposal', 'weasty_doctrine_hidden_type', array(
                'required' => false,
                'class' => 'ShopCatalogBundle:Proposal',
                'attr' => array(
                    'class' => 'item-value-element',
                ),
            ))
            ->add('test', 'text', array(
                'required' => false,
                'mapped' => false,
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
        return 'shop_discount_action_condition_proposal';
    }

} 