<?php
namespace Shop\DiscountBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class ActionProposalType
 * @package Shop\DiscountBundle\Form\Type
 */
class ActionProposalType extends ActionConditionType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    function buildConditionForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('proposal', 'entity', array(
                'required' => true,
                'class' => 'ShopCatalogBundle:Proposal',
                'multiple' => false,
                'attr' => array(
                    'data-placeholder' => 'Выберите',
                    'class' => 'chosen-select',
                ),
                'label' => 'Товар',
                'query_builder' => function(EntityRepository $er) {

                    return $er->createQueryBuilder('c')
                        ->orderBy('c.title', 'ASC');

                },
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
        return 'shop_discount_action_proposal';
    }

} 