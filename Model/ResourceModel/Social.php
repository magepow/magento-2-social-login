<?php
namespace Magepow\SocialLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Social extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('magepow_social_login', 'social_customer_id');
    }
}
