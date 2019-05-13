<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class RemoveVolumeWeightBeforeInsert
 */
class RemoveVolumeWeightBeforeInsert implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $dataTransferObject = $observer->getEvent()->getData('data_transfer_object');
        $data = $dataTransferObject->getData('rates_data');
        foreach ($data as $key => &$datum) {
            unset($data[$key]['volume_weight_from']);
            unset($data[$key]['volume_weight_to']);
        }

        $dataTransferObject->setData('rates_data', $data);
    }
}