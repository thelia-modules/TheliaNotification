<?php

namespace TheliaNotification\Service;

use Thelia\Core\Template\ParserInterface;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\Admin;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Customer;
use TheliaNotification\Entity\NotificationEntity;
use TheliaNotification\Model\Notification as NotificationModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TheliaNotification\Model\Notification;
use TheliaNotification\Model\NotificationAdmin;
use TheliaNotification\Model\NotificationCustomer;

/**
 * Class MailManagerService
 * @package AlphaRHCore\Service\FileManager
 */
class NotificationService
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var ParserInterface */
    protected $parser;

    /** @var MailerFactory */
    protected $mailer;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ParserInterface $parser,
        MailerFactory $mailer
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @param NotificationEntity $notification
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function sendNotification(NotificationEntity $notification)
    {
        if (empty($notification->getTitle())) {
            throw new \InvalidArgumentException('The notification title cannot be empty');
        }

        if (empty($notification->getMessage())) {
            throw new \InvalidArgumentException('The notification message cannot be empty');
        }

        if (count($notification->getCustomers()) || count($notification->getAdmins())) {
            $notificationModel = $this->getGenericNotificationModel($notification);
            $notificationModel->save();

            foreach ($notification->getCustomers() as $customer) {
                $this->linkNotificationToCustomer(
                    $notificationModel,
                    $customer
                );
            }

            foreach ($notification->getAdmins() as $admin) {
                $this->linkNotificationToAdmin(
                    $notificationModel,
                    $admin
                );
            }
        }

        // send  emails
        $this->performNotificationEmail($notification);
    }

    /**
     * @param NotificationEntity $notification
     */
    protected function performNotificationEmail(NotificationEntity $notification)
    {
        if ($notification->isByEmail() || count($notification->getEmails())) {
            foreach ($notification->getCustomers() as $customer) {
                $this->parser->assign('locale', $customer->getLangModel()->getLocale());

                if (filter_var($customer->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $this->sendEmail(
                        $notification,
                        $customer->getEmail(),
                        $customer->getFirstname() . $customer->getLastname(),
                        'customer',
                        ['customer' => $customer, 'notification' => $notification]
                    );
                }
            }

            foreach ($notification->getAdmins() as $admin) {
                $this->parser->assign('locale', $admin->getLocale());

                if (filter_var($admin->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $this->sendEmail(
                        $notification,
                        $admin->getEmail(),
                        $admin->getFirstname() . $admin->getLastname(),
                        'admin',
                        ['admin' => $admin, 'notification' => $notification]
                    );
                }
            }

            foreach ($notification->getEmails() as $email => $name) {
                $this->sendEmail(
                    $notification,
                    $email,
                    $name,
                    'email',
                    ['notification' => $notification]
                );
            }
        }
    }

    /**
     * @param NotificationEntity $notification
     * @param string $email
     * @param string $name
     * @param string $templateName
     * @param array $data
     * @return \Swift_Message
     */
    protected function sendEmail(NotificationEntity $notification, $email, $name, $templateName, array $data = [])
    {
        $instance = \Swift_Message::newInstance();

        $instance->addFrom(ConfigQuery::getStoreEmail());

        $instance->addTo($email, $name);

        $instance->setSubject($notification->getTitle());

        $this->parser->setTemplateDefinition(
            $this->parser->getTemplateHelper()->getActiveMailTemplate(),
            true
        );

        $htmlMessage = $this->parser->render('TheliaNotification/notification-' . $templateName . '.html', $data, true);

        $instance->setBody($htmlMessage, 'text/html');

        $this->mailer->send($instance);
    }

    /**
     * @param NotificationEntity $notification
     * @return Notification
     */
    protected function getGenericNotificationModel(NotificationEntity $notification)
    {
        $notificationModel = new NotificationModel();

        $notificationModel
            ->setCode($notification->getCode())
            ->setTitle($notification->getTitle())
            ->setMessage($notification->getMessage())
            ->setType($notification->getType())
            ->setUrl($notification->getUrl())
            ->setMessageType($notification->getMessageType());

        return $notificationModel;
    }

    /**
     * @param Notification $notification
     * @param Customer $customer
     * @return int
     */
    protected function linkNotificationToCustomer(Notification $notification, Customer $customer)
    {
        return (new NotificationCustomer())
            ->setNotificationId($notification->getId())
            ->setCustomerId($customer->getId())
            ->save();
    }

    /**
     * @param Notification $notification
     * @param Admin $admin
     * @return int
     */
    protected function linkNotificationToAdmin(Notification $notification, Admin $admin)
    {
        return (new NotificationAdmin())
            ->setNotificationId($notification->getId())
            ->setAdminId($admin->getId())
            ->save();
    }
}
