<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class Like
 *
 * @suppress PhanUnreferencedClass
 */
class Like extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function setValue($value): CriteriaInterface
    {
        if (!is_string($value)) {
            throw new \LogicException('QueryBuilder "like" criteria should have a string value.');
        }

        parent::setValue($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->like($this->property, ':' . $this->propertyAlias)
        );

        $this->queryBuilder->setParameter($this->propertyAlias, $this->value);
    }
}
