<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddVolumeWeightToRatesCollection
 *
 * Adds custom attribute to the rates collection.
 * It will be used later during quote validation.
 */
class AddVolumeWeightToRatesCollection implements ObserverInterface
{
    /**
     * Join custom table to the rates collection to obtain the voluem_weight attribute anywhere in the code.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection $collection */
        $collection = $observer->getEvent()->getData('collection');

        if (!$collection instanceof \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection) {
            return;
        }

        if ($collection->isLoaded()) {
            return;
        }

        $joinTable = $collection->getTable('mageworx_shippingrules_rates_volume_weight');
        $collection->getSelect()
                   ->joinLeft(
                       $joinTable,
                       '`main_table`.`rate_id` = `' . $joinTable . '`.`rate_id`',
                       ['volume_weight_from', 'volume_weight_to']
                   );
    }
}