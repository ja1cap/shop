<?php
namespace Weasty\DoctrineBundle\Form\ChoiceList;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class RepositoryLoader
 * @package Weasty\DoctrineBundle\Form\ChoiceList
 */
class RepositoryLoader implements EntityLoaderInterface {

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var \Closure
     */
    protected $queryBuilder;

    /**
     * @param \Closure $queryBuilder
     * @param ObjectRepository $repository
     */
    function __construct($queryBuilder, ObjectRepository $repository)
    {
        $this->queryBuilder = $queryBuilder;
        $this->repository = $repository;
    }

    /**
     * Returns an array of entities that are valid choices in the corresponding choice list.
     *
     * @return array The entities.
     * @throws UnexpectedTypeException
     */
    public function getEntities()
    {

        $queryBuilder = $this->getQueryBuilder();

        // Query builder must be a closure
        if (!$queryBuilder instanceof \Closure) {
            throw new UnexpectedTypeException($queryBuilder, '\Closure');
        }

        return $queryBuilder($this->getRepository());

    }

    /**
     * Returns an array of entities matching the given identifiers.
     *
     * @param string $identifier The identifier field of the object. This method
     *                           is not applicable for fields with multiple
     *                           identifiers.
     * @param array $values The values of the identifiers.
     *
     * @return array The entities.
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        return $this->getRepository()->findBy(array(
            $identifier => $values,
        ));
    }

    /**
     * @return callable
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

} 