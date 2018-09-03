<?php

$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
        ->newTable($installer->getTable('nemo_addimage/customerImage'))
        ->addColumn(
                'id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Id'
        )
        ->addColumn(
                'product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
                ), 'Product Id'
        )
        ->addColumn(
                'title', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => false,
                ), 'Title'
        )
        ->addColumn(
                'filename', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
                ), 'Filename'
        )
        ->addColumn(
                'is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'default' => '0',
                ), 'Is Active'
        )
        ->addColumn(
                'created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
                ), 'Created at'
        )
        ->addIndex(
                $installer->getIdxName('nemo_addimage/customerImage', array('id', 'product_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE), array('id', 'product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        )
        ->addForeignKey(
        $installer->getFkName('nemo_addimage/customerImage', 'product_id', 'catalog/product', 'entity_id'), 'product_id', $installer->getTable('catalog/product'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
);
$installer->getConnection()->createTable($table);

$installer->endSetup();
