<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class NotEquals
 *
 * @suppress PhanUnreferencedClass
 */
class NotEquals extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->neq($this->property, ':' . $this->propertyAlias)
        );

        $this->queryBuilder->setParameter($this->propertyAlias, $this->value);
    }
}
