<?php
namespace Shop\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class CategoryType
 * @package Shop\CatalogBundle\Form\Type
 */
class CategoryType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('singularName', 'text', array(
                'required' => true,
                'label' => 'Название товара в единстенном числе',
            ))
            ->add('multipleName', 'text', array(
                'required' => true,
                'label' => 'Название товара в множественном числе',
            ));

        $builder->add('image', 'sonata_media_type', array(
            'provider' => 'sonata.media.provider.image',
            'context'  => 'default',
            'label' => 'Изображение',
            'required' => false,
        ));

//        $imageField = $builder->get('image');
//        $imageUnlinkField = $imageField->get('unlink');
//        $imageUnlinkFieldType = $imageUnlinkField->getType()->getName();
//        $imageUnlinkFieldOptions = $imageUnlinkField->getOptions();
//
//        $imageUnlinkFieldOptions['label'] = ''
//        $imageField->add('unlink', $imageUnlinkFieldType, $imageUnlinkFieldOptions);

        $builder
            ->add('status', 'choice', array(
                'required' => true,
                'choices' => Category::$statuses,
                'label' => 'Статус',
            ));

        $builder->add('additionalCategories', 'entity', array(
            'required' => false,
            'class' => 'ShopCatalogBundle:Category',
            'multiple' => true,
            'attr' => array(
                'data-placeholder' => 'Выберите'
            ),
            'label' => 'Категории дополнительных товаров',
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
        return 'category';
    }

} 