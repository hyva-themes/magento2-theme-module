# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2021-04-08
### Added
- **SwatchRenderer ViewModel**
  
  Used to determine wheter an attribute should render as swatch: `isSwatchAttribute($attribute)`
  
  See `src/ViewModel/SwatchRenderer.php`

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
  

## [1.0.7] - 2021-02-15
### Added
- Added readme
- Added this changelog

### Changed
- Fix compare configuration path

### Removed
- none

[Unreleased]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.1...master
[1.1.1]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.0...1.1.1
[1.1.0]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.0.7...1.1.0
[1.0.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.0.6...1.0.7

