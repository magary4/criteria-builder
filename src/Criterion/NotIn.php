<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class NotIn
 *
 * @suppress PhanUnreferencedClass
 */
class NotIn extends AbstractCriteria
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
            $this->queryBuilder->expr()->notIn($this->property, ':' . $this->propertyAlias)
        );

        $this->queryBuilder->setParameter($this->propertyAlias, $this->value);
    }
}
