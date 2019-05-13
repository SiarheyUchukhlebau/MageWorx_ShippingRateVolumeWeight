<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageWorx\ShippingRules\Model\ImportExport\ExpressImportHandler;
use MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection as RateCollection;
use Magento\Framework\Message\ManagerInterface as MessageManager;

/**
 * Class InsertUpdateVolumeWeightDuringImport
 */
class InsertUpdateVolumeWeightDuringImport implements ObserverInterface
{
    /**
     * @var ExpressImportHandler
     */
    private $importHandler;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * InsertUpdateVolumeWeightDuringImport constructor.
     *
     * @param ExpressImportHandler $expressImportHandler
     * @param MessageManager $messageManager
     */
    public function __construct(
        ExpressImportHandler $expressImportHandler,
        MessageManager $messageManager
    ) {
        $this->importHandler  = $expressImportHandler;
        $this->messageManager = $messageManager;
    }

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

        $data = $observer->getEvent()->getData('data');
        if (empty($data)) {
            return;
        }

        if (!isset($data[0]['volume_weight_to']) && !isset($data[0]['volume_weight_from'])) {
            // The is no Volume weight column in the imported file
            return;
        }

        $conn = $collection->getConnection();

        $ratesDataWithId = $this->importHandler->fillRatesWithRateId($data);
        $dataToInsert    = [];
        $dataToDelete    = [];
        foreach ($ratesDataWithId as $rateData) {
            if ($rateData['volume_weight_to'] === '' && $rateData['volume_weight_from'] === '') {
                $dataToDelete[] = $rateData['rate_id'];
            } else {
                $dataToInsert[] = [
                    'rate_id'            => $rateData['rate_id'],
                    'volume_weight_to'   => $rateData['volume_weight_to'] === '' ? null : (float)$rateData['volume_weight_to'],
                    'volume_weight_from' => $rateData['volume_weight_from'] === '' ? null : (float)$rateData['volume_weight_from']
                ];
            }
        }

        if (!empty($dataToDelete)) {
            $conn->delete(
                $collection->getTable('mageworx_shippingrules_rates_volume_weight'),
                ['rate_id' => ['in' => $dataToDelete]]
            );
        }

        if (empty($dataToInsert)) {
            return;
        }

        try {
            $this->importHandler->insertData(
                $dataToInsert,
                $conn,
                $collection->getTable('mageworx_shippingrules_rates_volume_weight')
            );
        } catch (\Zend_Db_Exception $exception) {
            $this->messageManager->addErrorMessage(__('Unable to import the volume weight attributes'));
            $this->messageManager->addErrorMessage($exception->getMessage());

            return;
        }
    }
}