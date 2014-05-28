<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Entity\Proposal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProposalType
 * @package Shop\CatalogBundle\Form\Type
 */
class ProposalType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Наименование',
            ))
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => Proposal::$statuses,
                'label' => 'Статус',
            ))
            ->add('manufacturer', 'entity', array(
                'class' => 'ShopCatalogBundle:Manufacturer',
                'property' => 'name',
                'label' => 'Производитель',
            ))
            ->add('defaultContractor', 'entity', array(
                'required' => false,
                'empty_value' => 'Выберите контрагента',
                'class' => 'ShopCatalogBundle:Contractor',
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                'label' => 'Контрагент поумолчанию',
            ))
            ->add('short_description', 'textarea', array(
                'required' => false,
                'label' => 'Короткое описание',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                )
            ))
            ->add(
                'description',
                'ckeditor',
                array(
                    'label' => 'Полное описание',
                    'config' => array(
//                        'toolbar' => array(
//                            array(
//                                'name' => 'links',
//                                'items' => array('Link','Unlink'),
//                            ),
//                            array(
//                                'name' => 'insert',
//                                'items' => array('Image'),
//                            ),
//                        )
                    )
                )
            )
        ;

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'shop_catalog_proposal_type';
    }

} 