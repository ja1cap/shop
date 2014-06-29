<?php
namespace Weasty\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;

/**
 * Class BrowserType
 * @package Weasty\Bundle\AdminBundle\Form\Type
 */
class BrowserType extends AbstractType {

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'type' => 'weasty_doctrine_hidden_type',
                'prototype_type' => 'hidden',
                'prototype_options' => array(
                ),
            ))
            ->setRequired(array(
                'item_value_element_class',
                'browser_path',
            ))
            ->setAllowedTypes(array(
                'item_value_element_class' => 'string',
                'browser_path' => 'string',
                'prototype_type' => 'string',
            ))
        ;

    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars = array_replace($view->vars, array(
            'browser_path' => $options['browser_path'],
            'item_value_element_class' => $options['item_value_element_class'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['allow_add'] && $options['prototype']) {

            $prototype = $builder->create($options['prototype_name'], $options['prototype_type'], $options['prototype_options']);
            $builder->setAttribute('prototype', $prototype->getForm());

        }

        $resizeListener = new ResizeFormListener(
            $options['type'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete']
        );

        $builder->addEventSubscriber($resizeListener);
    }


    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'weasty_admin_browser_type';
    }

} 