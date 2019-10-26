<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class Gte
 *
 * @suppress PhanUnreferencedClass
 */
class Gte extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->gte($this->property, ':' . $this->propertyAlias)
        );

        $this->queryBuilder->setParameter($this->propertyAlias, $this->value);
    }
}
