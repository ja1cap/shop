<?php
namespace Shop\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\ParameterOption;

/**
 * Class PriceType
 * @package Shop\CatalogBundle\Form\Type
 */
class PriceType extends AbstractType {

    /**
     * @var Category
     */
    protected $category;

    /**
     * @param Category $category
     */
    function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('value', 'text', array(
                'required' => true,
                'label' => 'Цена (руб.)',
            ));

        /**
         * @var $categoryParameter CategoryParameter
         */
        foreach($this->getCategory()->getParameters() as $categoryParameter){

            if($categoryParameter->getParameter()->getIsPriceParameter()){

                $options = $categoryParameter->getParameter()->getOptions();

                $choices = array();

                /**
                 * @var $option ParameterOption
                 */
                foreach($options as $option){
                    $choices[$option->getId()] = $option->getName();
                }

                $builder->add('parameter' . $categoryParameter->getParameterId(), 'choice', array(
                    'label' => $categoryParameter->getName(),
                    'choices' => $choices,
                    'mapped' => false,
                    'required' => false,
                ));

            }

        }

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
        return 'price';
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

} 