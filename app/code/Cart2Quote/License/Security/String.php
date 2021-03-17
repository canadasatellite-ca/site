<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

// @deprecated Backward compatibility with 2.x series
if (PHP_VERSION_ID < 70000) {
    class_alias('Cart2Quote\License\Security\Text', 'Cart2Quote\License\Security\String');
}
