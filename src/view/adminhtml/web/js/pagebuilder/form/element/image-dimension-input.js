/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
