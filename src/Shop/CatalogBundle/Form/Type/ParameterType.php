<?php
namespace Shop\CatalogBundle\Form\Type;

use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Filter\FiltersResource;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ParameterType
 * @package Shop\CatalogBundle\Form\Type
 */
class ParameterType extends AbstractType {

    /**
     * @var Parameter
     */
    protected $parameter;

    /**
     * @param Parameter $parameter
     */
    function __construct(Parameter $parameter = null)
    {
        $this->parameter = $parameter;
    }

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
            ->add('type', 'choice', array(
                'choices' => FiltersResource::$filterTypes,
                'label' => 'Тип элемента',
            ))
//            ->add('isPriceParameter', 'choice', array(
//                'choices' => array(
//                    'нет',
//                    'да',
//                ),
//                'label' => 'Параметр цены',
//            ))
        ;

        if($this->parameter && $this->parameter->getId()){

            $builder->add('defaultOption', 'entity', array(
                'required' => false,
                'choices' => $this->parameter->getOptions(),
                'class' => 'ShopCatalogBundle:ParameterOption',
                'label' => 'Значение поумолчанию',
            ));

        }

        $builder
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'parameter';
    }

} 