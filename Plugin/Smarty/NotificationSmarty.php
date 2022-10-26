<?php

namespace TheliaNotification\Plugin\Smarty;

use Propel\Runtime\ActiveQuery\Criteria;
use TheliaNotification\Model\NotificationQuery;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Model\Admin;
use Thelia\Model\Customer;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class NotificationSmarty extends AbstractSmartyPlugin
{
    /** @var Request */
    protected $requestStack;

    /**
     * NotificationSmarty constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return SmartyPluginDescriptor[] an array of SmartyPluginDescriptor
     */
    public function getPluginDescriptors()
    {
        return [
            new SmartyPluginDescriptor("function", "get_notification", $this, "getNotification"),
            new SmartyPluginDescriptor("function", "get_nb_notification", $this, "getNbNotification")
        ];
    }

    /**
     * @param array $params
     * @param \Smarty_Internal_Template $smarty
     * @return null
     */
    public function getNotification(array $params, \Smarty_Internal_Template $smarty)
    {
        if ($this->getSession() !== null) {
            $query = $this->buildNotificationQuery($params);

            /** @var Admin $admin */
            /** @var Customer $customer */
            if ($this->getRequest()->fromFront()
                && null !== $customer = $this->getSession()->getCustomerUser()
            ) {
                $query->useNotificationCustomerQuery()
                    ->filterByCustomerId($customer->getId())
                    ->endUse();

                $query->withColumn('NotificationCustomer.READ_DATE', 'read_date');
            } elseif ($this->getRequest()->fromAdmin()
                && null !== $admin = $this->getSession()->getAdminUser()
            ) {
                $query->useNotificationAdminQuery()
                    ->filterByAdminId($admin->getId())
                    ->endUse();

                $query->withColumn('NotificationAdmin.READ_DATE', 'read_date');
            }

            $notifications = $query->limit(10)->find();

            $smarty->assign('notifications', $notifications);
            return null;
        }
    }

    /**
     * @param array $params
     * @param \Smarty_Internal_Template $smarty
     * @return int
     */
    public function getNbNotification(array $params, \Smarty_Internal_Template $smarty)
    {
        if ($this->getSession() !== null) {
            $query = $this->buildNotificationQuery($params);

            /** @var Admin $admin */
            /** @var Customer $customer */
            if ($this->getRequest()->fromFront()
                && null !== $customer = $this->getSession()->getCustomerUser()
            ) {
                $query->useNotificationCustomerQuery()
                    ->filterByCustomerId($customer->getId())
                    ->filterByReadDate(null)
                    ->endUse();

                $query->withColumn('NotificationCustomer.READ_DATE', 'read_date');
            } elseif ($this->getRequest()->fromAdmin()
                && null !== $admin = $this->getSession()->getAdminUser()
            ) {
                $query->useNotificationAdminQuery()
                    ->filterByAdminId($admin->getId())
                    ->filterByReadDate(null)
                    ->endUse();

                $query->withColumn('NotificationAdmin.READ_DATE', 'read_date');
            }

            return $query->count();
        }

        return 0;
    }

    protected function buildNotificationQuery(array $params)
    {
        $query = NotificationQuery::create();

        $codes = explode(',', $this->getParam($params, 'code', ''));

        if (count($codes) && !empty($codes[0])) {
            $query->filterByCode($codes);
        }

        $query->orderByCreatedAt(Criteria::DESC);

        return $query;
    }

    /**
     * @return null|Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @return null|Session
     */
    protected function getSession()
    {
        return $this->getRequest() ? $this->getRequest()->getSession() : null;
    }
}
