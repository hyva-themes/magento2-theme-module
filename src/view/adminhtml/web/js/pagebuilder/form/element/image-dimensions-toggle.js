/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (SingleCheckbox) {
    'use strict';

    return SingleCheckbox.extend({
        initialize(options) {
            this._super(options);

            this.showRelatedElement(this.value());

            return this;
        },

        initObservable() {
            this._super();

            // Expose boolean properties so other fields can be shown/hidden depending on the toggle state
            this.observe('useNativeDimensions');
            this.observe('useManualDimensions');

            return this;
        },

        onUpdate(value) {
            this.showRelatedElement(value);

            return this._super();
        },

        showRelatedElement: function (value) {
            this.useNativeDimensions(value === this.valueMap.true);
            this.useManualDimensions(value !== this.valueMap.true);

            return this;
        }
    });
});
