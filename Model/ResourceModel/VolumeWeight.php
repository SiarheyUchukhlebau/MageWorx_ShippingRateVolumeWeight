<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class VolumeWeight
 */
class VolumeWeight extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_shippingrules_rates_volume_weight', 'rate_id');
    }

    /**
     * @param $rateId
     * @param null $volumeWeightFrom
     * @param null $volumeWeightTo
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertUpdateRecord($rateId, $volumeWeightFrom = null, $volumeWeightTo = null)
    {
        $rowsAffected = $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            [
                'rate_id' => $rateId,
                'volume_weight_from' => $volumeWeightFrom,
                'volume_weight_to' => $volumeWeightTo
            ]
        );

        return $rowsAffected;
    }
}