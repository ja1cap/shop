<?php
namespace Weasty\Bundle\AdBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProposalBannerType
 * @package Weasty\Bundle\AdBundle\Form\Type
 */
class ProposalBannerType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', 'textarea', array(
                'required' => false,
                'label' => 'Название',
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'image',
                'label' => 'Изображение',
                'required' => true,
            ))
            ->add('proposal', 'weasty_admin_browser_type', array(
                'required' => true,
                'multiple' => false,
                'label' => 'Товары',
                'browser_path' => 'proposals_browser',
                'item_value_element_class' => 'item-value-element',
                'options' => array(
                    'class' => 'ShopCatalogBundle:Proposal',
                ),
                'prototype_options' => array(
                    'attr' => array(
                        'class' => 'item-value-element',
                    ),
                ),
                'allow_add' => true,
                'allow_delete' => true,
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
        return 'weasty_ad_proposal_banner';
    }

}