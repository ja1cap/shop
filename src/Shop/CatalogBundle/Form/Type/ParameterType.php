<?php
namespace Shop\CatalogBundle\Form\Type;

use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Filter\CategoryFiltersResource;
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
                'choices' => CategoryFiltersResource::$filterTypes,
                'label' => 'Тип елемента',
            ))
        ;

        if($this->parameter && $this->parameter->getId()){

            $builder->add('defaultOption', 'entity', array(
                'required' => false,
                'choices' => $this->parameter->getOptions(),
                'class' => 'ShopCatalogBundle:ParameterOption',
                'label' => 'Опция поумолчанию',
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