<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

use Doctrine\ORM\QueryBuilder;

interface CriteriaInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @return CriteriaInterface
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder): CriteriaInterface;

    /**
     * @param string $whereMethod
     * @return CriteriaInterface
     */
    public function setWhereMethod(string $whereMethod): CriteriaInterface;

    /**
     * @param string $property
     * @return CriteriaInterface
     */
    public function setProperty(string $property): CriteriaInterface;

    /**
     * @param string $propertyAlias
     * @return CriteriaInterface
     */
    public function setPropertyAlias(string $propertyAlias): CriteriaInterface;

    /**
     * @param mixed $value
     * @return CriteriaInterface
     */
    public function setValue($value): CriteriaInterface;

    /**
     * @return void
     */
    public function apply(): void;
}
