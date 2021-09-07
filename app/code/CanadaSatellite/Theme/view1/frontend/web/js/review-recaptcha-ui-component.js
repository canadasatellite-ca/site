define([
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Ui/js/form/element/abstract',
], function (_, utils, layout, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            recaptchaReviewScriptAdded: false,
            recaptchaInt: null,
            publicKey: null,
        },

        initialize: function () {
            this._super();

            this.initPublicKey();
            this.handleOpenedReview();

            return this;
        },

        initPublicKey: function () {
            if (!window.recaptchaReviewPublicKey) {
                throw new Error('window.recaptchaReviewPublicKey is not defined');
            }
            this.publicKey = window.recaptchaReviewPublicKey;
        },

        handleOpenedReview: function () {
            jQuery(document).on( "click", ".aw-advanced-reviews-summary-container .review-summary-actions a", this.loadReviewRecaptcha.bind(this));
            jQuery(document).on( "click", ".aw-ar-write-review-control .action.primary", this.loadReviewRecaptcha.bind(this));
            jQuery(document).on( "click", ".aw-ar-wrapper-fieldset .control", this.loadReviewRecaptcha.bind(this));
            jQuery('#tab-label-product_aw_reviews_tab-title').click(this.loadReviewRecaptcha.bind(this));
        },

        loadReviewRecaptcha: function () {
            loadMainRecaptchaScript();
            if (!this.recaptchaReviewScriptAdded) {
                this.recaptchaInt = setInterval((function () {
                    if (typeof (grecaptcha) == 'undefined') {
                        return;
                    }
                    clearInterval(this.recaptchaInt);

                    var current = this;
                    grecaptcha.ready(current.grecaptchaReady.bind(current));

                }).bind(this), 0);

                this.recaptchaReviewScriptAdded = true;
            }
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
