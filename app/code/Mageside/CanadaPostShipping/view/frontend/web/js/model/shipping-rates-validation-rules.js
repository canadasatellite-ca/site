/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
/*global define*/
define(
    [],
    function () {
        'use strict';

        return {
            getRules: function () {
                return {
                    'postcode': {
                        'required': true
                    },
                    'country_id': {
                        'required': true
                    }
                };
            }
        };
    }
);
