/**
* Copyright 2015 Iksanika. All rights reserved.
* See IKS-LICENSE.txt for license details.
*/

define([
    'jquery',
    'Iksanika_Ajaxcart/js/init',

    'Iksanika_Ajaxcart/js/ajaxcart',
    'Iksanika_Ajaxcart/js/ajaxcart/effect',
    'Iksanika_Ajaxcart/js/ajaxcart/effects'
], function($, iacConfig) {
    "use strict";

    $.widget('iac.connector', {
        _create: function() {
        },
        request: function(url, data, requestCaller) {
            var self = this;
            data.push({name: iacConfig.extIndex, value: 1});
            data.push({name: "handles", value: iacConfig.handles});

            $(document).trigger("iacEvent:onLoading");

            $.ajax({
                url: url,
                data: $.param(data),
                type: 'post',
                dataType: 'json',
                caller: requestCaller,
                success: function(response, status) {
                    if (status == 'success') {
                        if(response.redirectUrl)
                        {
                            window.locale = response.redirectUrl;
                            window.location = response.redirectUrl;
                        }else
                        if(response.backUrl)
                        {
                            window.locale = response.backUrl;
                            window.location = response.backUrl;
                        }else {
                            $(document).trigger("iacEvent:onComplete", [response, status, this.caller]);
                        }
                        //if (response.reloadUrl) {
                        //  window.locale = response.reloadUrl;
                        //}
                    }
                },
                fail: function()
                {
                    $(document).trigger("iacEvent:onFail");
                },
                done: function()
                {
                    $(document).trigger("iacEvent:onFail");
                }
            });
        }


    });

    return $.iac.connector;
});

