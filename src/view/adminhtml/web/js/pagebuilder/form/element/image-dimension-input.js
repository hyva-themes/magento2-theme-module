/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
define([
    'Magento_Ui/js/form/element/abstract'
], function (Input) {
    'use strict';

    return Input.extend({
        initObservable() {
            this._super();

            // Reset value to "" when native_dimensions toggle is set to true
            this.visible.subscribe(isVisible => {
                if (! isVisible) {
                    this.value('');
                }
            });

            return this;
        }
    });
});
