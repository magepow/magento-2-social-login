<?php
namespace Magepow\SocialLogin\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('magepow_social_login')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('magepow_social_login'))
                ->addColumn(
                    'social_customer_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Social Customer ID'
                )
                ->addColumn('social_id', Table::TYPE_TEXT, 255, ['unsigned' => true, 'nullable => false'], 'Social Id')
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable => false'],
                    'Customer Id'
                )
                ->addColumn(
                    'is_send_password_email',
                    Table::TYPE_INTEGER,
                    10,
                    ['unsigned' => true, 'nullable => false', 'default' => '0'],
                    'Is Send Password Email'
                )
                ->addColumn('type', Table::TYPE_TEXT, 255, ['default' => ''], 'Type')
                ->addColumn(
                    'social_created_at',
                    Table::TYPE_TIMESTAMP,
                    20,
                    [],
                    'Social Created At'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable => true'],
                    'Status'
                )
                ->addForeignKey(
                    $installer->getFkName('magepow_social_login', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Social Login Table');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
