<?php

namespace TheliaNotification\Model\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use TheliaNotification\Model\Notification as ChildNotification;
use TheliaNotification\Model\NotificationQuery as ChildNotificationQuery;
use TheliaNotification\Model\Map\NotificationTableMap;

/**
 * Base class that represents a query for the 'notification' table.
 *
 *
 *
 * @method     ChildNotificationQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildNotificationQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildNotificationQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildNotificationQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     ChildNotificationQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     ChildNotificationQuery orderByMessage($order = Criteria::ASC) Order by the message column
 * @method     ChildNotificationQuery orderByMessageType($order = Criteria::ASC) Order by the message_type column
 * @method     ChildNotificationQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildNotificationQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildNotificationQuery groupById() Group by the id column
 * @method     ChildNotificationQuery groupByCode() Group by the code column
 * @method     ChildNotificationQuery groupByType() Group by the type column
 * @method     ChildNotificationQuery groupByUrl() Group by the url column
 * @method     ChildNotificationQuery groupByTitle() Group by the title column
 * @method     ChildNotificationQuery groupByMessage() Group by the message column
 * @method     ChildNotificationQuery groupByMessageType() Group by the message_type column
 * @method     ChildNotificationQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildNotificationQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildNotificationQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNotificationQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNotificationQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNotificationQuery leftJoinNotificationCustomer($relationAlias = null) Adds a LEFT JOIN clause to the query using the NotificationCustomer relation
 * @method     ChildNotificationQuery rightJoinNotificationCustomer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the NotificationCustomer relation
 * @method     ChildNotificationQuery innerJoinNotificationCustomer($relationAlias = null) Adds a INNER JOIN clause to the query using the NotificationCustomer relation
 *
 * @method     ChildNotificationQuery leftJoinNotificationAdmin($relationAlias = null) Adds a LEFT JOIN clause to the query using the NotificationAdmin relation
 * @method     ChildNotificationQuery rightJoinNotificationAdmin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the NotificationAdmin relation
 * @method     ChildNotificationQuery innerJoinNotificationAdmin($relationAlias = null) Adds a INNER JOIN clause to the query using the NotificationAdmin relation
 *
 * @method     ChildNotification findOne(ConnectionInterface $con = null) Return the first ChildNotification matching the query
 * @method     ChildNotification findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNotification matching the query, or a new ChildNotification object populated from the query conditions when no match is found
 *
 * @method     ChildNotification findOneById(int $id) Return the first ChildNotification filtered by the id column
 * @method     ChildNotification findOneByCode(string $code) Return the first ChildNotification filtered by the code column
 * @method     ChildNotification findOneByType(int $type) Return the first ChildNotification filtered by the type column
 * @method     ChildNotification findOneByUrl(string $url) Return the first ChildNotification filtered by the url column
 * @method     ChildNotification findOneByTitle(string $title) Return the first ChildNotification filtered by the title column
 * @method     ChildNotification findOneByMessage(string $message) Return the first ChildNotification filtered by the message column
 * @method     ChildNotification findOneByMessageType(string $message_type) Return the first ChildNotification filtered by the message_type column
 * @method     ChildNotification findOneByCreatedAt(string $created_at) Return the first ChildNotification filtered by the created_at column
 * @method     ChildNotification findOneByUpdatedAt(string $updated_at) Return the first ChildNotification filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildNotification objects filtered by the id column
 * @method     array findByCode(string $code) Return ChildNotification objects filtered by the code column
 * @method     array findByType(int $type) Return ChildNotification objects filtered by the type column
 * @method     array findByUrl(string $url) Return ChildNotification objects filtered by the url column
 * @method     array findByTitle(string $title) Return ChildNotification objects filtered by the title column
 * @method     array findByMessage(string $message) Return ChildNotification objects filtered by the message column
 * @method     array findByMessageType(string $message_type) Return ChildNotification objects filtered by the message_type column
 * @method     array findByCreatedAt(string $created_at) Return ChildNotification objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildNotification objects filtered by the updated_at column
 *
 */
abstract class NotificationQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \TheliaNotification\Model\Base\NotificationQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\TheliaNotification\\Model\\Notification', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNotificationQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNotificationQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \TheliaNotification\Model\NotificationQuery) {
            return $criteria;
        }
        $query = new \TheliaNotification\Model\NotificationQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildNotification|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = NotificationTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NotificationTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildNotification A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CODE, TYPE, URL, TITLE, MESSAGE, MESSAGE_TYPE, CREATED_AT, UPDATED_AT FROM notification WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildNotification();
            $obj->hydrate($row);
            NotificationTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildNotification|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(NotificationTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(NotificationTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(NotificationTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(NotificationTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * @param     mixed $type The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        $valueSet = NotificationTableMap::getValueSet(NotificationTableMap::TYPE);
        if (is_scalar($type)) {
            if (!in_array($type, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $type));
            }
            $type = array_search($type, $valueSet);
        } elseif (is_array($type)) {
            $convertedValues = array();
            foreach ($type as $value) {
                if (!in_array($value, $valueSet)) {
                    throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
                }
                $convertedValues []= array_search($value, $valueSet);
            }
            $type = $convertedValues;
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $url)) {
                $url = str_replace('*', '%', $url);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::URL, $url, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the message column
     *
     * Example usage:
     * <code>
     * $query->filterByMessage('fooValue');   // WHERE message = 'fooValue'
     * $query->filterByMessage('%fooValue%'); // WHERE message LIKE '%fooValue%'
     * </code>
     *
     * @param     string $message The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByMessage($message = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($message)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $message)) {
                $message = str_replace('*', '%', $message);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::MESSAGE, $message, $comparison);
    }

    /**
     * Filter the query on the message_type column
     *
     * Example usage:
     * <code>
     * $query->filterByMessageType('fooValue');   // WHERE message_type = 'fooValue'
     * $query->filterByMessageType('%fooValue%'); // WHERE message_type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $messageType The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByMessageType($messageType = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($messageType)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $messageType)) {
                $messageType = str_replace('*', '%', $messageType);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::MESSAGE_TYPE, $messageType, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(NotificationTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(NotificationTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(NotificationTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(NotificationTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \TheliaNotification\Model\NotificationCustomer object
     *
     * @param \TheliaNotification\Model\NotificationCustomer|ObjectCollection $notificationCustomer  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByNotificationCustomer($notificationCustomer, $comparison = null)
    {
        if ($notificationCustomer instanceof \TheliaNotification\Model\NotificationCustomer) {
            return $this
                ->addUsingAlias(NotificationTableMap::ID, $notificationCustomer->getNotificationId(), $comparison);
        } elseif ($notificationCustomer instanceof ObjectCollection) {
            return $this
                ->useNotificationCustomerQuery()
                ->filterByPrimaryKeys($notificationCustomer->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByNotificationCustomer() only accepts arguments of type \TheliaNotification\Model\NotificationCustomer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the NotificationCustomer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function joinNotificationCustomer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('NotificationCustomer');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'NotificationCustomer');
        }

        return $this;
    }

    /**
     * Use the NotificationCustomer relation NotificationCustomer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \TheliaNotification\Model\NotificationCustomerQuery A secondary query class using the current class as primary query
     */
    public function useNotificationCustomerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinNotificationCustomer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'NotificationCustomer', '\TheliaNotification\Model\NotificationCustomerQuery');
    }

    /**
     * Filter the query by a related \TheliaNotification\Model\NotificationAdmin object
     *
     * @param \TheliaNotification\Model\NotificationAdmin|ObjectCollection $notificationAdmin  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function filterByNotificationAdmin($notificationAdmin, $comparison = null)
    {
        if ($notificationAdmin instanceof \TheliaNotification\Model\NotificationAdmin) {
            return $this
                ->addUsingAlias(NotificationTableMap::ID, $notificationAdmin->getNotificationId(), $comparison);
        } elseif ($notificationAdmin instanceof ObjectCollection) {
            return $this
                ->useNotificationAdminQuery()
                ->filterByPrimaryKeys($notificationAdmin->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByNotificationAdmin() only accepts arguments of type \TheliaNotification\Model\NotificationAdmin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the NotificationAdmin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function joinNotificationAdmin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('NotificationAdmin');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'NotificationAdmin');
        }

        return $this;
    }

    /**
     * Use the NotificationAdmin relation NotificationAdmin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \TheliaNotification\Model\NotificationAdminQuery A secondary query class using the current class as primary query
     */
    public function useNotificationAdminQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinNotificationAdmin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'NotificationAdmin', '\TheliaNotification\Model\NotificationAdminQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNotification $notification Object to remove from the list of results
     *
     * @return ChildNotificationQuery The current query, for fluid interface
     */
    public function prune($notification = null)
    {
        if ($notification) {
            $this->addUsingAlias(NotificationTableMap::ID, $notification->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the notification table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotificationTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            NotificationTableMap::clearInstancePool();
            NotificationTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildNotification or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildNotification object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotificationTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NotificationTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        NotificationTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            NotificationTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildNotificationQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(NotificationTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildNotificationQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(NotificationTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildNotificationQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(NotificationTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildNotificationQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(NotificationTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildNotificationQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(NotificationTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildNotificationQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(NotificationTableMap::CREATED_AT);
    }

} // NotificationQuery
