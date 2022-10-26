<?php

namespace TheliaNotification\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Admin;
use Thelia\Model\Customer;
use TheliaNotification\Model\NotificationAdminQuery;
use TheliaNotification\Model\NotificationCustomerQuery;

class NotificationController extends BaseFrontController
{
    public function markReadAction(Request $request, $notificationId)
    {
        /** @var Admin $admin */
        if (null !== $admin = $request->getSession()->getAdminUser()) {
            if (null !== $notificationId = NotificationAdminQuery::create()
                ->filterByAdminId($admin->getId())
                ->filterByNotificationId($notificationId)
                ->findOne()) {
                $notificationId->setReadDate(new \DateTime())->save();
            }
        }

        /** @var Customer $customer */
        if (null !== $customer = $request->getSession()->getAdminUser()) {
            if (null !== $notificationId = NotificationCustomerQuery::create()
                    ->filterByCustomerId($customer->getId())
                    ->filterByNotificationId($notificationId)
                    ->findOne()) {
                $notificationId->setReadDate(new \DateTime())->save();
            }
        }

        return new JsonResponse();
    }

    public function readAllAction(Request $request)
    {
        /** @var Admin $admin */
        if (null !== $admin = $request->getSession()->getAdminUser()) {
            if (null !== $notifications = NotificationAdminQuery::create()
                    ->filterByAdminId($admin->getId())
                    ->filterByReadDate(null)
                    ->find()) {
                foreach ($notifications as $notification) {
                    $notification->setReadDate(new \DateTime())->save();
                }
            }
        }

        /** @var Customer $customer */
        if (null !== $customer = $request->getSession()->getAdminUser()) {
            if (null !== $notifications = NotificationCustomerQuery::create()
                    ->filterByCustomerId($customer->getId())
                    ->filterByReadDate(null)
                    ->find()) {
                foreach ($notifications as $notification) {
                    $notification->setReadDate(new \DateTime())->save();
                }
            }
        }

        return new JsonResponse();
    }
}
