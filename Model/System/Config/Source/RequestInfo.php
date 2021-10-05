<?php
namespace Magepow\SocialLogin\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class RequestInfo implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => 1, 'label' => __('Always Require')],
            ['value' => 2, 'label' => __('If social account does not provide E-mail.')]
        ];
    }
}
