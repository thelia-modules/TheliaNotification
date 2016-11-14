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
use TheliaNotification\Model\NotificationAdmin as ChildNotificationAdmin;
use TheliaNotification\Model\NotificationAdminQuery as ChildNotificationAdminQuery;
use TheliaNotification\Model\Map\NotificationAdminTableMap;
use Thelia\Model\Admin;

/**
 * Base class that represents a query for the 'notification_admin' table.
 *
 *
 *
 * @method     ChildNotificationAdminQuery orderByNotificationId($order = Criteria::ASC) Order by the notification_id column
 * @method     ChildNotificationAdminQuery orderByAdminId($order = Criteria::ASC) Order by the admin_id column
 * @method     ChildNotificationAdminQuery orderByReadDate($order = Criteria::ASC) Order by the read_date column
 * @method     ChildNotificationAdminQuery orderByHide($order = Criteria::ASC) Order by the hide column
 *
 * @method     ChildNotificationAdminQuery groupByNotificationId() Group by the notification_id column
 * @method     ChildNotificationAdminQuery groupByAdminId() Group by the admin_id column
 * @method     ChildNotificationAdminQuery groupByReadDate() Group by the read_date column
 * @method     ChildNotificationAdminQuery groupByHide() Group by the hide column
 *
 * @method     ChildNotificationAdminQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildNotificationAdminQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildNotificationAdminQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildNotificationAdminQuery leftJoinNotification($relationAlias = null) Adds a LEFT JOIN clause to the query using the Notification relation
 * @method     ChildNotificationAdminQuery rightJoinNotification($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Notification relation
 * @method     ChildNotificationAdminQuery innerJoinNotification($relationAlias = null) Adds a INNER JOIN clause to the query using the Notification relation
 *
 * @method     ChildNotificationAdminQuery leftJoinAdmin($relationAlias = null) Adds a LEFT JOIN clause to the query using the Admin relation
 * @method     ChildNotificationAdminQuery rightJoinAdmin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Admin relation
 * @method     ChildNotificationAdminQuery innerJoinAdmin($relationAlias = null) Adds a INNER JOIN clause to the query using the Admin relation
 *
 * @method     ChildNotificationAdmin findOne(ConnectionInterface $con = null) Return the first ChildNotificationAdmin matching the query
 * @method     ChildNotificationAdmin findOneOrCreate(ConnectionInterface $con = null) Return the first ChildNotificationAdmin matching the query, or a new ChildNotificationAdmin object populated from the query conditions when no match is found
 *
 * @method     ChildNotificationAdmin findOneByNotificationId(int $notification_id) Return the first ChildNotificationAdmin filtered by the notification_id column
 * @method     ChildNotificationAdmin findOneByAdminId(int $admin_id) Return the first ChildNotificationAdmin filtered by the admin_id column
 * @method     ChildNotificationAdmin findOneByReadDate(string $read_date) Return the first ChildNotificationAdmin filtered by the read_date column
 * @method     ChildNotificationAdmin findOneByHide(boolean $hide) Return the first ChildNotificationAdmin filtered by the hide column
 *
 * @method     array findByNotificationId(int $notification_id) Return ChildNotificationAdmin objects filtered by the notification_id column
 * @method     array findByAdminId(int $admin_id) Return ChildNotificationAdmin objects filtered by the admin_id column
 * @method     array findByReadDate(string $read_date) Return ChildNotificationAdmin objects filtered by the read_date column
 * @method     array findByHide(boolean $hide) Return ChildNotificationAdmin objects filtered by the hide column
 *
 */
abstract class NotificationAdminQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \TheliaNotification\Model\Base\NotificationAdminQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\TheliaNotification\\Model\\NotificationAdmin', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildNotificationAdminQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildNotificationAdminQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \TheliaNotification\Model\NotificationAdminQuery) {
            return $criteria;
        }
        $query = new \TheliaNotification\Model\NotificationAdminQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$notification_id, $admin_id] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildNotificationAdmin|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = NotificationAdminTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NotificationAdminTableMap::DATABASE_NAME);
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
     * @return   ChildNotificationAdmin A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT NOTIFICATION_ID, ADMIN_ID, READ_DATE, HIDE FROM notification_admin WHERE NOTIFICATION_ID = :p0 AND ADMIN_ID = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildNotificationAdmin();
            $obj->hydrate($row);
            NotificationAdminTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildNotificationAdmin|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(NotificationAdminTableMap::NOTIFICATION_ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(NotificationAdminTableMap::ADMIN_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(NotificationAdminTableMap::NOTIFICATION_ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(NotificationAdminTableMap::ADMIN_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the notification_id column
     *
     * Example usage:
     * <code>
     * $query->filterByNotificationId(1234); // WHERE notification_id = 1234
     * $query->filterByNotificationId(array(12, 34)); // WHERE notification_id IN (12, 34)
     * $query->filterByNotificationId(array('min' => 12)); // WHERE notification_id > 12
     * </code>
     *
     * @see       filterByNotification()
     *
     * @param     mixed $notificationId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByNotificationId($notificationId = null, $comparison = null)
    {
        if (is_array($notificationId)) {
            $useMinMax = false;
            if (isset($notificationId['min'])) {
                $this->addUsingAlias(NotificationAdminTableMap::NOTIFICATION_ID, $notificationId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($notificationId['max'])) {
                $this->addUsingAlias(NotificationAdminTableMap::NOTIFICATION_ID, $notificationId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationAdminTableMap::NOTIFICATION_ID, $notificationId, $comparison);
    }

    /**
     * Filter the query on the admin_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAdminId(1234); // WHERE admin_id = 1234
     * $query->filterByAdminId(array(12, 34)); // WHERE admin_id IN (12, 34)
     * $query->filterByAdminId(array('min' => 12)); // WHERE admin_id > 12
     * </code>
     *
     * @see       filterByAdmin()
     *
     * @param     mixed $adminId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByAdminId($adminId = null, $comparison = null)
    {
        if (is_array($adminId)) {
            $useMinMax = false;
            if (isset($adminId['min'])) {
                $this->addUsingAlias(NotificationAdminTableMap::ADMIN_ID, $adminId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($adminId['max'])) {
                $this->addUsingAlias(NotificationAdminTableMap::ADMIN_ID, $adminId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationAdminTableMap::ADMIN_ID, $adminId, $comparison);
    }

    /**
     * Filter the query on the read_date column
     *
     * Example usage:
     * <code>
     * $query->filterByReadDate('2011-03-14'); // WHERE read_date = '2011-03-14'
     * $query->filterByReadDate('now'); // WHERE read_date = '2011-03-14'
     * $query->filterByReadDate(array('max' => 'yesterday')); // WHERE read_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $readDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByReadDate($readDate = null, $comparison = null)
    {
        if (is_array($readDate)) {
            $useMinMax = false;
            if (isset($readDate['min'])) {
                $this->addUsingAlias(NotificationAdminTableMap::READ_DATE, $readDate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($readDate['max'])) {
                $this->addUsingAlias(NotificationAdminTableMap::READ_DATE, $readDate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(NotificationAdminTableMap::READ_DATE, $readDate, $comparison);
    }

    /**
     * Filter the query on the hide column
     *
     * Example usage:
     * <code>
     * $query->filterByHide(true); // WHERE hide = true
     * $query->filterByHide('yes'); // WHERE hide = true
     * </code>
     *
     * @param     boolean|string $hide The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByHide($hide = null, $comparison = null)
    {
        if (is_string($hide)) {
            $hide = in_array(strtolower($hide), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(NotificationAdminTableMap::HIDE, $hide, $comparison);
    }

    /**
     * Filter the query by a related \TheliaNotification\Model\Notification object
     *
     * @param \TheliaNotification\Model\Notification|ObjectCollection $notification The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByNotification($notification, $comparison = null)
    {
        if ($notification instanceof \TheliaNotification\Model\Notification) {
            return $this
                ->addUsingAlias(NotificationAdminTableMap::NOTIFICATION_ID, $notification->getId(), $comparison);
        } elseif ($notification instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NotificationAdminTableMap::NOTIFICATION_ID, $notification->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByNotification() only accepts arguments of type \TheliaNotification\Model\Notification or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Notification relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function joinNotification($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Notification');

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
            $this->addJoinObject($join, 'Notification');
        }

        return $this;
    }

    /**
     * Use the Notification relation Notification object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \TheliaNotification\Model\NotificationQuery A secondary query class using the current class as primary query
     */
    public function useNotificationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinNotification($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Notification', '\TheliaNotification\Model\NotificationQuery');
    }

    /**
     * Filter the query by a related \Thelia\Model\Admin object
     *
     * @param \Thelia\Model\Admin|ObjectCollection $admin The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function filterByAdmin($admin, $comparison = null)
    {
        if ($admin instanceof \Thelia\Model\Admin) {
            return $this
                ->addUsingAlias(NotificationAdminTableMap::ADMIN_ID, $admin->getId(), $comparison);
        } elseif ($admin instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(NotificationAdminTableMap::ADMIN_ID, $admin->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAdmin() only accepts arguments of type \Thelia\Model\Admin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Admin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function joinAdmin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Admin');

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
            $this->addJoinObject($join, 'Admin');
        }

        return $this;
    }

    /**
     * Use the Admin relation Admin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\AdminQuery A secondary query class using the current class as primary query
     */
    public function useAdminQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAdmin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Admin', '\Thelia\Model\AdminQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildNotificationAdmin $notificationAdmin Object to remove from the list of results
     *
     * @return ChildNotificationAdminQuery The current query, for fluid interface
     */
    public function prune($notificationAdmin = null)
    {
        if ($notificationAdmin) {
            $this->addCond('pruneCond0', $this->getAliasedColName(NotificationAdminTableMap::NOTIFICATION_ID), $notificationAdmin->getNotificationId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(NotificationAdminTableMap::ADMIN_ID), $notificationAdmin->getAdminId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the notification_admin table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotificationAdminTableMap::DATABASE_NAME);
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
            NotificationAdminTableMap::clearInstancePool();
            NotificationAdminTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildNotificationAdmin or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildNotificationAdmin object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(NotificationAdminTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(NotificationAdminTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        NotificationAdminTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            NotificationAdminTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // NotificationAdminQuery
