<?php
namespace Weasty\Bundle\AdBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
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
            //@TODO use autocomplete or proposals browser
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
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'banner',
                'label' => 'Изображение',
                'required' => false,
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
        return 'weasty_ad_proposal_banner';
    }

}