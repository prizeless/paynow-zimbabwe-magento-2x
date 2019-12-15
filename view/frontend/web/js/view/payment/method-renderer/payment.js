define([
    'Magento_Payment/js/view/payment/cc-form',
    'jquery',
    'Magento_Payment/js/model/credit-card-validation/validator'
], function (Component, $) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Prizeless_PayNow/payment/payment'
        },
        getCode: function () {
            return 'paynow';
        },
        redirectAfterPlaceOrder: false,
        isActive: function () {
            return true;
        },
        afterPlaceOrder: function () {
            window.location.replace('/paynow/request');
            return false;
        },
    });
});
