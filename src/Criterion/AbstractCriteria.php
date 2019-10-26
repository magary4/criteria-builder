<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

use Doctrine\ORM\QueryBuilder;

abstract class AbstractCriteria implements CriteriaInterface
{
    /** @var  QueryBuilder */
    protected $queryBuilder;

    /** @var  string */
    protected $whereMethod;

    /** @var  string */
    protected $property;

    /** @var  string */
    protected $propertyAlias;

    /** @var  mixed */
    protected $value;

    /**
     * {@inheritdoc}
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder): CriteriaInterface
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setWhereMethod(string $whereMethod): CriteriaInterface
    {
        $this->whereMethod = $whereMethod;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty(string $property): CriteriaInterface
    {
        $this->property = $property;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPropertyAlias(string $propertyAlias): CriteriaInterface
    {
        $this->propertyAlias = $propertyAlias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value): CriteriaInterface
    {
        $this->value = $value;

        return $this;
    }
}
