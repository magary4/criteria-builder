<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class IsNull
 *
 * @suppress PhanUnreferencedClass
 */
class IsNull extends AbstractCriteria
{
    /**
     * {@inheritdoc}
     */
    public function apply(): void
    {
        $this->queryBuilder->{ $this->whereMethod }(
            $this->queryBuilder->expr()->isNull($this->property/*, ':' . $this->propertyAlias*/)
        );
    }
}
