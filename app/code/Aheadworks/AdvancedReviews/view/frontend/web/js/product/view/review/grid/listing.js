/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/listing',
    'Magento_Ui/js/lib/spinner',
    'Aheadworks_AdvancedReviews/js/product/view/review/grid/show-more',
    'rjsResolver',
    'awArMagnificPopup'
], function ($, _, Listing, loader, initShowMore, resolver) {
    'use strict';

    return Listing.extend({
        defaults: {
            imageByPages: {},
            template: 'Aheadworks_AdvancedReviews/product/view/review/grid/listing',
            listTemplate: 'Aheadworks_AdvancedReviews/product/view/review/grid/listing',
            attachmentIdentifierClass: 'preview-link',
            imports: {
                imageByPages: '${ $.provider }:data.imageByPages'
            },
            modules: {
                pages: '${ $.ns }.${ $.ns }.listing_bottom.listing_paging'
            }
        },

        /**
         * Gallery update types - init, update
         */
        galleryUpdateType: 'init',

        /**
         * Gallery update action - next, prev
         */
        galleryUpdateAction: 'next',

        /**
         * Check if need to render sorting block
         *
         * @returns {Boolean}
         */
        isNeedToRenderSortingBlock: function() {
            return this.getSortableColumns().length > 0;
        },

        /**
         * Get sortable columns
         *
         * @returns {Array}
         */
        getSortableColumns: function() {
            return this.elems.filter('sortable');
        },

        /**
         * @inheritdoc
         */
        hideLoader: function () {
            initShowMore();
            this._super();
        },

        /**
         * @inheritdoc
         */
        onDataReloaded: function () {
            this._super();
            if (this.galleryUpdateType === 'update') {
                resolver(this.updateImageGallery, this);
            } else {
                resolver(this.initImageGallery, this);
            }
        },

        /**
         * Update image gallery
         */
        updateImageGallery: function () {
            var mfp = $.magnificPopup.instance;

            if (mfp) {
                mfp.items = [];
                $('.aw-ar__attach .'+this.attachmentIdentifierClass+'.preview-image').each(function () {
                    mfp.items.push(this);
                });

                if (this.galleryUpdateAction === 'next') {
                    mfp.index = 0;
                } else if (this.galleryUpdateAction === 'prev') {
                    mfp.index = mfp.items.length > 0 ? mfp.items.length - 1 : 0;
                }

                mfp.updateItemHTML();
            }
        },

        /**
         * Init image gallery
         */
        initImageGallery: function () {
            var self = this;

            $('.aw-ar__attach .'+self.attachmentIdentifierClass+'.preview-image')
                .magnificPopup({
                    type:'image',
                    preload: true,
                    gallery:{
                        enabled:true
                    },
                    image: {
                        markup: '<div class="mfp-figure">'
                            + '<div class="mfp-close"></div>'
                            + '<figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar">'
                            + '<div class="mfp-title"></div></div></figcaption></figure></div>',
                        titleSrc: function (item) {
                            var titleContent = '';

                            item.el
                                .parents('.aw-ar__review-list-column-main')
                                .find('.aw-ar__review-list-column-main-middle.content .data-grid-cell-content-wrapper')
                                .each(
                                    $.proxy(function (index, element) {
                                        titleContent += $(element).html();
                                    }, this)
                                );
                            return titleContent;
                        },
                        cursor:false
                    },
                    callbacks: {
                        beforeOpen: function() {
                            var mfp = $.magnificPopup.instance;

                            // fix for arrow display if one items in gallery
                            if (mfp.items.length === 1) {
                                mfp.items.push(mfp.items[0]);
                                self.isFakeLastItem = true;
                            }
                        },
                        open: function() {
                            var mfp = $.magnificPopup.instance,
                                proto = $.magnificPopup.proto;

                            // fix for arrow display if one items in gallery
                            if (self.isFakeLastItem === true) {
                                mfp.items.splice(-1,1);
                                self.isFakeLastItem = false;
                            }

                            mfp.next = function() {
                                if(mfp.index < mfp.items.length - 1) {
                                    proto.next.call(mfp);
                                } else {
                                    self.changePage('next');
                                }
                            };
                            mfp.prev = function() {
                                if(mfp.index > 0) {
                                    proto.prev.call(mfp);
                                } else {
                                    self.changePage('prev');
                                }
                            };
                            self.arrowVisibilityUpdate();
                        },
                        change: function() {
                            self.arrowVisibilityUpdate();
                        },
                        close: function() {
                            var mfp = $.magnificPopup.instance;

                            mfp.arrowRight = mfp.arrowLeft = null;
                            self.galleryUpdateType = 'init';
                            self.initImageGallery();
                        }
                    }
                });
        },

        /**
         * Update arrow visibility
         */
        arrowVisibilityUpdate: function () {
            var mfp = $.magnificPopup.instance,
                nextPage = this._getPageNumberByType('next'),
                prevPage = this._getPageNumberByType('prev');

            if (mfp.arrowRight) {
                if (!nextPage && mfp.index >= mfp.items.length - 1) {
                    mfp.arrowRight.css('display', 'none');
                } else {
                    mfp.arrowRight.css('display', 'block');
                }
            }
            if (mfp.arrowLeft) {
                if (!prevPage && mfp.index === 0) {
                    mfp.arrowLeft.css('display', 'none');
                } else {
                    mfp.arrowLeft.css('display', 'block');
                }
            }
        },

        /**
         * Change page from gallery
         * @param {string} type
         */
        changePage: function (type) {
            var nextPage = this._getPageNumberByType(type),
                mfp = $.magnificPopup.instance,
                proto = $.magnificPopup.proto;

            if (nextPage) {
                // Detach and perform modifications
                mfp.contentContainer.detach();
                if(mfp.content)
                    mfp.content.detach();

                proto.updateStatus.call(mfp, 'loading', 'Loading...');
                this.galleryUpdateType = 'update';
                this.galleryUpdateAction = type;

                if (this.pages()) {
                    this.pages().setPage(nextPage);
                }
            }
        },

        /**
         * Retrieve next page number by type
         *
         * @param {string} type
         * @returns {number|undefined}
         */
        _getPageNumberByType: function (type) {
            var currentPage,
                imageByPage,
                nextPage;

            if (this.pages()) {
                currentPage = this.pages().current;
                while (true) {
                    type === 'next' ? currentPage++ : currentPage--;

                    if (currentPage === 0 || currentPage > _.size(this.imageByPages)) {
                        break;
                    }

                    imageByPage = this.imageByPages[currentPage];
                    if (imageByPage && imageByPage.count > 0) {
                        nextPage = currentPage;
                        break;
                    }
                }
            }

            return nextPage;
        }
    });
});
