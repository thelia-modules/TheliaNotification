<?php

namespace TheliaNotification\Service;

use Thelia\Core\Template\ParserInterface;
use Thelia\Core\Translation\Translator;
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
use TheliaNotification\TheliaNotification;

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

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ParserInterface $parser
     * @param MailerFactory $mailer
     */
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
     * @return void
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
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
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
     * @throws \SmartyException
     */
    protected function sendEmail(NotificationEntity $notification, $email, $name, $templateName, array $data = []): void
    {
        $this->parser->pushTemplateDefinition(
            $this->parser->getTemplateHelper()->getActiveMailTemplate(),
            true
        );

        $htmlMessage = $this->parser->render(
            'TheliaNotification/notification-' . $templateName . '.html',
            [
                'message' => $notification->getMessage(),
                'title' => $notification->getTitle(),
                'url' => $notification->getUrl(),
            ]
        );

        $emailMessage = $this->mailer->createSimpleEmailMessage(
            [ConfigQuery::getStoreEmail() => ConfigQuery::getStoreName()],
            [ $email=> $name ],
            $notification->getTitle(),
            $htmlMessage,
            Translator::getInstance()->trans("Please view this message in HTML format", [], TheliaNotification::DOMAIN_NAME)
        );

        $this->mailer->send($emailMessage);

        $this->parser->popTemplateDefinition();
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
