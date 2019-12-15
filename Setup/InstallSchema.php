<?php

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('paynow_transactions');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                               ->newTable($tableName)
                               ->addColumn(
                                   'id',
                                   Table::TYPE_INTEGER,
                                   null,
                                   [
                                       'identity' => true,
                                       'unsigned' => true,
                                       'nullable' => false,
                                       'primary'  => true
                                   ],
                                   'ID'
                               )->addColumn(
                    'order_number',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default'  => '',
                        'unique'   => true
                    ],
                    'Magento Order Number'
                )->addColumn(
                    'polling_url',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Paynow Poll Url'
                )->addColumn(
                    'last_polled',
                    Table::TYPE_INTEGER,
                    10,
                    ['nullable' => false, 'default' => 0],
                    'Description'
                )
                               ->addColumn(
                                   'created_at',
                                   Table::TYPE_DATETIME,
                                   null,
                                   ['nullable' => false],
                                   'Created At'
                               )
                               ->setComment('PayNow Table')
                               ->setOption('type', 'InnoDB')
                               ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
