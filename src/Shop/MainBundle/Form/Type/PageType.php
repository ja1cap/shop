<?php
namespace Shop\MainBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PageType
 * @package Shop\MainBundle\Form\Type
 */
class PageType extends AbstractType {

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $options;

    /**
     * @param array $options
     */
    function __construct(array $options = array())
    {
        $this->options = new ArrayCollection($options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('seoTitle', 'text', array(
                'required' => false,
                'label' => 'Заголовок страницы (title)',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                ),
            ))
            ->add('seoDescription', 'textarea', array(
                'required' => false,
                'label' => 'Описание страницы (description)',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 200,
                ),
            ))
            ->add('seoKeywords', 'textarea', array(
                'required' => false,
                'label' => 'Ключевые слова страницы (keywords)',
                'attr' => array(
                    'class' => 'maxlength',
                    'data-max'=> 100,
                ),
            ));

        if(!$this->options->containsKey('seoSlug') || ($this->options->containsKey('seoSlug') && $this->options->get('seoSlug'))){

            $builder->add('seoSlug', 'text', array(
                'required' => false,
                'label' => 'Имя страницы в адресе (slug)',
            ));

        }

        if(!$this->options->containsKey('content') || ($this->options->containsKey('content') && $this->options->get('content'))){

            $builder->add('content', 'textarea', array(
                'required' => false,
                'label' => 'Содержание страницы',
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
        return 'page';
    }

} 