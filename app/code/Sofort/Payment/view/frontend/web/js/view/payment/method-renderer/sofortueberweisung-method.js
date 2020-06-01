define(
    [
        'Magento_Payment/js/view/payment/cc-form',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Sofort_Payment/payment/sofort'
            },
            redirectAfterPlaceOrder: false,

            getCode: function() {
                return 'sofortueberweisung';
            },

            isActive: function() {
                return true;
            },

            isBanner: function() {
                this.isPlaceOrderActionAllowed(true);
                return window.checkoutConfig.payment.sofort.isBanner;
            },

            getTitle: function() {
                if (window.checkoutConfig.payment.sofort.recommendedPayment !== '') {
                    return this.item.title + " " + window.checkoutConfig.payment.sofort.recommendedPayment;
                } else {
                    return this.item.title;
                }
            },

            afterPlaceOrder: function () {
                window.location.replace(url.build('sofortueberweisung/payment/redirect/'));
            }
        });
    }
);
