<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class In
 *
 * @suppress PhanUnreferencedClass
 */
class In extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        if (count($this->value) <= 0) {
            return;
        }

        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->in($this->property, ':' . $this->propertyAlias)
        );

        $this->queryBuilder->setParameter($this->propertyAlias, $this->value);
    }
}
