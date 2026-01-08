/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/single-checkbox',
    'Magento_PageBuilder/js/config',
    'uiRegistry'
], function (SingleCheckbox, pageBuilderConfig, registry) {
    'use strict';

    return SingleCheckbox.extend({
        initialize(options) {
            this._super(options);

            // The regular configured default value from pagebuilder_base_form_with_background_attributes.xml is
            // overridden by ko listeners, so they don't take effect.

            // If this is a new element, enforce the default value after the listeners are initialized.
            const dataProvider = registry.get(options.provider);
            if (dataProvider && dataProvider.data.background_lazy_load === '') {
                this.default = pageBuilderConfig.getConfig('background_lazy_load_default')
                    ? this.valueMap.true
                    : this.valueMap.false
                this.value(this.default);
                this.initialValue = this.default;
                this.checked(this.getReverseValueMap(this.default));
                this.initialChecked = this.checked();
            }
        }
    });
});
