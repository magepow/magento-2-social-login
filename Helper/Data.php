<?php
namespace Magepow\SocialLogin\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\ScopeInterface;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_MODULE_PATH = 'sociallogin';

    protected $backendConfig;

    protected $objectManager;

    protected $storeManager;

    protected $isArea = [];

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }
    
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';
        
        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if ($scopeValue === null && !$this->isArea()) {
            if (!$this->backendConfig) {
                $this->backendConfig = $this->objectManager->get(\Magento\Backend\App\ConfigInterface::class);
            }

            return $this->backendConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    public function isArea($area = Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            $state = $this->objectManager->get(\Magento\Framework\App\State::class);

            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (Exception $e) {
                $this->isArea[$area] = false;
            }
        }

        return $this->isArea[$area];
    }

    /**
     * @param RequestInterface $request
     * @param $formId
     *
     * @return string
     */
    public function captchaResolve(RequestInterface $request, $formId)
    {
        $captchaParams = $request->getPost(\Magento\Captcha\Helper\Data::INPUT_NAME_FIELD_VALUE);

        return isset($captchaParams[$formId]) ? $captchaParams[$formId] : '';
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function canSendPassword($storeId = null)
    {
        return $this->getConfigGeneral('send_password', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPopupEffect($storeId = null)
    {
        return $this->getConfigGeneral('popup_effect', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getStyleManagement($storeId = null)
    {
        $style = $this->getConfigGeneral('style_management', $storeId);
        if ($style === 'custom') {
            return $this->getCustomColor($storeId);
        }

        return $style;
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomColor($storeId = null)
    {
        return $this->getConfigGeneral('custom_color', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCustomCss($storeId = null)
    {
        return $this->getConfigGeneral('custom_css', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function requiredMoreInfo($storeId = null)
    {
        return $this->getConfigGeneral('require_more_info', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getFieldCanShow($storeId = null)
    {
        return $this->getConfigGeneral('information_require', $storeId);
    }

    /**
     * @return mixed
     */
    public function isSecure()
    {
        return $this->getConfigValue('web/secure/use_in_frontend');
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isReplaceAuthModal($storeId = null)
    {
        return $this->getPopupLogin() && $this->getConfigGeneral('authentication_popup', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPopupLogin($storeId = null)
    {
        return $this->getConfigGeneral('popup_login', $storeId);
    }
}
