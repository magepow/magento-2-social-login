<?php
namespace Magepow\SocialLogin\Model\ResourceModel\Social;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magepow\SocialLogin\Model\ResourceModel\Social;

/**
 * Class Collection
 *
 * @package Mageplaza\SocialLogin\Model\ResourceModel\Social
 */
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(\Magepow\SocialLogin\Model\Social::class, Social::class);
    }

    /**
     * @param $type
     */
    public function filterOrder($type)
    {
        $sales_order_table = $this->getTable('sales_order');

        $this->getSelect()->join(
            ['top_social_login' => $sales_order_table],
            'main_table.customer_id = top_social_login.customer_id',
            [
                'grand_total'    => 'top_social_login.base_subtotal',
                'created_at'     => 'top_social_login.created_At',
                'total_refunded' => 'top_social_login.base_total_refunded',
                'total_canceled' => 'top_social_login.base_subtotal_canceled',
                'store_id'       => 'top_social_login.store_id'
            ]
        );

        $this->getSelect()->where("main_table.type='" . $type . "'");
    }
}
