/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_CheckoutAgreements/js/model/agreements-modal'
], function (ko, $, Component, agreementsModal) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/agreements/view',
            areAgreementsEnabled: false,
            isNeedToShowForGuests: false,
            isNeedToShowForCustomers: false,
            isCustomerLoggedIn: false,
            agreementsData: {},
        },
        modalTitle: ko.observable(null),
        modalContent: ko.observable(null),
        modalWindow: null,

        /**
         * Checks if need to render agreements component
         *
         * @returns {Boolean}
         */
        isNeedToRender: function () {
            return this.areAgreementsEnabled
                && ((this.isNeedToShowForGuests && !this.isCustomerLoggedIn)
                    || (this.isNeedToShowForCustomers && this.isCustomerLoggedIn));
        },

        /**
         * Show agreement content in modal
         *
         * @param {Object} element
         */
        showContent: function (element) {
            this.modalTitle(element.checkboxText);
            this.modalContent(element.content);
            agreementsModal.showModal();
        },

        /**
         * Init modal window for rendered element
         *
         * @param {Object} element
         */
        initModal: function (element) {
            agreementsModal.createModal(element);
        },

        /**
         * Build a unique id for the agreement checkbox
         *
         * @param {Number} agreementId
         */
        getCheckboxId: function (agreementId) {
            return 'agreement_' + agreementId;
        },

        /**
         * Build a unique id for the agreement validation errors message box for the product page
         *
         * @param {Number} agreementId
         */
        getErrorsMessageBoxId: function (agreementId) {
            return this.getCheckboxId(agreementId) + '-errors-message-box';
        },
    });
});
