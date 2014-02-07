<?php
namespace Shop\MainBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AbstractProposalType
 * @package Shop\MainBundle\Form\Type
 */
abstract class AbstractProposalType extends AbstractType {

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

        $this->buildProposalFormElements($builder, $options);

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
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    abstract public function buildProposalFormElements(FormBuilderInterface $builder, array $options);

    /**
     * @return string
     */
    public function getName()
    {
        return 'proposal';
    }

} 