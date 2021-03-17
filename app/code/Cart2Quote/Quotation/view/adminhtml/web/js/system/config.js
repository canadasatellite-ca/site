/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'jquery/jquery-storageapi'
], function ($) {
    var storage = $.localStorage;

    //find all tooltips in cart2quote configs
    $('[id^=cart2quote] .tooltip-content').each(function () {
        var $tooltip = $(this);
        var number = $tooltip.text();

        //check if the text in the tooltip is a number
        if (!isNaN(number)) {
            var cacheKey = 'c2q_admin_tooltip_cache_' + number;

            //load from cache if available
            if (storage.isSet(cacheKey)) {
                $tooltip.html(storage.get(cacheKey));
            } else {
                //request data from zendesk
                var url = 'https://cart2quote.zendesk.com/api/v2/help_center/en-us/articles/' + number + '.json';
                $.get(url, function (data) {
                    if (data.article) {
                        if (data.article.body) {
                            var body = data.article.body;

                            //split on the Available in Cart2Quote line (it doesn't look good)
                            var lineSplit = "<p><strong><span class=\"wysiwyg-font-size-x-large\">Available in Cart2Quote:</span></strong></p>";
                            var bodyParts = body.split(lineSplit);
                            if (bodyParts[0]) {
                                body = bodyParts[0];

                                //split on the Need more information line (as you can't click in the tooltip)
                                var lineSplit = "<p><strong><span class=\"wysiwyg-font-size-x-large\">Need more information?</span></strong></p>";
                                var moreBodyParts = body.split(lineSplit);
                                if (moreBodyParts[0]) {
                                    //only keep the explanation
                                    body = moreBodyParts[0];

                                    //save to cache
                                    storage.set(cacheKey, body)

                                    //replace tooltip
                                    $tooltip.html(body);
                                }
                            }
                        } else {
                            console.log('This article is empty in external support documentation: ' + number);
                            $tooltip.hide();
                        }
                    } else {
                        console.log('This article does not exist in external support documentation: ' + number);
                        $tooltip.hide();
                    }
                }).fail(function () {
                    console.log('Could not load tooltip data from external support documentation for: ' + number);
                    $tooltip.hide();
                });
            }
        }
    });
});
