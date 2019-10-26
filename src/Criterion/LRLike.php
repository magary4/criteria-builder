<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder\Criterion;

/**
 * Class LRLike
 *
 * @suppress PhanUnreferencedClass
 */
class LRLike extends Like
{
    /**
     * {@inheritdoc}
     */
    public function setValue($value): CriteriaInterface
    {
        if (!is_string($value)) {
            throw new \LogicException('QueryBuilder "like" criteria should have a string value.');
        }

        $value = "%$value%";

        parent::setValue($value);

        return $this;
    }
}
