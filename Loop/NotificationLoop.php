<?php

namespace TheliaNotification\Loop;

use AlphaRHCore\Model\ArhCompanyQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use TheliaNotification\Model\Notification;
use TheliaNotification\Model\NotificationQuery;

/**
 *
 * @method int[] getId()
 * @method int[] getAdminId()
 * @method int[] getCustomerId()
 * @method string[] getOrder()
 */
class NotificationLoop extends BaseLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @inheritdoc
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createAnyListTypeArgument('admin_id'),
            Argument::createAnyListTypeArgument('customer_id'),
            Argument::createEnumListTypeArgument(
                'order',
                [
                    'id',
                    'id-reverse',
                    'date',
                    'date-reverse'
                ],
                'date'
            )
        );
    }

    /**
     * @inheritdoc
     * @return ArhCompanyQuery
     */
    public function buildModelCriteria()
    {
        $query = new NotificationQuery();

        if (null !== $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null !== $adminId = $this->getAdminId()) {
            $query->useNotificationAdminQuery()
                ->filterByAdminId($adminId)
                ->endUse();

            $query->withColumn('NotificationAdmin.READ_DATE', 'read_date');
        }

        if (null !== $customerId = $this->getCustomerId()) {
            $query->useNotificationCustomerQuery()
                ->filterByCustomerId()
                ->endUse();

            $query->withColumn('NotificationCustomer.READ_DATE', 'read_date');
        }

        $this->buildModelCriteriaOrder($query);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function parseResults(LoopResult $loopResult)
    {
        /**
         * @var Notification $model
         */
        foreach ($loopResult->getResultDataCollection() as $model) {
            $row = new LoopResultRow($model);

            $row->set('notification', $model);

            $this->addOutputFields($row, $model);

            $loopResult->addRow($row);
        }

        return $loopResult;
    }

    protected function buildModelCriteriaOrder(NotificationQuery $query)
    {
        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'id':
                    $query->orderById();
                    break;
                case 'id-reverse':
                    $query->orderById(Criteria::DESC);
                    break;
                case 'date':
                    $query->orderByCreatedAt();
                    break;
                case 'date-reverse':
                    $query->orderByCreatedAt(Criteria::DESC);
                    break;
                default:
                    break;
            }
        }
    }
}
