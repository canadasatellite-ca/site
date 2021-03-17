define([
    'jquery'
], function($) {
    'use strict';
    $.widget('ccType.cardNumber',{
        options: {
            ccTypes: {
                JCB: new RegExp('^(?:2131|1800|35)[0-9]{0,}$'),
                AE: new RegExp('^3([47]\\d*)?$'),
                VI: new RegExp('^4\\d*$'),
                MC: new RegExp('^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$'),
                // MA: new RegExp('^(5[06789]|6)[0-9]{0,}$'),
                DI: new RegExp('^(6011(0|[2-4]|74|7[7-9]|8[6-9]|9)|6(4[4-9]|5))\\d*$'),
                DN: new RegExp('^(3(0[0-5]|095|6|[8-9]))\\d*$'),
                UN: new RegExp('^(622(1(2[6-9]|[3-9])|[3-8]|9([[0-1]|2[0-5]))|62[4-6]|628([2-8]))\\d*?$'),
                MI: new RegExp('^(5(0|[6-9])|63|67(?!59|6770|6774))\\d*$'),
                MD: new RegExp('^6759(?!24|38|40|6[3-9]|70|76)|676770|676774\\d*$'),
                HC: new RegExp('^((606282)|(637095)|(637568)|(637599)|(637609)|(637612))\\d*$'),
                ELO: new RegExp('^((509091)|(636368)|(636297)|(504175)|(438935)|(40117[8-9])|(45763[1-2])|' +
                    '(457393)|(431274)|(50990[0-2])|(5099[7-9][0-9])|(50996[4-9])|(509[1-8][0-9][0-9])|' +
                    '(5090(0[0-2]|0[4-9]|1[2-9]|[24589][0-9]|3[1-9]|6[0-46-9]|7[0-24-9]))|' +
                    '(5067(0[0-24-8]|1[0-24-9]|2[014-9]|3[0-379]|4[0-9]|5[0-3]|6[0-5]|7[0-8]))|' +
                    '(6504(0[5-9]|1[0-9]|2[0-9]|3[0-9]))|' +
                    '(6504(8[5-9]|9[0-9])|6505(0[0-9]|1[0-9]|2[0-9]|3[0-8]))|' +
                    '(6505(4[1-9]|5[0-9]|6[0-9]|7[0-9]|8[0-9]|9[0-8]))|' +
                    '(6507(0[0-9]|1[0-8]))|(65072[0-7])|(6509(0[1-9]|1[0-9]|20))|' +
                    '(6516(5[2-9]|6[0-9]|7[0-9]))|(6550(0[0-9]|1[0-9]))|' +
                    '(6550(2[1-9]|3[0-9]|4[0-9]|5[0-8])))\\d*$'),
                AU: new RegExp('^5078\\d*$')

            },
        },
        _create: function() {
            var self = this;
            $('[name="payment[cc_number]"]').on('change', function() {
                var ccNumber = $(this).val().replace(/\D/g, '');
                var ccType = self._getCcTypeByNumber(ccNumber);

                if (ccType) {
                    $('[name="payment[cc_type]"]').val(ccType);
                } else {
                    $('.cc-type-error').fadeIn();
                    $('[name="payment[cc_type]"]').val('');
                }
            })
        },
        _getCcTypeByNumber: function (ccNumber) {
            $('.cc-type-error').hide();
            var availableTypesOfMethod = JSON.parse(this.options.availableTypes);
            $('.payment-icons').css('opacity', .2);
            if (ccNumber.match(this.options.ccTypes.JCB)) {
                if (typeof availableTypesOfMethod.JCB != 'undefined') {
                    $('.payment-icon-jcb').css('opacity', 1);
                    return "JCB";
                }
            } else if (ccNumber.match(this.options.ccTypes.AE)) {
                if (typeof availableTypesOfMethod.AE != 'undefined') {
                    $('.payment-icon-ae').css('opacity', 1);
                    return "AE";
                }
            }else if (ccNumber.match(this.options.ccTypes.VI)) {
                if (typeof availableTypesOfMethod.VI != 'undefined') {
                    $('.payment-icon-vi').css('opacity', 1);
                    return "VI";
                }
            } else if (ccNumber.match(this.options.ccTypes.MC)) {
                if (typeof availableTypesOfMethod.MC != 'undefined') {
                    $('.payment-icon-mc').css('opacity', 1);
                    return "MC";
                }
            } else if (ccNumber.match(this.options.ccTypes.DI)) {
                if (typeof availableTypesOfMethod.DI != 'undefined') {
                    $('.payment-icon-di').css('opacity', 1);
                    return "DI";
                }
            } else if (ccNumber.match(this.options.ccTypes.DN)) {
                if (typeof availableTypesOfMethod.DN != 'undefined') {
                    $('.payment-icon-dn').css('opacity', 1);
                    return "DN";
                }
            } else if (ccNumber.match(this.options.ccTypes.UN)) {
                if (typeof availableTypesOfMethod.UN != 'undefined') {
                    $('.payment-icon-un').css('opacity', 1);
                    return "UN";
                }
            } else if (ccNumber.match(this.options.ccTypes.MI)) {
                if (typeof availableTypesOfMethod.MI != 'undefined') {
                    $('.payment-icon-mi').css('opacity', 1);
                    return "MI";
                }
            } else if (ccNumber.match(this.options.ccTypes.MD)) {
                if (typeof availableTypesOfMethod.MD != 'undefined') {
                    $('.payment-icon-md').css('opacity', 1);
                    return "MD";
                }
            } else if (ccNumber.match(this.options.ccTypes.HC)) {
                if (typeof availableTypesOfMethod.HC != 'undefined') {
                    $('.payment-icon-hc').css('opacity', 1);
                    return "HC";
                }
            } else if (ccNumber.match(this.options.ccTypes.ELO)) {
                if (typeof availableTypesOfMethod.ELO != 'undefined') {
                    $('.payment-icon-elo').css('opacity', 1);
                    return "ELO";
                }
            } else if (ccNumber.match(this.options.ccTypes.AU)) {
                if (typeof availableTypesOfMethod.AU != 'undefined') {
                    $('.payment-icon-au').css('opacity', 1);
                    return "AU";
                }
            }

            return false;
        }
    });

    return $.ccType.cardNumber;
});
