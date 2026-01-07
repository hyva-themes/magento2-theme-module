/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/lib/validation/utils'
], function ($, _, utils) {
    'use strict';

    /**
     * Validate string could be a TailwindCSS class
     *
     * This function overrides the original PageBuilder CSS class validator to allow TailwindCSS classes with : # . [] and more
     *
     * @param {String} str
     * @return {Boolean}
     */
    function validateTailwindCssClass(str) {
        return (/^[a-zA-Z0-9 _(),.:![\]#\-\d\/%]+$/i).test(str);
    }


    /**
     * Override the original PageBuilder validate-css-class validator to allow TailwindCSS classes
     *
     * @param {Function} validator
     * @param {String} ruleName
     */
    return function (validator) {

        validator.addRule(
            'validate-css-class',
            function (value) {
                if (utils.isEmptyNoTrim(value)) {
                    return true;
                }

                return validateTailwindCssClass(value);
            },
            $.mage.__('Please enter a valid CSS class.')
        );
        return validator;
    };
});
