<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Model;


use Magento\Framework\Model\AbstractModel;

/**
 * Class VolumeWeight
 */
class VolumeWeight extends AbstractModel
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_volume_weight';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'volume_weight';

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRateVolumeWeight\Model\ResourceModel\VolumeWeight');
        $this->setIdFieldName('rate_id');
    }
}