# Advanced JavaScript validation

By default, _hyva_ uses built-in browser form validation. In most cases that's enough, but you may need something more advanced (eg. for custom validation messages in chosen place). That's why there is an optional advanced JavaScript validator, which you enable in you project in case of need.

## Getting started

### 1. Enable advanced JS validation

Add module to `default_hyva.xml`:

```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="before.body.end">
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

### 3. Set validators

Add `data-validate` attribute to form element.

```html
<div class="field">
    <input name="qty" data-validate='{"value-range": [20, 400], "required": true}' />
</div>
```
> Input elements should be wrapper with `.field` container by default - error messages will be placed there.
> It is possible to use custom class name (check out _Advanced settings_ section of this document).

> It should look like regular JSON object, because we parse data-validate by `JSON.parse`.

_Boolean_ and _Array_ are accepted as a value of validator. You can use these values for validator logic,  
and they are used as a values to replace translation variables (example below).

## Available validators

```text
required
minLength
maxLength
minValue
maxValue
email
number
password
```

---

## Examples of usage

```html
<div class="field">
    <input name="first-name" data-validate='{"required": true}' />
</div>

<div class="field">
    <input name="last-name" data-validate='{"maxLength": 20}' required />
</div>

<div class="field">
    <input name="last-name" data-validate='{"maxLength": 20}' data-msg-maxlength="<?= /* @noEscape  */  __('Max length of this field is %1') ?>" />
</div>

<div class="field">
    <input name="city" minlength="3" required />
</div>

```

> Some input attributes (required, min, max, minvalue, maxvalue) are supported by default.  
> There is no need to define `data-validation` if one of these is defined.  
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
                 * not valid elements provided as an argument
                 * eg. to scroll to problematic field
                 */
                $event.preventDefault
            });
        }
    }
}
```

### Add new validation rules

```js
hyva.addFormValidationRule('nip', {
    message: 'provide correct nip',
    validator(value, options) {
        // custom validation here
        return true;
    }
});
```

### Validate single field

`validateField`

```html
@input="validateField"
```


## Advanced settings

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
