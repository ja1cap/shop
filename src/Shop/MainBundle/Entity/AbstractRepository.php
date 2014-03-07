<?php
namespace Shop\MainBundle\Entity;
use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

/**
 * Class AbstractRepository
 * @package Shop\MainBundle\Entity
 */
class AbstractRepository extends EntityRepository {

    /**
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function convertDqlToSql(QueryBuilder $qb){

        $em = $qb->getEntityManager();

        $fromParts = $qb->getDQLPart('from');
        $qb->resetDQLPart('from');

        /**
         * @var $fromPart \Doctrine\ORM\Query\Expr\From
         */
        foreach($fromParts as $fromPart){

            if(strpos($fromPart->getFrom(), ':') !== false){
                $tableName = $em->getClassMetadata($fromPart->getFrom())->getTableName();
            } else {
                $tableName = $fromPart->getFrom();
            }

            $qb->from($tableName, $fromPart->getAlias(), $fromPart->getIndexBy());

        }

        $rootJoinParts = $qb->getDQLPart('join');
        $qb->resetDQLPart('join');

        if($rootJoinParts){

            foreach($rootJoinParts as $rootAlias => $joinParts){

                /**
                 * @var $joinPart \Doctrine\ORM\Query\Expr\Join
                 */
                foreach($joinParts as $joinPart){

                    $join = $joinPart->getJoin();

                    if($join instanceof QueryBuilder){
                        $join = "(" . $this->convertDqlToSql($join) . ")";
                    } else {
                        if(strpos($join, ':') !== false ){
                            $join = $em->getClassMetadata($join)->getTableName();
                        }
                    }

                    $join = new Expr\Join(
                        $joinPart->getJoinType(), $join, $joinPart->getAlias(), Expr\Join::ON, $joinPart->getCondition(), $joinPart->getIndexBy()
                    );

                    $qb->add('join', array($rootAlias => $join), true);


                }

            }

        }

        return $qb;

    }

    /**
     * @param $className
     * @param $alias
     * @param null $resultAlias
     * @return Query\ResultSetMapping
     */
    protected function createResultSetMappingFromMetadata($className, $alias, $resultAlias = null){

        $rsm = new Query\ResultSetMapping();
        $metaData = $this->getEntityManager()->getClassMetadata($className);

        $rsm->addEntityResult($className, $alias, $resultAlias);

        if($metaData->discriminatorColumn){
            $rsm->addMetaResult($alias, $metaData->discriminatorColumn['name'], $metaData->discriminatorColumn['fieldName'])
                ->setDiscriminatorColumn($alias, $metaData->discriminatorColumn['name']);
        }

        foreach($metaData->fieldMappings as $fieldMapping){
            $rsm->addFieldResult($alias, $fieldMapping['columnName'], $fieldMapping['fieldName']);
        }

        return $rsm;

    }

} 