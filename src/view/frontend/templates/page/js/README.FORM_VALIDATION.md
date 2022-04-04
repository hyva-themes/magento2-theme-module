# Advanced JavaScript validation

By default, _hyva_ uses built-in browser form validation. In most cases that's enough, but you may need something more advanced (eg. for custom validation messages in chosen place). That's why there is an optional advanced JavaScript validator, which you enable in you project in case of need.

## Getting started

### 1. Enable advanced JS validation

Load the advanced form validation on the pages your want to use it (for example `default_hyva.xml` for all pages).

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="after.body.start">
            <!-- ... default hyva scripts ... -->
            <block name="advanced-form-validation" template="Hyva_Theme::page/js/advanced-form-validation.phtml" after="-"/>
        </referenceContainer>
    </body>
</page>
```

### 2. Init form validation

You can bind form with `hyva.formValidation($el)` function.  
You can perform validation on `@submit` event or on `@change`.

```html
<form x-data="hyva.formValidation($el)">
```

Default submit action is preventing form submit event until all fields are correct.  
`novalidate` attribute will be added to _FORM_ element automatically to disable browser default validation. 

### 3. Set custom validators or use browser default

To add custom validator, use this syntax:  

```html
<div class="field">
    <input type="number" data-validate='{"min": 2, "required": true}' />
</div>
```

> It should look like regular JSON object, because we parse data-validate by `JSON.parse`.

You can also use some browser native input attributes (list of supported in table below):  
```html
<div class="field">
    <input type="number" min="2" required />
</div>
```

> Input elements should be wrapper with `.field` container by default - error messages will be placed there.
> It is possible to use custom class name (check out _Advanced settings_ section of this document).


### Available validators

| Name      | Usage examples                                             | Note                                           |
|-----------|------------------------------------------------------------|------------------------------------------------|
| required  | `required`<br/>`data-validate='{"required": true}'`        | Uses browser native validation                 |
| minlength | `minlength="2"`<br/>`data-validate='{"minlength": 2}'`     | Uses browser native validation of text input   |
| maxlength | `maxlength="3"`<br/>`data-validate='{"maxlength": 3}'`     | Uses browser native validation of text input   |
| min       | `min="2"`<br/>`data-validate='{"min": 2}'`                 | Uses browser native validation of number input |
| max       | `max="4"`<br/>`data-validate='{"max": 3}'`                 | Uses browser native validation of number input |
| step      | `step="1"`<br/>`data-validate='{"step": 1}'`               | Uses browser native validation of number input |
| email     | `type="email"`<br/>`data-validate='{"email": true}'`       | Uses Magento email requirements                |
| password  | `type="password"`<br/>`data-validate='{"password": true}'` | Uses Magento password requirements             |
| equalTo   | `data-validate='{"equalTo": "password"}'`                  | Compares field value with other field          |

---

## Examples of usage in templates

```html
<div class="field">
    <input name="first-name" data-validate='{"required": true}' />
</div>

<div class="field">
    <input name="last-name" data-validate='{"maxlength": 20}' required />
</div>

<div class="field">
    <input name="last-name" 
           data-validate='{"maxlength": 20}' 
           data-msg-maxlength="<?= /* @noEscape  */  __('Max length of this field is %1') ?>" 
    />
</div>

<div class="field">
    <input name="city" minlength="3" required />
</div>

```

> `data-msg-VALIDATOR_NAME` attribute allows overwriting default validator message.

### Custom submit function

There is a possibility to merge custom form handling with `hyva.formValidation`:

```html
<form x-data="{...hyva.formValidation($el), ...myForm()}" 
      @submit="myFormSubmit($event)"
>
```

```js
function myForm() {
    return {
        myFormSubmit($event) {
            this.validate().then(() => {
                this.$el.submit();
            }).catch((elements) => {
                /**
                 * not valid elements are provided as an argument
                 * eg. to scroll to problematic field
                 */
                $event.preventDefault;
            });
        }
    }
}
```

### Add new validation rules

To add new validator, use `hyva.addFormValidationRule` method and pass new validation function.  
Function name is validator name.

```js
/**
 * Validator arguments
 * @param {string} value - input value
 * @param {*} options - additional options passed by data-validator='{"validatorName": {"option": 1}}'
 * @param {Object} - Alpine.js object which contains element, validators and validation state
 * @param {Object} - Alpine.js object with all form validators and methods used for validation
 */
hyva.addFormValidationRule(function phone(value, options, field, context) {
    const phoneNumber = value.trim().replace(' ', '');
    if (phoneNumber.length !== 9) {
        // return message if validation fails;
        return '<?= /* @noEscape */ __("Enter correct phone number, like XXX XXX XXX") ?>';
    } else {
        // return true if validation passed
        return true;
    }
});
```

### Validate single field

`validateField`

```html
@input="validateField"
```

---

## Advanced settings

### Initialization options

There is a possibility to customize class names used by validator, by passing object with options as a second argument of `hyva.formValidation`.

```html
<form x-data="hyva.formValidation($el, {fieldWrapperClassName: 'fld', messagesWrapperClassName: 'msg'})"></form>
```

Default values:

```json
{
    "fieldWrapperClassName": "field",
    "messagesWrapperClassName": "messages",
    "validClassName": "field--success",
    "invalidClassName": "field--error"
}
```

### Validation with dependency field

Sometimes field validation is dependent on another field. For example purposes, let's create conditional validation for ZIP code:

```html
<div class="field">
    <select name="country" required>
        <option selected hidden value="">Choose country</option>
        <option value="ch">Switzerland</option>
        <option value="fr">France</option>
        <option value="de">Germany</option>
        <option value="nl">The Netherlands</option>
    </select>
</div>
<div class="field">
    <input type="text" name="zip" placeholder="Enter ZIP code" data-validate='{"zip": {}}' />
</div>
```

```js
hyva.addFormValidationRule(['zip', function(value, options, field, context) {
    const rules = {
        ch: ['^(CH-)?\\d{4}$', '<?= /* @noEscape */ __("Switzerland ZIPs must have exactly 4 digits: e.g. CH-1950 or 1950") ?>'],
        fr: ['^(F-)?\\d{5}$', '<?= /* @noEscape */ __("France ZIPs must have exactly 5 digits: e.g. F-75012 or 75012") ?>'],
        de: ['^(D-)?\\d{5}$', '<?= /* @noEscape */ __("Germany ZIPs must have exactly 5 digits: e.g. D-12345 or 12345") ?>'],
        nl: [
            '^(NL-)?\\d{4}\\s*([A-RT-Z][A-Z]|S[BCE-RT-Z])$',
            '<?= /* @noEscape */ __("Netherland ZIPs must have exactly 4 digits, followed by 2 letters except SA, SD and SS") ?>'
        ]
    };
    context.validateField(context.fields['country']);

    if (context.fields['country'].state.valid) {
        const country = context.fields['country'].element.value;
        const rule = new RegExp(rules[country][0], '');
        if (!rule.test(value)) {
            return rules[country][1];
        } else {
            return true;
        }
    } else {
        return '<?= /* @noEscape */ __("Select country first") ?>'
    }
}
])

```
