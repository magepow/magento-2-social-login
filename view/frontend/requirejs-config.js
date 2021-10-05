var config = {
    paths: {
        socialProvider: 'Magepow_SocialLogin/js/provider',
        socialPopupForm: 'Magepow_SocialLogin/js/popup'
    },
    map: {
        '*': {
            'Magento_Checkout/js/proceed-to-checkout': 'Magepow_SocialLogin/js/proceed-to-checkout'
        }
    }
};