define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Ui/js/form/element/abstract',
], function (_, utils, layout, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            recaptchaInt: null,
            publicKey: null,
        },

        initialize: function () {
            this._super();

            this.initPublicKey();
            this.handleOpenedComment();

            return this;
        },

        initPublicKey: function () {
            if (!window.recaptchaReviewPublicKey) {
                throw new Error('window.recaptchaReviewPublicKey is not defined');
            }
            this.publicKey = window.recaptchaReviewPublicKey;
        },

        handleOpenedComment: function () {
            jQuery(document).on( "click", ".aw-ar__review-list .add-comment", this.loadReviewRecaptcha.bind(this));
        },

        loadReviewRecaptcha: function () {
            loadMainRecaptchaScript();
                this.recaptchaInt = setInterval((function () {
                    if (typeof (grecaptcha) == 'undefined') {
                        return;
                    }
                    clearInterval(this.recaptchaInt);

                    var current = this;
                    grecaptcha.ready(current.grecaptchaReady.bind(current));

                }).bind(this), 0);
        },

        grecaptchaReady: function () {
            grecaptcha.execute(this.publicKey, {action: 'contact'})
                .then(this.grecaptchaAfterExecute.bind(this));
        },

        grecaptchaAfterExecute(token) {
            this.set('value', token);
        },
    });
});
