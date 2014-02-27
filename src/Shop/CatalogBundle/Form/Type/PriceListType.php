<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PriceListType
 * @package Shop\CatalogBundle\Form\Type
 */
class PriceListType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('priceListFile', 'file', array(
                'required' => true,
                'label' => 'Фаил (.xls, .csv, .xlsx)',
            ));

        $builder->add('contractor', 'entity', array(
            'required' => true,
            'empty_value' => 'Выберите контрагента',
            'class' => 'ShopCatalogBundle:Contractor',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            'label' => 'Контрагент',
        ));

        $builder
            ->add('save', 'submit', array(
                'label' => 'Загрузить',
            ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'price_list';
    }

} 