<?php declare(strict_types=1);

namespace Rvadym\CriteriaBuilder;

use Doctrine\ORM\QueryBuilder;
use Rvadym\CriteriaBuilder\Criterion;

/**
 * Class CriteriaBuilder
 *
 *   key                    value          SQL
 * {or.}property           => 'bla'     (property = 'bla')
 * {or.}property.not       => 'bla'     (property <> 'bla')
 * {or.}property           => [1, 2]    (property in (1, 2))
 * {or.}property.not       => [1, 2]    (property not in (1, 2))
 * {or.}property.in        => [1, 2]    (property in (1, 2))
 * {or.}property.not.in    => [1, 2]    (property not in (1, 2))
 * {or.}property           => null      (property is null)
 * {or.}property.not       => null      (property is not null)
 * {or.}property.like      => 'bla'     (property like 'bla')
 * {or.}property.%like%    => 'bla'     (property like '%bla%')
 * {or.}property.%like     => 'bla'     (property like '%bla')
 * {or.}property.like%     => 'bla'     (property like 'bla%')
 * {or.}property.gt        => 5         (property > 5)
 * {or.}property.gte       => 5         (property >= 5)
 * {or.}property.lt        => 5         (property < 5)
 * {or.}property.lte       => 5         (property <= 5)
 *
 *
 * Not required prefix "or" will change condition from "andWhere" to "orWhere"
 *
 * Possible combinations
 * property
 * or.property
 * property.criteria
 * or.property.criteria
 * property.not.criteria
 * or.property.not.criteria
 *
 * TODO:
 * Grouping:
 * {or.}group => [
 *    property1           => 'foo',
 *    {or.}property2.not  => 'bar',
 * ]
 * AND/OR (WHERE (property1 = "foo" AND/OR property2 <> "bar"))
 *
 * TODO:
 * Add wrappers for following syntax
 *
 * $cb = new CriteriaBuilder($criteria);
 * $cb
 *   ->equals('proprty1', 'bla')
 *   ->or()
 *   ->notEquals('property2', 'qwerty');
 */
class CriteriaBuilder
{
    /** @var QueryBuilder  */
    protected $queryBuilder;

    /** @var array  */
    protected $criteria = [];

    /** @var string  */
    protected $tableAlias = '_t';

    /** @var int  */
    protected $propertyAliasCounter = 0;

    /** @var array  */
    protected static $criteriaClassesMapping = [

        // key             yes              not

        'equals'    => [ 'Equals',  'NotEquals' ],
        'in'        => [ 'In',      'NotIn'     ],
        'is_null'   => [ 'IsNull',  'IsNotNull' ],
        'like'      => [ 'Like',    false       ],
        '%like%'    => [ 'LRLike',  false       ],
        '%like'     => [ 'LLike',   false       ],
        'like%'     => [ 'RLike',   false       ],
        'gt'        => [ 'Gt',      false       ],
        'gte'       => [ 'Gte',     false       ],
        'lt'        => [ 'Lt',      false       ],
        'lte'       => [ 'Lte',     false       ],

    ];

    /**
     * CriteriaBuilder constructor.
     * @param QueryBuilder $queryBuilder
     * @param null|string $tableAlias
     */
    public function __construct(QueryBuilder $queryBuilder, ?string $tableAlias = null)
    {
        $this->queryBuilder = $queryBuilder;

        if (!is_null($tableAlias)) {
            $this->setTableAlias($tableAlias);
        } elseif (count($queryBuilder->getAllAliases()) > 0) {
            $this->setTableAlias($queryBuilder->getAllAliases()[0]);
        }
    }

    /**
     * @param array $criteria
     * @return $this
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public function setCriteria(array $criteria): self
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return CriteriaBuilder
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public function setCriterion(string $key, string $value): self
    {
        $this->criteria[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param string|array $value
     * @return CriteriaBuilder
     */
    public function addCriterion(string $key, $value): self
    {
        if (isset($this->criteria[$key])) {
            if (!is_array($this->criteria[$key])) {
                $this->criteria[$key] = [$this->criteria[$key]];
            }

            if (is_array($value)) {
                $this->criteria[$key] = array_merge($this->criteria[$key], $value);
            } else {
                $this->criteria[$key][] = $value;
            }
        } else {
            $this->criteria[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $from
     * @param null|string $toPos
     * @param null|string $toNeg
     * @throws \Exception
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public static function addClassMapping(string $from, ?string $toPos, ?string $toNeg): void
    {
        if (isset(self::$criteriaClassesMapping[$from])) {
            throw new \Exception(sprintf('Mapping for key "%s" already set to "%s"', $from, self::$criteriaClassesMapping[$from]));
        }

        $to = [$toPos, $toNeg];

        self::$criteriaClassesMapping[$from] = $to;
    }

    /**
     * @param string $tableAlias
     * @return CriteriaBuilder
     *
     * @suppress PhanUnreferencedPublicMethod
     */
    public function setTableAlias(string $tableAlias): self
    {
        $this->tableAlias = $tableAlias;

        return $this;
    }

    /**
     * @return string
     */
    protected function getTableAlias(): string
    {
        return $this->tableAlias;
    }

    /**
     * @return QueryBuilder
     * @throws \Exception
     */
    public function apply(): QueryBuilder
    {
        return $this->applyCriteria($this->queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     * @throws \Exception
     *
     * @suppress PhanNonClassMethodCall
     */
    protected function applyCriteria(QueryBuilder $queryBuilder): QueryBuilder
    {
        foreach ($this->criteria as $actionString => $value) {
            list($criteriaClass, $whereMethod, $property, $propertyAlias) = $this->parseActionString($actionString, $value);

            // used for messages
            $backCriteriaClass = $criteriaClass;

            // 3rd party classes support
            if (!class_exists($criteriaClass)) {
                $criteriaClass = __NAMESPACE__ . '\\Criterion\\' . $criteriaClass;
            }

            if (!class_exists($criteriaClass)) {
                throw new \Exception(sprintf(
                    'Criterion "%s" not found. (%s)',
                    $criteriaClass,
                    $backCriteriaClass
                ));
            }

            /** @var Criterion\CriteriaInterface $criteria */
            $criteria = new $criteriaClass();

            if (!$criteria instanceof Criterion\CriteriaInterface) {
                throw new \Exception(sprintf(
                    'Criterion must implement %s',
                    Criterion\CriteriaInterface::class
                ));
            }

            $criteria
                ->setQueryBuilder($queryBuilder)
                ->setWhereMethod($whereMethod)
                ->setProperty($this->getPropertyName($property))
                ->setPropertyAlias($propertyAlias)
                ->setValue($value)
            ;

            /*$queryBuilder =*/ $criteria->apply();
        }

        return $queryBuilder;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getPropertyName(string $name): string
    {
        if (false === strpos($name, '.') && $this->getTableAlias()) {
            $name = sprintf('%s.%s', $this->getTableAlias(), $name);
        }

        return $name;
    }

//    /**
//     * @param string $name
//     * @return string
//     */
//    protected function getPropertyName(string $name): string
//    {
//        if (false === strpos($name, '.')) {
//            if ($this->getTableAlias()) {
//                $name = sprintf('`%s`.`%s`', $this->getTableAlias(), $name);
//            } else {
//                $name = sprintf('`%s`', $this->getTableAlias(), $name);
//            }
//        } else {
//            $arr    = explode('.', $name);
//            $newArr = [];
//            foreach ($arr as $item) {
//                $newArr[] = sprintf('`%s`', str_replace('`', '', $item));
//            }
//
//            $name = implode('.', $newArr);
//        }
//
//        return $name;
//    }

    /**
     * @param string $actionString
     * @param null|array|string $value
     * @return array
     * @throws \Exception
     */
    protected function parseActionString(string $actionString, $value): array
    {
        $isNot                     = false;
        $whereMethod               = 'andWhere';

        if (is_null($value)) {
            $criteriaClassMappingKey  = 'is_null';
        } elseif (is_array($value)) {
            $criteriaClassMappingKey  = 'in';
        } else {
            $criteriaClassMappingKey  = 'equals';
        }

        $arr = explode('.', $actionString);

        if (count($arr) <= 0) {
            throw new \Exception('At least field name must be provided');
        }

        // is "or" ?
        if ($arr[0] === 'or') {
            $whereMethod = 'orWhere';
            array_shift($arr);
        }

        if (count($arr) <= 0) {
            throw new \Exception('Field name is required and cannot be "or"');
        }

        // get property
        $property = array_shift($arr);
        $propertyAlias   = $property . '_' . $this->propertyAliasCounter;

        // is "not"?
        if (isset($arr[0]) && $arr[0] === 'not') {
            $isNot = true;
            array_shift($arr);
        }

        // criteria method
        if (isset($arr[0])) {
            $criteriaClassMappingKey = $arr[0];
        }
        $criteriaClass = $this->getCriteriaClass($criteriaClassMappingKey, $isNot);

        $this->propertyAliasCounter++;

        return [
            $criteriaClass,
            $whereMethod,
            $property,
            $propertyAlias
        ];
    }

    /**
     * @param string $criteriaClassMappingKey
     * @param bool $isNot
     * @return string
     * @throws \Exception
     */
    protected function getCriteriaClass(string $criteriaClassMappingKey, bool $isNot = false): string
    {
        if (!isset(self::$criteriaClassesMapping[$criteriaClassMappingKey]) || !self::$criteriaClassesMapping[$criteriaClassMappingKey]) {
            throw new \Exception(sprintf('No class set for criteria class mapping key "%s"', $criteriaClassMappingKey));
        }

        $classes = self::$criteriaClassesMapping[$criteriaClassMappingKey];

        if ($isNot) {
            if (empty($classes[1])) {
                throw new \Exception(sprintf('No "not" class set for criteria class mapping key "%s"', $criteriaClassMappingKey));
            }

            return $classes[1];
        }

        return $classes[0];
    }
}
