<?php
namespace Weasty\DoctrineBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Weasty\DoctrineBundle\Form\ChoiceList\RepositoryLoader;

/**
 * Class RepositoryType
 * @package Weasty\DoctrineBundle\Form\Type
 */
class RepositoryType extends DoctrineType {

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @param ManagerRegistry $registry
     * @param $entityClass
     */
    public function __construct(ManagerRegistry $registry, $entityClass = null)
    {
        $this->entityClass = $entityClass;
        parent::__construct($registry);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => $this->getEntityClass(),
        ));

        parent::setDefaultOptions($resolver);
    }

    /**
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param mixed $queryBuilder
     * @param string $class
     *
     * @return EntityLoaderInterface
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {

        $repository = $manager->getRepository($class ?: $this->getEntityClass());
        return new RepositoryLoader($queryBuilder, $repository);

    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository(){
        return $this->registry->getRepository($this->getEntityClass());
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'repository';
    }

} 