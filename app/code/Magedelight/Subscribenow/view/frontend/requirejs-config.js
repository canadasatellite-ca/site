var config = {
    config: {
        mixins: {
            'Magento_Bundle/js/price-bundle': {
                'Magedelight_Subscribenow/js/price-bundle-mixin': true
            },
            'Magento_Catalog/js/price-box': {
                'Magedelight_Subscribenow/js/price-box-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Braintree/template/payment/form.html':
                    'Magedelight_Subscribenow/template/payment/form.html',
            'Magento_Paypal/template/payment/payflowpro-form.html':
                    'Magedelight_Subscribenow/template/payment/payflowpro-form.html'
        }
    }
};
