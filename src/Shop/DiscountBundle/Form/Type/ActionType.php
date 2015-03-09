<?php
namespace Shop\DiscountBundle\Form\Type;

use Shop\DiscountBundle\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActionType
 * @package Shop\DiscountBundle\Form\Type
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
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => Action::$statuses,
                'label' => 'Статус',
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'action_image',
                'label' => 'Изображение',
                'required' => false,
            ))
            ->add('startDate', 'datetime', array(
                'required' => false,
                'label' => 'Дата начала акции',
            ))
            ->add('endDate', 'datetime', array(
                'required' => false,
                'label' => 'Дата окончния акции',
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label' => 'Описание',
            ))
            ->add('content', 'ckeditor', array(
                'required' => false,
                'label' => 'Содержание',
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
        return 'shop_discount_action';
    }

} 