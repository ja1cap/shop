<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActionType
 * @package Shop\CatalogBundle\Form\Type
 */
class ActionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('thumbImage', 'file', array(
                'required' => false,
                'label' => 'Маленькая картинка',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Большая картинка',
            ));

        $builder
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => Action::$statuses,
                'label' => 'Статус',
            ));

        $builder
            ->add('minOrderSummary', 'text', array(
                'required' => false,
                'label' => 'Минимальная сумма заказа',
            ));

        $builder
            ->add('maxOrderSummary', 'text', array(
                'required' => false,
                'label' => 'Максимальная сумма заказа',
            ));

        $builder->add('categories', 'entity', array(
            'class' => 'ShopCatalogBundle:Category',
            'multiple' => true,
            'attr' => array(
                'data-placeholder' => 'Выберите',
                'class' => 'chosen-select',
            ),
            'label' => 'Категории',
            'query_builder' => function(EntityRepository $er) {

                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');

                },
        ));

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
        return 'action';
    }

} 