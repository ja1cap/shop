<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProposalCollectionType
 * @package Shop\CatalogBundle\Form\Type
 */
class ProposalCollectionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'label' => 'Название',
            ))
            ->add('slug', 'text', array(
                'label' => 'Уникальный адрес (http://домен/catalog/:адрес)',
            ))
        ;

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
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
        return 'shop_catalog_proposal_collection';
    }

} 