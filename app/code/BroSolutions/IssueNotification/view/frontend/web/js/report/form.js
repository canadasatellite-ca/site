define(['ko', 'uiComponent', 'Magento_Ui/js/model/messageList', 'mage/url'], function(ko, Component, messageList, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'BroSolutions_IssueNotification/report/form'
        },
        initialize: function() {
            this._super();
        },
        getTitle: function () {
            return this.message;
        },
        sendReport: function () {
            var email = jQuery('[name="report_email"]').val(),
                comment = jQuery('[name="report_comment"]').val(),
                formKey = jQuery('[name="form_key"]').val();

            if (jQuery('#issue-report-form').validation() && jQuery('#issue-report-form').validation('isValid')) {
                jQuery.ajax({
                    url: urlBuilder.build('issue_notif/checkoutReport/send'),
                    method: 'POST',
                    dataType: 'json',
                    data: { email: email, comment: comment, formKey: formKey},
                    success: function (response) {
                        if (!response) {
                            messageList.addErrorMessage({ message: 'Something went wrong. Try again later' });
                            return false;
                        }
                        location.reload();
                    },
                    fail: function () {
                    }
                })
            }
        }
    });
});
