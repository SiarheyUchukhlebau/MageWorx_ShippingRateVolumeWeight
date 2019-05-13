<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRateVolumeWeight\Setup;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $ratesTable = $installer->getTable(\MageWorx\ShippingRules\Model\Carrier::RATE_TABLE_NAME);

        /**
         * Create table 'mageworx_shippingrules_rates_volume_weight'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mageworx_shippingrules_rates_volume_weight')
        )->addColumn(
            'rate_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rate Id'
        )->addColumn(
            'volume_weight_from',
            Table::TYPE_DECIMAL,
            '12,4',
            ['unsigned' => true, 'nullable' => true],
            'Volume Weight'
        )->addColumn(
            'volume_weight_to',
            Table::TYPE_DECIMAL,
            '12,4',
            ['unsigned' => true, 'nullable' => true],
            'Volume Weight'
        )->addForeignKey(
            $installer->getFkName('mageworx_shippingrules_rates_volume_weight', 'rate_id', $ratesTable, 'rate_id'),
            'rate_id',
            $ratesTable,
            'rate_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                'mageworx_shippingrules_rates_volume_weight',
                ['rate_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            'rate_id',
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Volume Weight For Shipping Suite Rates'
        );

        $installer->getConnection()->createTable($table);
    }
}