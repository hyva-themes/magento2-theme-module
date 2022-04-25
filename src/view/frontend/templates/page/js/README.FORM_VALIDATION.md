# Advanced JavaScript validation

By default, _hyva_ uses built-in browser form validation. In most cases that's enough, but you may need something more 
advanced (for example, to display custom validation messages in a specific location). 
That's why there is an optional advanced JavaScript validator, which can be used as needed.

## Getting started

### 1. Enable advanced JS validation

Load the advanced form validation on the pages your want to use it using layout XML by applying the layout handle `hyva_form_validation`.

```xml
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <update handle="hyva_form_validation"/>
</page>

```

### 2. Initialize the form validation Alpine.js component

If you only need the form validation on frontend logic, use `x-data="hyva.formValidation($el)"` to initialize the component.

```html
<form x-data="hyva.formValidation($el)">
```

If additional logic is needed, merge the form validation component with your custom component properties:

```html
<form x-data="{...initMyCustomComponent(), ...hyva.formValidation($el)}">
```

You can trigger the validation of the whole form using the Alpine.js `@submit` event with the `onSubmit` method.

```html
<form x-data="hyva.formValidation($el)" @submit="onSubmit">
```

Validation of individual fields can be triggered using the `@change` event with the `onChange` method.

```html
<div class="field">
    <input name="example" data-validate='{"required": true}' @change="onChange" />
</div>
```

Note: the default submit action is prevented to stop the form from submitting until all fields are correct.  
For this purpose the `novalidate` attribute is added to the _FORM_ element automatically.


### 3. Define the validation rules

Validation rules can be added using `data-validate` or using HTML5 browser constraints API.

To add a custom validator, use this syntax:  

```html
<div class="field">
    <input type="number" data-validate='{"min": 2, "required": true}' />
</div>
```

Use regular JSON object notation (not plain JavaScript), because the content of the `data-validate` attribute is parsed with `JSON.parse()`.

You can also use some browser HTML5 constraint attributes (see below for a list of supported native validation rules):  

```html
<div class="field">
    <input type="number" min="2" required />
</div>
```

Input elements should be wrapped by a container element with a `.field` class.
It is also possible to use a custom class name instead of `class="field"` (check out the _Advanced settings_ section below).

Should the container element be missing, it will be generated automatically if error messages need to be displayed.


### Available validators

| Name      | Usage examples                                             | Note                                             |
|-----------|------------------------------------------------------------|--------------------------------------------------|
| required  | `required`<br/>`data-validate='{"required": true}'`        | Uses the browser constraint API validation       |
| minlength | `minlength="2"`<br/>`data-validate='{"minlength": 2}'`     | Uses the browser constraint API on text input    |
| maxlength | `maxlength="3"`<br/>`data-validate='{"maxlength": 3}'`     | Uses the browser constraint API on text input    |
| min       | `min="2"`<br/>`data-validate='{"min": 2}'`                 | Uses the browser constraint API on number inputs |
| max       | `max="4"`<br/>`data-validate='{"max": 3}'`                 | Uses the browser constraint API on number inputs |
| step      | `step="1"`<br/>`data-validate='{"step": 1}'`               | Uses the browser constraint API on number inputs |
| email     | `type="email"`<br/>`data-validate='{"email": true}'`       | Uses the Magento email validation regex          |
| password  | `type="password"`<br/>`data-validate='{"password": true}'` | Uses the Magento password validation regex       |
| equalTo   | `data-validate='{"equalTo": "password"}'`                  | Compares a field value with other field value    |

Custom validators can be added using the method `hyva.formValidation.addRule` (see below).

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
           data-msg-maxlength='<?= /* @noEscape  */  __("Max length of this field is "%0"") ?>'
    />
</div>

<div class="field">
    <input name="city" minlength="3" required />
</div>

```

The `data-msg-VALIDATOR_NAME` attribute allows overriding the default validator message.  
The `%0` placeholder will be replaced with the validation rule argument.



### Validating a single field during user interaction

Use the `onChange` callback with the `input` event to trigger field validation during user interaction.

```html
<input @input="onChange" .../>

```

### Custom submit function

Custom form submission can be accomplished with `hyva.formValidation`:

```html
<form x-data="{...hyva.formValidation($el), ...initMyForm()}" 
      @submit="myFormSubmit"
>
```

```js
function initMyForm() {
    return {
        myFormSubmit(event) {
            event.preventDefault();

            this.validate()
                .then(() => {
                    // all fields validated
                    event.target.submit()
                })
                .catch((invalid) => {
                    if (invalid.length > 0) {
                        invalid[0].focus();
                    }
                });
        }
    }
}
```

### Adding new validation rules

To add new validation rule, use the `hyva.formValidation.addRule` method and pass new validation function.  
The first argument is the validator name and the second argument is the validator rule callback.

A validator rule callback is a function that will receive four arguments:

```js
/**
 * Validator arguments
 * @param {string} value - input value
 * @param {*} options - additional options passed by data-validator='{"validatorName": {"option": 1}}'
 * @param {Object} field - Alpine.js object which contains element, validators and validation state // TODO MAKE CLEARER
 * @param {Object} context - The Alpine.js component instance
 */
hyva.formValidation.addRule('phone', function(value, options, field, context) {
    const phoneNumber = value.trim().replace(' ', '');
    if (phoneNumber.length !== 9) {
        // return message if validation fails;
        return '<?= $escaper->escapeJs(__("Enter correct phone number, like XXX XXX XXX")) ?>';
    } else {
        // return true if validation passed
        return true;
    }
});
```

Validation rule functions should return one of the following values:

* The boolean `true` if the rule is valid.
* A string message if the rule is invalid. The string should describe the failure in a helpful way
* A Promise that resolves to `true` or a message string when the validation completes.  
  See below for more information on asynchronous validation rules.

### Asynchronous validation

Sometimes validation of form values requires asynchronous actions, such as sending a query to a web API and waiting for the response.

This can be accomplished by returning a promise from the validation function.

The form submission is prevented by the onSubmit function until either all validation rules pass or the one of the fields has an invalid value.
For fields with async validators, error messages will be displayed as soon as all field rules have completed.


#### Example async validator rule:

```js
hyva.formValidation.addRule('username', (value, options, field, context) => {
    return new Promise(resolve => {
        // show the user form validation is ongoing, maybe show a spinner
        field.element.disabled = true;

        fetch(this.url + '?form_key=' + hyva.getFormKey(), {
            method: 'post',
            body: JSON.stringify({username: value}),
            headers: {contentType: 'application/json'}
        })
            .then(response => response.json())
            .then(result => {
                if (result.ok) {
                    resolve(true);
                } else {
                    resolve(hyva.strf('The username "%0" is already taken.', value));
                }
            })
            .finally(() => {
                // indicate validation has finished, remove spinner if shown
                field.element.disabled = false;
            });
    });
});
```

---

## Advanced settings

### Initialization options

To customize the class names used by validator rules, passing an object with options as a second argument to `hyva.formValidation`.

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

### Validation rules with a dependency on another field

Sometimes field validation is dependent on another field. For example purposes, let's create conditional validation for ZIP codes:

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
hyva.addFormValidationRule('zip', function(value, options, field, context) {
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
})
```

### Custom non-error messages

Sometimes it is necessary to display additional messages besides validation failures.  
This can be accomplished using the `context.createMessage(field, messages)` method.  

The `createMessage` method can take a single message string or an array of messages.

The following example shows the number of characters remaining.  
If more characters are added, the validation fails.


```php
<script>
hyva.formValidation.addRule('remaining', (value, options, field, context) => {
    // Remove old message if present
    const container = context.getFieldWrapper(field);
    const oldMsg = container && container.querySelector('.remaining-msg');
    if (oldMsg) oldMsg.remove();
    
    const remaining = parseInt(options) - value.length;
    if (remaining < 0) {
        // Fail validation
        return hyva.strf('%0 character(s) too many', Math.abs(remaining));
    }
    // Add message without failing validation
    const message = hyva.strf('<?= $escaper->escapeJs(__('%0 remaining')) ?>', remaining);
    const newMsg = context.createMessage(field, message);
    newMsg.classList.add('remaining-msg');
    return true;
})
</script>
<form x-data="hyva.formValidation($el)">
<div class="field">
    <label for="example">Example</label>
    <textarea id="example" name="example" data-validate='{"remaining": 10}' @input="onChange"></textarea>
</div>
</form>
```
