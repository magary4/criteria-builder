<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class IsNotNull
 *
 * @suppress PhanUnreferencedClass
 */
class IsNotNull extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->isNotNull($this->property/*, ':' . $this->propertyAlias*/)
        );
    }
}
