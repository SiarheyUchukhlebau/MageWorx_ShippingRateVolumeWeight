<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class FilterRatesCollectionByVolumeWeightAttribute
 *
 * Filter rates collection before we load it by custom attribute: volume weight.
 *
 * For more details
 *
 * @see \MageWorx\ShippingRules\Model\Carrier\Artificial::getSuitableRatesAccordingRequest()
 *
 */
class FilterRatesCollectionByVolumeWeightAttribute implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection $collection */
        $collection = $observer->getEvent()->getData('rates_collection');
        if (!$collection instanceof \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection) {
            return;
        }

        /** @var \Magento\Quote\Model\Quote\Address\RateRequest $request */
        $request = $observer->getEvent()->getData('request');
        if (!$request instanceof \Magento\Quote\Model\Quote\Address\RateRequest) {
            return;
        }

        /** @var \Magento\Quote\Model\Quote\Item[] $items */
        $items        = $request->getAllItems() ?? [];
        $volumeWeight = 0;
        foreach ($items as $item) {
            $volumeWeight += (float)$item->getProduct()->getData('volume_weight') * $item->getQty();
        }

        $joinTable = $collection->getTable('mageworx_shippingrules_rates_volume_weight');
        $collection->getSelect()
                   ->joinLeft(
                       ['vv' => $joinTable],
                       '`main_table`.`rate_id` = `vv`.`rate_id`',
                       ['volume_weight_from', 'volume_weight_to']
                   );

        $collection->getSelect()->where(
            "(`vv`.`volume_weight_from` <= ?) OR (`vv`.`volume_weight_from` IS NULL)) 
            AND 
            ((`vv`.`volume_weight_to` >= ?) OR (`vv`.`volume_weight_to` IS NULL)",
            $volumeWeight
        );
    }
}
