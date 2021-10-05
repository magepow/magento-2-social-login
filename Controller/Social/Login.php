<?php
namespace Magepow\SocialLogin\Controller\Social;

use Exception;
use Hybridauth\Hybridauth as Hybrid_Auth;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;

class Login extends AbstractSocial
{
    private $type;
    /**
     * @return ResponseInterface|Raw|ResultInterface|Login|void
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->checkCustomerLogin() && $this->session->isLoggedIn()) {
            $this->_redirect('customer/account');

            return;
        }

        $type = $this->getRequest()->getParam('type')? $this->getRequest()->getParam('type') : strtolower($this->getRequest()->getParam('hauth_done'));
        if($type){
            $type = strtolower($this->getRequest()->getParam('type'));
        }
        if(!$type) {
            $this->_forward('noroute');

            return;
        }

        $this->apiHelper->setType($type);

        try {
            $userProfile = $this->getUserProfile($type);
            if (!$userProfile->identifier) {
                return $this->emailRedirect($type);
            }
        } catch (Exception $e) {

            $this->setBodyResponse($e->getMessage());

            return;
        }

        $customer     = $this->apiObject->getCustomerBySocial($userProfile->identifier, $type);
        $customerData = $this->customerModel->load($customer->getId());

        if (!$customer->getId()) {
            $requiredMoreInfo = (int)$this->apiHelper->requiredMoreInfo();

            if ((!$userProfile->email && $requiredMoreInfo === 2) || $requiredMoreInfo === 1) {
                $this->session->setUserProfile($userProfile);

                return $this->_appendJs(
                    sprintf(
                        "<script>window.close();window.opener.fakeEmailCallback('%s','%s','%s');</script>",
                        $type,
                        $userProfile->firstName,
                        $userProfile->lastName
                    )
                );
            }

            $customer = $this->createCustomerProcess($userProfile, $type);
        } 

        $this->refresh($customer);

        return $this->_appendJs();
    }

    public function getUserProfile($apiName, $area = null)
    {   
        $config = [
            'callback'   => $this->apiHelper->getAuthUrl($apiName),
            'providers'  => [
                $apiName => $this->getProviderData($apiName)
            ],
        ];

        $auth = new Hybrid_Auth($config);

        try {

            $adapter     = $auth->authenticate($apiName);
            $userProfile = $adapter->getUserProfile();

        } catch (Exception $e) {

            $auth->disconnectAllAdapters();
            $auth        = new Hybrid_Auth($config);
            $adapter     = $auth->authenticate($apiName);
            $userProfile = $adapter->getUserProfile();
        }

        return $userProfile;
    } 
    /**
     * @param $apiName
     *
     * @return array
     */
    public function getProviderData($apiName)
    { 
        $data = [
            'enabled' => $this->apiHelper->isEnabled(),
            'keys'    => [
                'key'    => $this->apiHelper->getAppId(),
                'secret' => $this->apiHelper->getAppSecret()
            ]
        ];

        return array_merge($data, $this->apiHelper->getSocialConfig($apiName));
    }

    /**
     * @return bool
     */
    public function checkCustomerLogin()
    {
        return true;
    }

    /**
     * @param $message
     */
    protected function setBodyResponse($message)
    {
        $content = '<html><head></head><body>';
        $content .= '<div class="message message-error">' . __('Ooophs, we got an error: %1', $message) . '</div>';
        $content .= <<<Style
            <style type="text/css">
                .message{
                    background: #fffbbb;
                    border: none;
                    border-radius: 0;
                    color: #333333;
                    font-size: 1.4rem;
                    margin: 0 0 10px;
                    padding: 1.8rem 4rem 1.8rem 1.8rem;
                    position: relative;
                    text-shadow: none;
                }
                .message-error{
                    background:#ffcccc;
                }
            </style>
            Style;
        $content .= '</body></html>';
        $this->getResponse()->setBody($content);
    }
}
