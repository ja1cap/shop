<?php
namespace Shop\UserBundle\Form\Type;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Contractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class ManagerContractorType
 * @package Shop\UserBundle\Form\Type
 */
class ManagerContractorType extends AbstractType {

    /**
     * @var ObjectRepository
     */
    protected $contractorRepository;

    /**
     * @var ObjectRepository
     */
    protected $categoryRepository;

    /**
     * @param ObjectRepository $categoryRepository
     * @param ObjectRepository $contractorRepository
     */
    function __construct(ObjectRepository $categoryRepository, ObjectRepository $contractorRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->contractorRepository = $contractorRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $contractorChoices = array(
            '*' => 'Все',
        );

        $contractors = $this->contractorRepository->findBy(array(), array('name' => 'ASC'));
        foreach($contractors as $contractor){
            if($contractor instanceof Contractor){
                $contractorChoices[$contractor->getId()] = $contractor->getName();
            }
        }

        $builder
            ->add('contractorId', 'choice', array(
                'label' => 'Контрагент',
                'empty_value' => 'Выберите',
                'empty_data'  => null,
                'choices' => $contractorChoices,
            ))
        ;

        $categoriesChoices = array(
            '*' => 'Все',
        );

        $categories = $this->categoryRepository->findBy(array(), array('name' => 'ASC'));
        foreach($categories as $category){
            if($category instanceof Category){
                $categoriesChoices[$category->getId()] = $category->getName();
            }
        }

        $builder
            ->add('categoriesIds', 'choice', array(
                'label' => 'Категории',
                'multiple' => true,
                'attr' => array(
                    'data-placeholder' => 'Выберите'
                ),
                'choices' => $categoriesChoices,
            ))
        ;

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
        return 'admin_manager_contractor';
    }

} 