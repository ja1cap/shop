<?php
namespace Weasty\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SeoType
 * @package Weasty\Bundle\AdminBundle\Form\Type
 */
class SeoType extends AbstractType {

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver
            ->setDefaults(array(
                'slug_element_name' => 'seoSlug',
                'title_element_name' => 'seoTitle',
                'description_element_name' => 'seoDescription',
                'keywords_element_name' => 'seoKeywords',
                'submit_button' => true,
            ))
            ->setAllowedTypes(array(
                'slug_element_name' => array('string', 'null'),
                'title_element_name' => array('string', 'null'),
                'description_element_name' => array('string', 'null'),
                'keywords_element_name' => array('string', 'null'),
                'submit_button' => 'bool',
            ))
        ;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if($options['slug_element_name']){

            $builder
                ->add($options['slug_element_name'], 'text', array(
                    'required' => false,
                    'label' => 'Имя страницы в адресе (slug)',
                    'attr' => array(
                        'class' => 'maxlength',
                        'data-max'=> 50,
                    )
                ))
            ;

        }

        if($options['title_element_name']){

            $builder
                ->add($options['title_element_name'], 'text', array(
                    'required' => false,
                    'label' => 'Заголовок страницы (title)',
                    'attr' => array(
                        'class' => 'maxlength',
                        'data-max'=> 100,
                    )
                ))
            ;

        }

        if($options['description_element_name']){

            $builder
                ->add($options['description_element_name'], 'textarea', array(
                    'required' => false,
                    'label' => 'Описание страницы (description)',
                    'attr' => array(
                        'class' => 'maxlength',
                        'data-max'=> 200,
                    )
                ))
            ;

        }

        if($options['keywords_element_name']){

            $builder
                ->add($options['keywords_element_name'], 'textarea', array(
                    'required' => false,
                    'label' => 'Ключевые слова страницы (keywords)',
                    'attr' => array(
                        'class' => 'maxlength',
                        'data-max'=> 100,
                    )
                ))
            ;

        }

        if($options['submit_button']){

            $builder
                ->add('save', 'submit', array(
                    'label' => 'Сохранить',
                ))
            ;

        }

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'weasty_admin_seo_type';
    }

} 