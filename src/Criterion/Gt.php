<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class Gt
 *
 * @suppress PhanUnreferencedClass
 */
class Gt extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->gt($this->property, ':' . $this->propertyAlias)
        );

        $this->queryBuilder->setParameter($this->propertyAlias, $this->value);
    }
}
