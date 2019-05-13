<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\Data\RateInterface;

/**
 * Class SaveVolumeWeightRateAttribute
 *
 * Saves custom attribute (`volume_weight`) values after model was saved
 */
class SaveVolumeWeightRateAttribute implements ObserverInterface
{
    /**
     * @var \MageWorx\ShippingRateVolumeWeight\Model\ResourceModel\VolumeWeight
     */
    private $resource;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messagesManager;

    /**
     * SaveVolumeWeightRateAttribute constructor.
     *
     * @param \MageWorx\ShippingRateVolumeWeight\Model\ResourceModel\VolumeWeight $resource
     * @param \Magento\Framework\Message\ManagerInterface $messagesManager
     */
    public function __construct(
        \MageWorx\ShippingRateVolumeWeight\Model\ResourceModel\VolumeWeight $resource,
        \Magento\Framework\Message\ManagerInterface $messagesManager
    ) {
        $this->resource = $resource;
        $this->messagesManager = $messagesManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var RateInterface $model */
        $model = $observer->getEvent()->getData('rate');
        if (!$model instanceof RateInterface) {
            return;
        }

        $volumeWeightFrom = $model->getData('volume_weight_from');
        $volumeWeightTo = $model->getData('volume_weight_to');

        try {
            $this->resource->insertUpdateRecord($model->getRateId(), $volumeWeightFrom, $volumeWeightTo);
        } catch (LocalizedException $exception) {
            $this->messagesManager->addErrorMessage(__('Unable to save the Volume Weight for the Rate %1', $model->getRateId()));
        }

        return;
    }
}