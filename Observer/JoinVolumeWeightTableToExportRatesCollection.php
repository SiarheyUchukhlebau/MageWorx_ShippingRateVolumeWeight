<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection as RateCollection;

/**
 * Class JoinVolumeWeightTableToExportRatesCollection
 *
 * Join table with volume weight attribute to the export rates collection
 */
class JoinVolumeWeightTableToExportRatesCollection implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $collection = $observer->getEvent()->getData('collection');
        if (!$collection instanceof RateCollection) {
            return;
        }

        $joinTable = $collection->getTable('mageworx_shippingrules_rates_volume_weight');
        $select = $collection->getSelect();
        $partsFrom = $select->getPart('from');
        foreach ($partsFrom as $part) {
            if ($part['tableName'] === $joinTable) {
                return;
            }
        }

        $collection->getSelect()
                   ->joinLeft(
                       ['vv' => $joinTable],
                       '`main_table`.`rate_id` = `vv`.`rate_id`',
                       ['volume_weight_from', 'volume_weight_to']
                   );
    }
}