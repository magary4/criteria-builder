<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder;

use Doctrine\ORM\Query;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

trait CountTrait
{
    /**
     * @throws NonUniqueResultException
     *
     * @suppress PhanUnusedVariableCaughtException
     */
    protected function getCountFromQuery(Query $query): int
    {
        try {
            $count = intval($query->getSingleScalarResult());
        } catch (NoResultException $e) {
            $count = 0;
        }

        return $count;
    }
}
