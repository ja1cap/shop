<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Entity\Proposal;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\ParameterOption;

/**
 * Class ProposalType
 * @package Shop\CatalogBundle\Form\Type
 */
class ProposalType extends AbstractType {

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
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Наименование',
            ))
            ->add('short_description', 'textarea', array(
                'required' => true,
                'label' => 'Короткое описание',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                )
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Полное описание',
            ))
            ->add('manufacturer', 'entity', array(
                'class' => 'ShopCatalogBundle:Manufacturer',
                'property' => 'name',
                'label' => 'Производитель',
            ));

        $builder->add('defaultContractor', 'entity', array(
            'required' => false,
            'empty_value' => 'Выберите контрагента',
            'class' => 'ShopCatalogBundle:Contractor',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            'label' => 'Контрагент поумолчанию',
        ));

        $builder
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => Proposal::$statuses,
                'label' => 'Статус',
            ));

        /**
         * @var $categoryParameter CategoryParameter
         */
        foreach($this->getCategory()->getParameters() as $categoryParameter){

            if(!$categoryParameter->getParameter()->getIsPriceParameter()){

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
            ->add('showOnHomePage', 'choice', array(
                'label' => 'Показывать на главной странице',
                'choices' => array(
                    'Нет',
                    'Да',
                ),
                'required' => true,
            ));

        $builder
            ->add('seoTitle', 'text', array(
                'required' => false,
                'label' => 'Заголовок страницы (title)',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                )
            ))
            ->add('seoDescription', 'textarea', array(
                'required' => false,
                'label' => 'Описание страницы (description)',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 200,
                )
            ))
            ->add('seoKeywords', 'textarea', array(
                'required' => false,
                'label' => 'Ключевые слова страницы (keywords)',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                )
            ));

        $builder->add('seoSlug', 'text', array(
            'required' => false,
            'label' => 'Имя страницы в адресе (slug)',
            'attr' => array(
                'class' => 'maxlength',
                'data-max'=> 50,
            )
        ));

        $builder
            ->add('thumbImage', 'file', array(
                'required' => false,
                'label' => 'Маленькая картинка',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Большая картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ));

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'proposal';
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

} 