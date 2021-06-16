# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

[Unreleased]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.4...master

## [1.1.4] - 2021-06-16
### Added
- **ViewModel Cache Tags

  ViewModels can now contain cache tags which are added to the block that renders output from that ViewModel.
  This enables you to, for example, render menu-items in any block and add the cache tags of the menu items to that block.
  
  This requires you to add a getIdentities method to the ViewModel you use to load in identities with.

  See commit [`cd8c38bb`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/cd8c38bb85f52641759d1ec0e70ee2c2f6062c99)

- **Cookie Consent now prevents cookies from being stored until accepted**

  It is now possible to prevent the theme to store cookies in the browser if the client doesn't give consent and the Magento 2 cookie restriction feature is enabled.
  
  The cookies are stored in a temp js object window.cookie_temp_storage and only if the user already gave the consent (information stored in  user_allowed_save_cookie cookie) or after an explicit confirmation (the banner of hyva-themes/magento2-default-theme/Magento_Cookie/templates/notices.phtml), they will be saved.
  
  There is also a config to save necessary cookies that don't require confirmation (the e-commerce without these cookies cannot work, eg: form_key), stored in the window.cookie_consent_configuration object.
  
  In this object is also possible to add different categories to the cookie that requires different logic to be handled; the variable cookie_consent needs to be properly set for this.
  Cookies not declared in the cookie_consent_configuration are saved only after the confirmation.

  See commit [`a19f65d4`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/a19f65d4b9085bfd027fe376ea764b5801c1a955)

  Thanks to Mirko Cesaro (Bitbull) for contributing

- **ProductPage ViewModel now has productAttributeHtml() method**

  This method parses template tags (directives) for attributes so that attributes like `description` now render store variables and other `{{directives}}`

  See `src/ViewModel/ProductPage.php`
  
  Thanks to Vincent MARMIESSE (PH2M) for contributing

- **Cart GraphQl queries now contain available shipping methods**

  Added available methods with and without vat
  Added method_code to allow matching
  
  See `src/ViewModel/Cart/GraphQlQueries.php`

  Thanks to Alexander Menk (imi) for contributing.

- **Added EmailToFriend viewModel** loading configuration values for SendFriend functionality
  
  See `src/ViewModel/EmailToFriend.php`
  
- **Added `format` method to ProductPrice view model**

  When calling $priceViewModel->currency($amount), the amount is treated as the
  base currency and converted to the current currency before being formatted.

  If $amount already is in the store view currency, this leads to double conversions.

  Now, the pricecurrency format() method is exposed through the view model.

  See `src/ViewModel/ProductPrice.php`
  
- **Add view model for easy use of generic slider**
  
  A new `Slider` View Model was added that allows you to create more generic sliders in conjection with a generic slider phtml file in `Magento_Theme::elements/slider-php.phtml` and `Magento_Theme::elements/slider-gql.phtml`

  See `src/ViewModel/Slider.php`

### Changed
- none

### Removed
- none

[1.1.4]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.3...1.1.4

## [1.1.3] - 2021-05-07
### Added
- **Fix: polyfill baseOldPrice in priceinfo for Magento versions < 2.4.2**

  Hyva Themes 1.1.2 depends on the baseOldPrice being set, but that property only was added in Magento 2.4.2. This
  Release adds compatibility for older Magento versions by polyfilling the price info baseOldPrice if it doesn't exist.
  
### Changed
- **Deprecated \Hyva\Theme\ViewModel\ProductPrice::setProduct()**

  Pass the $product instance as an argument to price methods instead of using internal state.
  This improves reusability of templates regardless of the order they are rendered in.
  The method still is preserved for backward compatibility, but is no longer used by default Hyva theme.
  https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/37

### Removed
- none

[1.1.3]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.2...1.1.3

## [1.1.2] - 2021-05-03
### Added
- **GQL support for customOption of `file` type**

  See `src/Model/CartItem/DataProvider/CustomizableOptionValue/File.php`, `src/Plugin/QuoteGraphQL/CustomizableOptionPlugin.php`

- **GQL added custom options for Virtual, Downloadable and Bundle to cart**
  
  See `src/ViewModel/Cart/GraphQlQueries.php`
  Configurables are not yet included due to a core-bug that will be fixed in 2.4.3: https://github.com/magento/magento2/issues/31180

- **customOptions viewModel** that allows to override the pthml file for customOptions of dropdown/multiselect/radio/checkbox types.
  
  By default, Magento renders `select` custom-options with a toHtml() method in `\Magento\Catalog\Block\Product\View\Options\Type\Select\Multiple`. This can now be replaced with a proper pthml file using this viewModel.

  See `src/ViewModel/CustomOption.php`
  
- **ProductPrices viewModel** that calculates product prices, tier prices and custom options on Product Detail pages.

  See `src/ViewModel/ProductPrice.php`

### Changed
- none

### Removed
- none

[1.1.2]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.1...1.1.2

## [1.1.1] - 2021-04-08
### Added
- **SwatchRenderer ViewModel**
  
  Used to determine wheter an attribute should render as swatch: `isSwatchAttribute($attribute)`
  
  See `src/ViewModel/SwatchRenderer.php`

### Changed
- none

### Removed
- none

[1.1.1]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.0...1.1.1

## [1.1.0] - 2021-04-02
### Added
- **Icon Directive** `{{icon}}` to render SVG icons from PHTML files or CMS content
   
  Icons can now be rendered with a directive: `{{icon "heroicons/solid/shopping-cart" classes="w-6 h-6" width=12 height=12}}`
  
  See `src/Model/Template/IconProcessor.php`
  
  Thanks to Helge Baier (integer_net) for contributing
  
- **Current Category Registry ViewModel**
  
  The current category can now be fetched with: `$viewModels->require(\Hyva\Theme\ViewModel\CurrentCategory::class);`
  
  See `src/ViewModel/CurrentCategory.php`
  
  Thanks to Gennaro Vietri (Bitbull) for contributing
  
- **Cart Items ViewModel** that loads the items currently in cart
  
  See `src/ViewModel/Cart/Items.php`
  
  Thanks to Vincent MARMIESSE (PH2M) for contributing
  
- **Compare Products ViewModel** that loads preferences for showing compared products in the sidebar.
  Additionally, product images are added to the compared product in customer section data.
  
  See `src/ViewModel/ProductCompare.php` and `src/Plugin/CompareCustomerData/AddImages.php`
  
  Thanks to Timon de Groot (Mooore)
  
- **Customer Registration ViewModel** that loads `isAllowed()` for customer registration from config
  
  See `src/ViewModel/CustomerRegistration.php`
  
  Thanks to Barry vd. Heuvel (Fruitcake) 

- **Currency ViewModel** that retrieves current currency (and currency symbol) and currency-switcher url/postData
  
  See `src/ViewModel/Currency.php`

- **ProductListItem ViewModel** that retrieves formatted product prices in product lists
  
  See `src/ViewModel/ProductListItem.php`

- **StoreSwitcher ViewModel** that loads available groups/stores for the store/language switchers.
  
  See `src/ViewModel/StoreSwitcher.php`

- **Built-With header added**

  We've added a `x-built-with: Hyva Themes` header to pages on the frontend that are rendered with Hyvä.

### Changed
- **Customer Section data invalidation** on store-switch. The JavaScript variable `CURRENT_STORE_CODE` is now added to `src/view/frontend/templates/page/js/variables.phtml` and checked against in `src/view/frontend/templates/page/js/private-content.phtml` to invalidate customer section-data when switching between stores.
  
  Thanks to Gennaro Vietri (Bitbull) for contributing

- **FormKey retrieval is now global under hyva.getFormKey()**
  
  Form Keys are no longer generated in `src/view/frontend/templates/page/js/cookies.phtml` (though still initialized from here).
  
  `hyva.getFormKey()` can now be used globally instead of `document.querySelector('input[name=form_key]').value`. This will be refactored in the default theme in the future.
  
  Thanks to Gennaro Vietri (Bitbull) for contributing

- **formatPrice() is now a global function hyva.getFormKey()**
  
  `hyva.getFormKey()` has been added to `src/view/frontend/templates/page/js/hyva.phtml`

- **SvgIcons are now cached per Theme**
  
  See `src/ViewModel/SvgIcons.php`
  
  Thanks to Paul van der Meijs (RedKiwi) for contributing
  
- **CSP Whitelist added for unsplash.com** Magento_Csp can now be enabled by default. Previously, the unsplash.com images on the homepage would throw console errors.
  
  Thanks to Aad Mathijssen (Isaac) for requesting this
  
- **SVG files with preset width and height now work with SvgIcons**
  
  Previously, an error would be thrown: 
  
  ```Exception #0 (Exception): Warning: SimpleXMLElement::addAttribute(): Attribute already exists in /Dev/www/chlobo/vendor/hyva-themes/magento2-theme-module/src/ViewModel/SvgIcons.php on line 84```
  
  Thanks to Fabian Schmengler (integer_net) for contributing
  
- **The `ProductInterface` in GraphQL calls now contain `visibility` and `status`**
  
  We can now filter product-lists, loaded through GraphQL, by visibility code and status. This has been added because 'linked products' (upsells, cross-sells, upsells) are not filtered by visibility in store.

### Removed
- **`<script>` tags no longer contain the `defer` attribute**
  
  Since these have no effect...

[1.1.0]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.0.7...1.1.0  

## [1.0.7] - 2021-02-15
### Added
- Added readme
- Added this changelog

### Changed
- Fix compare configuration path

### Removed
- none

[1.0.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.0.6...1.0.7
