<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PopularProposalType
 * @package Shop\CatalogBundle\Form\Type
 */
class PopularProposalType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('proposal', 'shtumi_ajax_autocomplete', array(
                'entity_alias' => 'proposals',
                'attr' => array(
                    'data-placeholder' => 'Начните вводите название',
                ),
                'label' => 'Предложение',
            ))
            ->add('save', 'submit', array(
                'label' => 'Добавить',
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
        return 'shop_catalog_popular_proposal_type';
    }

} 