<?php
namespace TheliaNotification\Entity;

use Propel\Runtime\Collection\ObjectCollection;
use Thelia\Model\Admin;
use Thelia\Model\Customer;

class NotificationEntity
{
    const MESSAGE_TYPE_HTML = 'html';
    const MESSAGE_TYPE_TEXT = 'text';

    const TYPE_DEFAULT = 'default';
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    /** @var string */
    protected $code;

    /** @var Customer[] */
    protected $customers = [];

    /** @var Admin[] */
    protected $admins = [];

    /** @var string[] */
    protected $emails = [];

    /** @var string */
    protected $message;

    /** @var string */
    protected $messageType = self::MESSAGE_TYPE_TEXT;

    /** @var string */
    protected $title;

    /** @var string */
    protected $url;

    /** @var bool */
    protected $byEmail = false;

    /** @var string */
    protected $type = self::TYPE_DEFAULT;

    public function __construct($code)
    {
        $this->setCode($code);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return NotificationEntity
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return Customer[]
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @return Admin[]
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * @return string[]
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param array|ObjectCollection $customers
     * @return $this
     */
    public function toCustomers($customers)
    {
        foreach ($customers as $customer) {
            if (!($customer instanceof Customer)) {
                throw new \InvalidArgumentException();
            }
        }
        $this->customers = $customers;
        return $this;
    }

    /**
     * @param array|ObjectCollection $admins
     * @return $this
     */
    public function toAdmins($admins)
    {
        foreach ($admins as $admin) {
            if (!($admin instanceof Admin)) {
                throw new \InvalidArgumentException();
            }
        }
        $this->admins = $admins;
        return $this;
    }

    /**
     * @param array $emails
     * @return $this
     */
    public function toEmails(array $emails)
    {
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException();
            }
        }
        $this->emails = $emails;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return NotificationEntity
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function appendMessage($message)
    {
        $this->message .= $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return NotificationEntity
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return NotificationEntity
     */
    public function setUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException();
        }

        $this->url = (string) $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType
     * @return NotificationEntity
     */
    public function setMessageType($messageType)
    {
        if (!in_array($messageType, [
            self::MESSAGE_TYPE_HTML,
            self::MESSAGE_TYPE_TEXT
        ])) {
            throw new \InvalidArgumentException();
        }

        $this->messageType = $messageType;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isByEmail()
    {
        return $this->byEmail;
    }

    /**
     * @param boolean $byEmail
     * @return NotificationEntity
     */
    public function setByEmail($byEmail)
    {
        $this->byEmail = (bool) $byEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return NotificationEntity
     */
    public function setType($type)
    {
        if (!in_array($type, [
            self::TYPE_DEFAULT,
            self::TYPE_INFO,
            self::TYPE_SUCCESS,
            self::TYPE_WARNING,
            self::TYPE_DANGER
        ])) {
            throw new \InvalidArgumentException();
        }

        $this->type = $type;
        return $this;
    }
}
