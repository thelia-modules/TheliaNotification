<?php

namespace TheliaNotification\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;
use TheliaNotification\Model\Notification as ChildNotification;
use TheliaNotification\Model\NotificationAdmin as ChildNotificationAdmin;
use TheliaNotification\Model\NotificationAdminQuery as ChildNotificationAdminQuery;
use TheliaNotification\Model\NotificationCustomer as ChildNotificationCustomer;
use TheliaNotification\Model\NotificationCustomerQuery as ChildNotificationCustomerQuery;
use TheliaNotification\Model\NotificationQuery as ChildNotificationQuery;
use TheliaNotification\Model\Map\NotificationTableMap;

abstract class Notification implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\TheliaNotification\\Model\\Map\\NotificationTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the code field.
     * @var        string
     */
    protected $code;

    /**
     * The value for the type field.
     * @var        int
     */
    protected $type;

    /**
     * The value for the url field.
     * @var        string
     */
    protected $url;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the message field.
     * @var        string
     */
    protected $message;

    /**
     * The value for the message_type field.
     * @var        string
     */
    protected $message_type;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        ObjectCollection|ChildNotificationCustomer[] Collection to store aggregation of ChildNotificationCustomer objects.
     */
    protected $collNotificationCustomers;
    protected $collNotificationCustomersPartial;

    /**
     * @var        ObjectCollection|ChildNotificationAdmin[] Collection to store aggregation of ChildNotificationAdmin objects.
     */
    protected $collNotificationAdmins;
    protected $collNotificationAdminsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $notificationCustomersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $notificationAdminsScheduledForDeletion = null;

    /**
     * Initializes internal state of TheliaNotification\Model\Base\Notification object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Notification</code> instance.  If
     * <code>obj</code> is an instance of <code>Notification</code>, delegates to
     * <code>equals(Notification)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Notification The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Notification The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [code] column value.
     *
     * @return   string
     */
    public function getCode()
    {

        return $this->code;
    }

    /**
     * Get the [type] column value.
     *
     * @return   int
     */
    public function getType()
    {
        if (null === $this->type) {
            return null;
        }
        $valueSet = NotificationTableMap::getValueSet(NotificationTableMap::TYPE);
        if (!isset($valueSet[$this->type])) {
            throw new PropelException('Unknown stored enum key: ' . $this->type);
        }

        return $valueSet[$this->type];
    }

    /**
     * Get the [url] column value.
     *
     * @return   string
     */
    public function getUrl()
    {

        return $this->url;
    }

    /**
     * Get the [title] column value.
     *
     * @return   string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [message] column value.
     *
     * @return   string
     */
    public function getMessage()
    {

        return $this->message;
    }

    /**
     * Get the [message_type] column value.
     *
     * @return   string
     */
    public function getMessageType()
    {

        return $this->message_type;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[NotificationTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [code] column.
     *
     * @param      string $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->code !== $v) {
            $this->code = $v;
            $this->modifiedColumns[NotificationTableMap::CODE] = true;
        }


        return $this;
    } // setCode()

    /**
     * Set the value of [type] column.
     *
     * @param      int $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $valueSet = NotificationTableMap::getValueSet(NotificationTableMap::TYPE);
            if (!in_array($v, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $v));
            }
            $v = array_search($v, $valueSet);
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[NotificationTableMap::TYPE] = true;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [url] column.
     *
     * @param      string $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[NotificationTableMap::URL] = true;
        }


        return $this;
    } // setUrl()

    /**
     * Set the value of [title] column.
     *
     * @param      string $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[NotificationTableMap::TITLE] = true;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [message] column.
     *
     * @param      string $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setMessage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->message !== $v) {
            $this->message = $v;
            $this->modifiedColumns[NotificationTableMap::MESSAGE] = true;
        }


        return $this;
    } // setMessage()

    /**
     * Set the value of [message_type] column.
     *
     * @param      string $v new value
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setMessageType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->message_type !== $v) {
            $this->message_type = $v;
            $this->modifiedColumns[NotificationTableMap::MESSAGE_TYPE] = true;
        }


        return $this;
    } // setMessageType()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[NotificationTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[NotificationTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : NotificationTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : NotificationTableMap::translateFieldName('Code', TableMap::TYPE_PHPNAME, $indexType)];
            $this->code = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : NotificationTableMap::translateFieldName('Type', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : NotificationTableMap::translateFieldName('Url', TableMap::TYPE_PHPNAME, $indexType)];
            $this->url = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : NotificationTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : NotificationTableMap::translateFieldName('Message', TableMap::TYPE_PHPNAME, $indexType)];
            $this->message = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : NotificationTableMap::translateFieldName('MessageType', TableMap::TYPE_PHPNAME, $indexType)];
            $this->message_type = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : NotificationTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : NotificationTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 9; // 9 = NotificationTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \TheliaNotification\Model\Notification object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(NotificationTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildNotificationQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collNotificationCustomers = null;

            $this->collNotificationAdmins = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Notification::setDeleted()
     * @see Notification::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotificationTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildNotificationQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(NotificationTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(NotificationTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(NotificationTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(NotificationTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                NotificationTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->notificationCustomersScheduledForDeletion !== null) {
                if (!$this->notificationCustomersScheduledForDeletion->isEmpty()) {
                    \TheliaNotification\Model\NotificationCustomerQuery::create()
                        ->filterByPrimaryKeys($this->notificationCustomersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->notificationCustomersScheduledForDeletion = null;
                }
            }

                if ($this->collNotificationCustomers !== null) {
            foreach ($this->collNotificationCustomers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->notificationAdminsScheduledForDeletion !== null) {
                if (!$this->notificationAdminsScheduledForDeletion->isEmpty()) {
                    \TheliaNotification\Model\NotificationAdminQuery::create()
                        ->filterByPrimaryKeys($this->notificationAdminsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->notificationAdminsScheduledForDeletion = null;
                }
            }

                if ($this->collNotificationAdmins !== null) {
            foreach ($this->collNotificationAdmins as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[NotificationTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . NotificationTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(NotificationTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(NotificationTableMap::CODE)) {
            $modifiedColumns[':p' . $index++]  = 'CODE';
        }
        if ($this->isColumnModified(NotificationTableMap::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE';
        }
        if ($this->isColumnModified(NotificationTableMap::URL)) {
            $modifiedColumns[':p' . $index++]  = 'URL';
        }
        if ($this->isColumnModified(NotificationTableMap::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE';
        }
        if ($this->isColumnModified(NotificationTableMap::MESSAGE)) {
            $modifiedColumns[':p' . $index++]  = 'MESSAGE';
        }
        if ($this->isColumnModified(NotificationTableMap::MESSAGE_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'MESSAGE_TYPE';
        }
        if ($this->isColumnModified(NotificationTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(NotificationTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO notification (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'CODE':
                        $stmt->bindValue($identifier, $this->code, PDO::PARAM_STR);
                        break;
                    case 'TYPE':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_INT);
                        break;
                    case 'URL':
                        $stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case 'TITLE':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'MESSAGE':
                        $stmt->bindValue($identifier, $this->message, PDO::PARAM_STR);
                        break;
                    case 'MESSAGE_TYPE':
                        $stmt->bindValue($identifier, $this->message_type, PDO::PARAM_STR);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = NotificationTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getCode();
                break;
            case 2:
                return $this->getType();
                break;
            case 3:
                return $this->getUrl();
                break;
            case 4:
                return $this->getTitle();
                break;
            case 5:
                return $this->getMessage();
                break;
            case 6:
                return $this->getMessageType();
                break;
            case 7:
                return $this->getCreatedAt();
                break;
            case 8:
                return $this->getUpdatedAt();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Notification'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Notification'][$this->getPrimaryKey()] = true;
        $keys = NotificationTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCode(),
            $keys[2] => $this->getType(),
            $keys[3] => $this->getUrl(),
            $keys[4] => $this->getTitle(),
            $keys[5] => $this->getMessage(),
            $keys[6] => $this->getMessageType(),
            $keys[7] => $this->getCreatedAt(),
            $keys[8] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collNotificationCustomers) {
                $result['NotificationCustomers'] = $this->collNotificationCustomers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collNotificationAdmins) {
                $result['NotificationAdmins'] = $this->collNotificationAdmins->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = NotificationTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setCode($value);
                break;
            case 2:
                $valueSet = NotificationTableMap::getValueSet(NotificationTableMap::TYPE);
                if (isset($valueSet[$value])) {
                    $value = $valueSet[$value];
                }
                $this->setType($value);
                break;
            case 3:
                $this->setUrl($value);
                break;
            case 4:
                $this->setTitle($value);
                break;
            case 5:
                $this->setMessage($value);
                break;
            case 6:
                $this->setMessageType($value);
                break;
            case 7:
                $this->setCreatedAt($value);
                break;
            case 8:
                $this->setUpdatedAt($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = NotificationTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCode($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setType($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setUrl($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setTitle($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setMessage($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setMessageType($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setCreatedAt($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setUpdatedAt($arr[$keys[8]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(NotificationTableMap::DATABASE_NAME);

        if ($this->isColumnModified(NotificationTableMap::ID)) $criteria->add(NotificationTableMap::ID, $this->id);
        if ($this->isColumnModified(NotificationTableMap::CODE)) $criteria->add(NotificationTableMap::CODE, $this->code);
        if ($this->isColumnModified(NotificationTableMap::TYPE)) $criteria->add(NotificationTableMap::TYPE, $this->type);
        if ($this->isColumnModified(NotificationTableMap::URL)) $criteria->add(NotificationTableMap::URL, $this->url);
        if ($this->isColumnModified(NotificationTableMap::TITLE)) $criteria->add(NotificationTableMap::TITLE, $this->title);
        if ($this->isColumnModified(NotificationTableMap::MESSAGE)) $criteria->add(NotificationTableMap::MESSAGE, $this->message);
        if ($this->isColumnModified(NotificationTableMap::MESSAGE_TYPE)) $criteria->add(NotificationTableMap::MESSAGE_TYPE, $this->message_type);
        if ($this->isColumnModified(NotificationTableMap::CREATED_AT)) $criteria->add(NotificationTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(NotificationTableMap::UPDATED_AT)) $criteria->add(NotificationTableMap::UPDATED_AT, $this->updated_at);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(NotificationTableMap::DATABASE_NAME);
        $criteria->add(NotificationTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \TheliaNotification\Model\Notification (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCode($this->getCode());
        $copyObj->setType($this->getType());
        $copyObj->setUrl($this->getUrl());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setMessage($this->getMessage());
        $copyObj->setMessageType($this->getMessageType());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getNotificationCustomers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotificationCustomer($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getNotificationAdmins() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addNotificationAdmin($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \TheliaNotification\Model\Notification Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('NotificationCustomer' == $relationName) {
            return $this->initNotificationCustomers();
        }
        if ('NotificationAdmin' == $relationName) {
            return $this->initNotificationAdmins();
        }
    }

    /**
     * Clears out the collNotificationCustomers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addNotificationCustomers()
     */
    public function clearNotificationCustomers()
    {
        $this->collNotificationCustomers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collNotificationCustomers collection loaded partially.
     */
    public function resetPartialNotificationCustomers($v = true)
    {
        $this->collNotificationCustomersPartial = $v;
    }

    /**
     * Initializes the collNotificationCustomers collection.
     *
     * By default this just sets the collNotificationCustomers collection to an empty array (like clearcollNotificationCustomers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotificationCustomers($overrideExisting = true)
    {
        if (null !== $this->collNotificationCustomers && !$overrideExisting) {
            return;
        }
        $this->collNotificationCustomers = new ObjectCollection();
        $this->collNotificationCustomers->setModel('\TheliaNotification\Model\NotificationCustomer');
    }

    /**
     * Gets an array of ChildNotificationCustomer objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildNotification is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildNotificationCustomer[] List of ChildNotificationCustomer objects
     * @throws PropelException
     */
    public function getNotificationCustomers($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collNotificationCustomersPartial && !$this->isNew();
        if (null === $this->collNotificationCustomers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotificationCustomers) {
                // return empty collection
                $this->initNotificationCustomers();
            } else {
                $collNotificationCustomers = ChildNotificationCustomerQuery::create(null, $criteria)
                    ->filterByNotification($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collNotificationCustomersPartial && count($collNotificationCustomers)) {
                        $this->initNotificationCustomers(false);

                        foreach ($collNotificationCustomers as $obj) {
                            if (false == $this->collNotificationCustomers->contains($obj)) {
                                $this->collNotificationCustomers->append($obj);
                            }
                        }

                        $this->collNotificationCustomersPartial = true;
                    }

                    reset($collNotificationCustomers);

                    return $collNotificationCustomers;
                }

                if ($partial && $this->collNotificationCustomers) {
                    foreach ($this->collNotificationCustomers as $obj) {
                        if ($obj->isNew()) {
                            $collNotificationCustomers[] = $obj;
                        }
                    }
                }

                $this->collNotificationCustomers = $collNotificationCustomers;
                $this->collNotificationCustomersPartial = false;
            }
        }

        return $this->collNotificationCustomers;
    }

    /**
     * Sets a collection of NotificationCustomer objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $notificationCustomers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildNotification The current object (for fluent API support)
     */
    public function setNotificationCustomers(Collection $notificationCustomers, ConnectionInterface $con = null)
    {
        $notificationCustomersToDelete = $this->getNotificationCustomers(new Criteria(), $con)->diff($notificationCustomers);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->notificationCustomersScheduledForDeletion = clone $notificationCustomersToDelete;

        foreach ($notificationCustomersToDelete as $notificationCustomerRemoved) {
            $notificationCustomerRemoved->setNotification(null);
        }

        $this->collNotificationCustomers = null;
        foreach ($notificationCustomers as $notificationCustomer) {
            $this->addNotificationCustomer($notificationCustomer);
        }

        $this->collNotificationCustomers = $notificationCustomers;
        $this->collNotificationCustomersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related NotificationCustomer objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related NotificationCustomer objects.
     * @throws PropelException
     */
    public function countNotificationCustomers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collNotificationCustomersPartial && !$this->isNew();
        if (null === $this->collNotificationCustomers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotificationCustomers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getNotificationCustomers());
            }

            $query = ChildNotificationCustomerQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByNotification($this)
                ->count($con);
        }

        return count($this->collNotificationCustomers);
    }

    /**
     * Method called to associate a ChildNotificationCustomer object to this object
     * through the ChildNotificationCustomer foreign key attribute.
     *
     * @param    ChildNotificationCustomer $l ChildNotificationCustomer
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function addNotificationCustomer(ChildNotificationCustomer $l)
    {
        if ($this->collNotificationCustomers === null) {
            $this->initNotificationCustomers();
            $this->collNotificationCustomersPartial = true;
        }

        if (!in_array($l, $this->collNotificationCustomers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddNotificationCustomer($l);
        }

        return $this;
    }

    /**
     * @param NotificationCustomer $notificationCustomer The notificationCustomer object to add.
     */
    protected function doAddNotificationCustomer($notificationCustomer)
    {
        $this->collNotificationCustomers[]= $notificationCustomer;
        $notificationCustomer->setNotification($this);
    }

    /**
     * @param  NotificationCustomer $notificationCustomer The notificationCustomer object to remove.
     * @return ChildNotification The current object (for fluent API support)
     */
    public function removeNotificationCustomer($notificationCustomer)
    {
        if ($this->getNotificationCustomers()->contains($notificationCustomer)) {
            $this->collNotificationCustomers->remove($this->collNotificationCustomers->search($notificationCustomer));
            if (null === $this->notificationCustomersScheduledForDeletion) {
                $this->notificationCustomersScheduledForDeletion = clone $this->collNotificationCustomers;
                $this->notificationCustomersScheduledForDeletion->clear();
            }
            $this->notificationCustomersScheduledForDeletion[]= clone $notificationCustomer;
            $notificationCustomer->setNotification(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Notification is new, it will return
     * an empty collection; or if this Notification has previously
     * been saved, it will retrieve related NotificationCustomers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Notification.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildNotificationCustomer[] List of ChildNotificationCustomer objects
     */
    public function getNotificationCustomersJoinCustomer($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildNotificationCustomerQuery::create(null, $criteria);
        $query->joinWith('Customer', $joinBehavior);

        return $this->getNotificationCustomers($query, $con);
    }

    /**
     * Clears out the collNotificationAdmins collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addNotificationAdmins()
     */
    public function clearNotificationAdmins()
    {
        $this->collNotificationAdmins = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collNotificationAdmins collection loaded partially.
     */
    public function resetPartialNotificationAdmins($v = true)
    {
        $this->collNotificationAdminsPartial = $v;
    }

    /**
     * Initializes the collNotificationAdmins collection.
     *
     * By default this just sets the collNotificationAdmins collection to an empty array (like clearcollNotificationAdmins());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initNotificationAdmins($overrideExisting = true)
    {
        if (null !== $this->collNotificationAdmins && !$overrideExisting) {
            return;
        }
        $this->collNotificationAdmins = new ObjectCollection();
        $this->collNotificationAdmins->setModel('\TheliaNotification\Model\NotificationAdmin');
    }

    /**
     * Gets an array of ChildNotificationAdmin objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildNotification is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildNotificationAdmin[] List of ChildNotificationAdmin objects
     * @throws PropelException
     */
    public function getNotificationAdmins($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collNotificationAdminsPartial && !$this->isNew();
        if (null === $this->collNotificationAdmins || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collNotificationAdmins) {
                // return empty collection
                $this->initNotificationAdmins();
            } else {
                $collNotificationAdmins = ChildNotificationAdminQuery::create(null, $criteria)
                    ->filterByNotification($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collNotificationAdminsPartial && count($collNotificationAdmins)) {
                        $this->initNotificationAdmins(false);

                        foreach ($collNotificationAdmins as $obj) {
                            if (false == $this->collNotificationAdmins->contains($obj)) {
                                $this->collNotificationAdmins->append($obj);
                            }
                        }

                        $this->collNotificationAdminsPartial = true;
                    }

                    reset($collNotificationAdmins);

                    return $collNotificationAdmins;
                }

                if ($partial && $this->collNotificationAdmins) {
                    foreach ($this->collNotificationAdmins as $obj) {
                        if ($obj->isNew()) {
                            $collNotificationAdmins[] = $obj;
                        }
                    }
                }

                $this->collNotificationAdmins = $collNotificationAdmins;
                $this->collNotificationAdminsPartial = false;
            }
        }

        return $this->collNotificationAdmins;
    }

    /**
     * Sets a collection of NotificationAdmin objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $notificationAdmins A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildNotification The current object (for fluent API support)
     */
    public function setNotificationAdmins(Collection $notificationAdmins, ConnectionInterface $con = null)
    {
        $notificationAdminsToDelete = $this->getNotificationAdmins(new Criteria(), $con)->diff($notificationAdmins);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->notificationAdminsScheduledForDeletion = clone $notificationAdminsToDelete;

        foreach ($notificationAdminsToDelete as $notificationAdminRemoved) {
            $notificationAdminRemoved->setNotification(null);
        }

        $this->collNotificationAdmins = null;
        foreach ($notificationAdmins as $notificationAdmin) {
            $this->addNotificationAdmin($notificationAdmin);
        }

        $this->collNotificationAdmins = $notificationAdmins;
        $this->collNotificationAdminsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related NotificationAdmin objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related NotificationAdmin objects.
     * @throws PropelException
     */
    public function countNotificationAdmins(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collNotificationAdminsPartial && !$this->isNew();
        if (null === $this->collNotificationAdmins || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collNotificationAdmins) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getNotificationAdmins());
            }

            $query = ChildNotificationAdminQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByNotification($this)
                ->count($con);
        }

        return count($this->collNotificationAdmins);
    }

    /**
     * Method called to associate a ChildNotificationAdmin object to this object
     * through the ChildNotificationAdmin foreign key attribute.
     *
     * @param    ChildNotificationAdmin $l ChildNotificationAdmin
     * @return   \TheliaNotification\Model\Notification The current object (for fluent API support)
     */
    public function addNotificationAdmin(ChildNotificationAdmin $l)
    {
        if ($this->collNotificationAdmins === null) {
            $this->initNotificationAdmins();
            $this->collNotificationAdminsPartial = true;
        }

        if (!in_array($l, $this->collNotificationAdmins->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddNotificationAdmin($l);
        }

        return $this;
    }

    /**
     * @param NotificationAdmin $notificationAdmin The notificationAdmin object to add.
     */
    protected function doAddNotificationAdmin($notificationAdmin)
    {
        $this->collNotificationAdmins[]= $notificationAdmin;
        $notificationAdmin->setNotification($this);
    }

    /**
     * @param  NotificationAdmin $notificationAdmin The notificationAdmin object to remove.
     * @return ChildNotification The current object (for fluent API support)
     */
    public function removeNotificationAdmin($notificationAdmin)
    {
        if ($this->getNotificationAdmins()->contains($notificationAdmin)) {
            $this->collNotificationAdmins->remove($this->collNotificationAdmins->search($notificationAdmin));
            if (null === $this->notificationAdminsScheduledForDeletion) {
                $this->notificationAdminsScheduledForDeletion = clone $this->collNotificationAdmins;
                $this->notificationAdminsScheduledForDeletion->clear();
            }
            $this->notificationAdminsScheduledForDeletion[]= clone $notificationAdmin;
            $notificationAdmin->setNotification(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Notification is new, it will return
     * an empty collection; or if this Notification has previously
     * been saved, it will retrieve related NotificationAdmins from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Notification.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildNotificationAdmin[] List of ChildNotificationAdmin objects
     */
    public function getNotificationAdminsJoinAdmin($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildNotificationAdminQuery::create(null, $criteria);
        $query->joinWith('Admin', $joinBehavior);

        return $this->getNotificationAdmins($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->code = null;
        $this->type = null;
        $this->url = null;
        $this->title = null;
        $this->message = null;
        $this->message_type = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collNotificationCustomers) {
                foreach ($this->collNotificationCustomers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collNotificationAdmins) {
                foreach ($this->collNotificationAdmins as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collNotificationCustomers = null;
        $this->collNotificationAdmins = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(NotificationTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildNotification The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[NotificationTableMap::UPDATED_AT] = true;

        return $this;
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
