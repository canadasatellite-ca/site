define([
    "jquery"
], function ($) {
    'use strict';

    return function (config) {
        var selected = config.status_id;
        var statuses = config.data;
        for (var i = 0; i < statuses.length; ++i) {

            if (statuses[i].name == selected) {
                if (statuses[i].finalized) {
                    $(':button').prop('disabled', true);
                    $('select[id="quote_status"]').prop('disabled', true);
                    $("#change-shipping-method").attr('disabled', 'disabled');
                    $('button[id="quote-pdf"]').prop('disabled', false);
                    $('button[id="edit"]').prop('disabled', false);
                    break;
                } else if (statuses[i].locked) {
                    $(':button').prop('disabled', true);
                    $("#change-shipping-method").attr('disabled', 'disabled');
                    $('button[id="quote-pdf"]').prop('disabled', false);
                    $('button[id="edit"]').prop('disabled', false);
                    $('button[id="saveQuote"]').prop('disabled', false);
                    break;
                }
                break;
            }
        }

        $('select[id="quote_status"]').on('change', function() {
            for (var i = 0; i < statuses.length; ++i) {

                if (statuses[i].name == this.value) {
                    if (statuses[i].locked || statuses[i].finalized) {
                        $('button[id="saveQuote"]').prop('disabled', false);
                        break;
                    } else if ( statuses[i].limitation) {
                        $('button[id="saveQuote"]').prop('disabled', true);
                        break;
                    }
                    break;
                }
            }
        })
    };
});