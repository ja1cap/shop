<?php
namespace Weasty\Bundle\NewsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Weasty\Bundle\NewsBundle\Entity\BasePost;

/**
 * Class PostType
 * @package Weasty\Bundle\NewsBundle\Form\Type
 */
class PostType extends AbstractType {

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
                'choices' => BasePost::$statuses,
                'label' => 'Статус',
            ))
            ->add('image', 'sonata_media_type', array(
                'provider' => 'sonata.media.provider.image',
                'context'  => 'news_image',
                'label' => 'Изображение',
                'required' => false,
            ))
            ->add('description', 'textarea', array(
                'required' => true,
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
        return 'weasty_news_post';
    }

} 