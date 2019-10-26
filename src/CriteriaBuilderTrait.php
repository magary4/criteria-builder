<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder;

use Doctrine\ORM\QueryBuilder;
use ArrayObject;

trait CriteriaBuilderTrait
{
    /**
     * @throws \Exception
     */
    protected function getQueryBuilder(ArrayObject $conditions, QueryBuilder $queryBuilder, string $alias): QueryBuilder
    {
        $criteriaBuilder = new CriteriaBuilder($queryBuilder, $alias);

        foreach ($conditions as $cKey => $cVal) {
            $criteriaBuilder->addCriterion((string)$cKey, $cVal);
        }

        return $criteriaBuilder->apply();
    }
}
