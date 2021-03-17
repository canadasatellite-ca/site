/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiElement',
    'mage/translate'
], function ($, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/summary/brief-summary',
            ratingViewTmpl: 'Aheadworks_AdvancedReviews/product/view/review/summary/brief-summary/rating-view',
            imports: {
                reviewsCount: '${ $.provider }:data.reviews_count',
                aggregatedRatingPercentValue: '${ $.provider }:data.aggregated_rating_percent',
                aggregatedRatingAbsoluteValue: '${ $.provider }:data.aggregated_rating_absolute',
                aggregatedRatingTitle: '${ $.provider }:data.aggregated_rating_title',
                customerRecommendedPercent: '${ $.provider }:data.customer_recommended_percent'
            }
        }
    });
});
