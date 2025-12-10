# Changelog - Theme Module

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [Unreleased]

[Unreleased]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.4.2...main

## [1.4.2] - 2025-12-10

[1.4.2]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.4.1...1.4.2

### Added

-   **Add a method to get single image URLs that will optimize with Hyvä Commerce**  
    For more information, please refer to [merge request #601](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/601)

-   **Add source urls for easier debugging**  
    For more information, please refer to [merge request #600](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/600)

### Changed

-   **Fixed Speculation rules to take account for store codes in URL**  
    For more information, please refer to [issue #494](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/494)

### Removed

-   Nothing removed

## [1.4.1] - 2025-11-17

[1.4.1]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.4.0...1.4.1

### Added

-   **Added installation instructions to README**

-   **Add notes about unsupported PageBilder slider features**  
    Autoplay is no longer supported for A11Y with the CSS SnapSlider introduced in Hyvä 1.4.0.
    Also, Infinite Scroll is not supported by the CSS slider.

### Changed

-   **Fix error when default-theme or default-theme-csp are composer replaced**  
    For more information, please refer to [issue #488](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/488)

-   **Fix table name prefixing in recurring data setup script**  
    Previously, the script was broken if global table name prefixes were configured.

    For more information, please refer to [issue #489](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/489)

-   **Fix a number of minor bugs in the Snap Slider**  
    * Fix scroll-bounce edge case when there is not enough space available.  
    * Exclude `<style>` tags from slider contents.
    * Prevent error when group-pager is set and there are no slides

    For more information, please refer to [issue #490](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/490)

### Removed

-   Nothing removed

## [1.4.0] - 2025-11-10

[1.4.0]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.20...1.4.0

### Added

-   **Add new AlpineJS Slider**  
    This also replaces the Slider viewmodal for this simpler AlpineJS plugin and in so the slider viewmodal has been marked for deprecation.

    For more information, please refer to [issue #98](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/98)

-   **Add AlpineJs based HTML Dialog**  
    For more information, please refer to [issue #481](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/481)

-   **Add Support for bfcache**  
    For more information, please refer to [merge request #567](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/567)

-   **Add Theme Module version in the footer next to the Magento version**  
    For more information, please refer to [issue #358](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/358)

-   **Add speculation rules to the theme as a stable feature**  
    For more information, please refer to [merge request #571](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/571)

### Changed

-   **Fixed TypeError in PageJsDependencyRegistry when block HTML contains Phrase object**  
    For more information, please refer to [issue #480](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/480)
  
### Removed

-   Nothing removed

## [1.3.20] - 2025-11-04

[1.3.20]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.19...1.3.20

### Added

-   Nothing added

### Changed

-   **Resolve critical bug in the `isHyvaTheme` function for static assets**  
    The `isHyvaTheme` function, introduced in 1.3.18 for the Base Layout Reset used by Theme 1.4,
    could cause a 500 error due to a `NULL` theme value.
    This is now resolved by handling this condition.

### Removed

-   Nothing removed

## [1.3.19] - 2025-11-04

[1.3.19]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.18...1.3.19

### Added

-   Nothing added

### Changed

-   **Resolve critical bug in setup script**  
    In 1.3.18, the parent_id of the default theme was set to NULL when the theme was older than 1.4.0.  
    This is now fixed and is only applied if the installed default theme has a version >= 1.4.0.

### Removed

-   Nothing removed

## [1.3.18] - 2025-11-03

[1.3.18]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.17...1.3.18

### Added

-   **Add HyvaThemes service class**
    This class can be used to check if a theme is a Hyvä theme.

### Changed

-   **Disable SVG icon caching by default**  
    For more information, please refer to [issue #462](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/462)

-   **Fix integration tests with default-theme-csp installed**  
    For more information, please refer to [issue #482](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/482)

-   **Fixed Regular and Special Pricing does not work on Product List with Magento 2.4.8**  
    For more information, please refer to [issue #470](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/470)

    Many thanks to Pieter Hoste (Baldwin) for the contribution!

-   **Removed Hard dependency on Magento's PageBuilder module**  
    It is now possible to remove the Magento's PageBuilder using a composer replace.

    For more information, please refer to [issue #478](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/478)

-   **Fixed Auto-deferred Alpine components selector causes issues with nested custom option components**  
    For more information, please refer to [issue #473](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/473)

### Removed

-   Nothing removed

## [1.3.17] - 2025-09-02

[1.3.17]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.16...1.3.17

### Added

-   Nothing added

### Changed

-   **Move Alpine Plugin Files to Base Area**  
    For more information, please refer to [merge request #560](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/560)

-   **Reverse picture tag source inclusion for MediaHtmlProvider**  
    For more information, please refer to [issue #477](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/477)

### Removed

-   Nothing removed

## [1.3.16] - 2025-08-19

[1.3.16]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.15...1.3.16

### Added

-   **Add addPriceData option to ProductList view model**  
    For more information, please refer to [issue #446](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/446)

-   **Add ViewModel to expose Magento versions**  
    For more information, please refer to [issue #476](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/476)

-   **Add optional arguments for ProductListItem view model pricerenderer**  
    For more information, please refer to [merge request #513](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/513)

    Many thanks to Akif Gumussu (Aquive) for the contribution!

### Changed

-   **Avoid duplicate queries to load CSP fetch policies**  
    For more information, please refer to [merge request #547](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/547)

    Many thanks to Igor Wulff (Youwe) for the contribution!

-   **Improve Speculation rules exclude list for none subdomains**  
    For more information, please refer to [merge request #554](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/554)

-   **Allow disabling block level caching of product list item templates**  
    For more information, please refer to [issue #466](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/466)

-   **Fix error in Safari 18 for missing Navigation Api**  
    For more information, please refer to [issue #469](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/469)

-   **Fix check if block is cached in RegisterPageJsDependencies**  
    For more information, please refer to [merge request #468](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/468)

-   **Fix CLI command events:generate:module by not checking app state code**  
    For more information, please refer to [issue #467](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/467)

-   **Fix issue with missing tax rates from http context**  
    For more information, please refer to [issue #443](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/443)

    Many thanks to Jakub Idziak (Macopedia) for the contribution!

-   **Improve in-memory path IDs**  
    For more information, please refer to [issue #443](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/443)

    Many thanks to Thomas Klein (ATI4 Group) for the contribution!

### Removed

-   Nothing removed

## [1.3.15] - 2025-07-03

[1.3.15]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.14...1.3.15

### Added

-   **Some new methods to the Media viewmodel that allow more advanced features when the Hyva Commerce media optimization module is installed**  
    For more information, please refer to [merge request #543](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/543)

-   **HyvaCSP View Model to Admin Block Dictionary**  
    For more information, please refer to [merge request #540](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/540)

-   **Add Lucide Icons for Hyvä Commerce**  
    For more information, please refer to [issue #460](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/460)

### Changed

-   **Fixed getPriceDisplayType can return string as config values could also be returned as strings "1", "2", "3" and this would end up with exception**  
    For more information, please refer to [merge request #544](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/544)

-   **Move Alpine Files to Base Area**  
    For more information, please refer to [merge request #538](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/538)

### Removed

-   **Remove private TTL hardcode**  
    For more information, please refer to [issue #441](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/441)

## [1.3.14] - 2025-05-26

[1.3.14]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.13...1.3.14

### Added

-   Nothing added

### Changed

-   **Fixed one more usage of implicit nullable param which is deprecated since PHP 8.4**
    For more information, please refer to [merge request #535](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/535)

    Many thanks to Pieter Hoste (Baldwin) for the contribution!

-   **Resolve a minor difference between Alpine CSP and non Alpine CSP**
    For more information, please refer to [merge request #525](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/525)

### Removed

-   Nothing removed

### Added

-   **Lucide Icons**

    To transition all Hyvä Commerce and Hyvä Projects to a unified icon set,
    we're now introducing Lucide Icons as an alternative to Heroicons.  
    New projects will begin using them immediately,
    and we plan to update the default Hyvä theme with this new icon set in an upcoming release.

    A huge thank you to [Siteation](https://siteation.dev/) for their valuable contribution in creating and sharing this icon pack with us.  
    We also recommend checking out their dedicated [Hyvä Lucide Icons](https://github.com/Siteation/magento2-hyva-icons-lucide) and their other excellent Hyvä icon options.

    For more information, please refer to [issue #460](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/460)

## [1.3.13] - 2025-04-22

[1.3.13]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.12...1.3.13

### Added

-   **Script type to `hyva.activateScripts`**
    For more information, please refer to [issue #445](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/445)

### Changed

-   **Explicitly mark nullable parameters for PHP 8.4 compatibility**
    For more information, please refer to [issue #459](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/459)

-   **Make code to initially show modal dialogs CSP compatible**
    For more information, please refer to [issue #458](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/458)

-   **Improve HtmlPageContent class**
    For more information, please refer to [issue #457](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/457)

    Many thanks to Christoph Hendreich (In Session) for the contribution!

-   **Downgrade AlpineJS to v3.14.3**
    For more information, please refer to [merge request #457](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/527) and [Alpinejs merge request #4509](https://github.com/alpinejs/alpine/pull/4509)

-   **Fix Area code is not set error in CLI commands instantiating HyvaCsp template variable**
    For more information, please refer to [issue #455](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/455)

-   **Fix phpdoc in `src/Model/ViewModelRegistry.php`**
    For more information, please refer to [merge request #483](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/483)

    Many thanks to Frederik Rommel (WEBiDEA) for the contribution!

### Removed

-   Nothing removed

## [1.3.12] - 2025-03-17

[1.3.12]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.11...1.3.12

### Added

-   **Authorize speculation rules script tag on page without unsafe-inline policy**

    The `<?php $hyvaCsp->registerInlineScript() ?>` call was previously missing.  
    For more information, please refer to [issue #449](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/449)

    Many thanks to Christoph Hendreich (In Session) for the contribution!

-   **Add frontend extension point for section data**

    A new method `window.processSectionDataBeforeDispatch` can be intercepted by extensions to mutate section data before it is dispatched.

    For more information, please refer to [merge request #523](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/523)

### Changed

-   **Fix error handling existing script tag attributes with spaces in the value when injecting a nonce attribute**

    For more information, please refer to [issue #450](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/450)

-   **Improve hyva.createBooleanObject resilience**

    When migrating non-csp alpine components, it could happen that the value property was accidentally overwritten using the `=` operator.
    This change protects against this and will assign the value to the internal value property instead.

    For more information, please refer to [issue #448](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/448)

### Removed

-   Nothing removed

## [1.3.11] - 2025-03-06

[1.3.11]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.10...1.3.11

### Added

-   **Support strict CSP mode**

    These changes support using a Hyvä theme or other Hyvä products like the checkout without the Content-Security-Policies
    `unsafe-eval` and `unsafe-inline`. However, the required changes to the theme or other products are not part of this release.  

    Most notably, this release will use Alpine-CSP if the unsafe-eval script-src CSP is not present.

    For more information, please refer to the Hyvä CSP Developer documentation for [theme](https://docs.hyva.io/hyva-themes/writing-code/csp/index.html) and the [checkout](https://docs.hyva.io/checkout/hyva-checkout/devdocs/csp/index.html).

### Changed

-   **Change init-external-scripts event trigger**
  
    Replaced mouseover with mousemoved event as one of the triggers for the init-external-scripts event, since the other one was triggered on page-load in page speed insights tests now.

    For details, please refer to [merge request #512](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/512).

    Many thanks to Sergiy Pikhterev (Transform-Agency) for the contribution!

### Removed

-   Nothing removed

## [1.3.10] - 2024-12-06

[1.3.10]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.9...1.3.10

### Added

-   **Add experimental view transitions**

    View Transitions is a new browser API allowing for smooth and engaging animations during page transitions.  
    This can significantly improve user experience by providing visual cues and reducing the perception of page load times.  
    The feature is disabled by default. It can be enabled in the "Hyvä Themes > Experimental > Enable View Transitions" system configuration.

    Further resources:
    - caniuse: <https://caniuse.com/mdn-css_at-rules_view-transition>  
    - Release notes of Chrome 126: <https://developer.chrome.com/blog/new-in-chrome-126#cross-document-transitions>  
    - Learn more about cross-document View Transitions: <https://developer.chrome.com/docs/web-platform/view-transitions/cross-document>    

    For details, please refer to [merge request #467](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/467).

-   **Lazy load PageBuilder slider background images**

    Lazy loading can be enabled for each slider background individually.  
    The default can be configured in the system configuration at "Hyvä Themes > PageBuilder > Images > Enable lazy-loading by default for background images". 

    For details, please refer to [merge request #472](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/472).

-   **Allow lazy loading PageBuilder images / add image dimensions**

    For details, please refer to [merge request #475](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/475).

-   **Add position `none` to ModalBuilder**

    This allows the manual positioning of the modal box.

    For details, please refer to [merge request #435](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/435).

    Many thanks to Andy Eades (elevate web) for the contribution!

-   **Add Image view model**

    The view model only has the method `getPlaceholderImageUrl`. The method is required by Hyvä Enterprise.

    For details, please refer to [merge request #462](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/462).

-   **Add Vimeo player host to CSP policy**

    For details, please refer to [merge request #468](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/468).

    Many thanks to Ole Schäfer (Customgento) for the contribution!

-   **Add Date and Locale PHP view models**

    The view models are used in Hyvä Enterprise and could also be useful in general.  
    - `Date::getDateYMD(?string $date = null) `: Return the input date or the current date in UTC timezone ('Y-m-d')  
    - `Locale::getLocale()`: return the store locale, for example, `en-US`.  

    For details, please refer to [merge request #470](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/470).

-   **Add an explanatory comment in the template for crossorigin attribute on script element**

    For details, please refer to [merge request #473](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/473).

    Many thanks to Alex Galdin (IT-Delight) for the contribution!

-   **Allow overriding hyva.formatPrice grouping and decimal separator with custom options**

    Previously, customizing the decimal or thousands separator character required overriding the `formatPrice` function.  
    Now the `groupSeparator` or `decimalSeparator` options can be used instead. For example:  
    ```js
    hyva.formatPrice(price, false, {decimalSeparator: ' : '})
    ```

    For details, please refer to [issue #378](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/378).

    Many thanks to Alex Galdin (IT-Delight) for the contribution!

-   **Add Notice to Old CAPTCHA Config Area**

    For details, please refer to [issue #415](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/415).

-   **Add customer attribute validate_rules frontend validations**

    The new validation rules are used by Hyvä Enterprise but could be useful in a broader context.

    For details, please refer to [issue #357](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/357).

-   **Add config option to disable homepage demo content**

    For details, please refer to [merge request #490](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/490).

-   **Add Google Maps API view model**

    The GoogleMapsApi view model provides all the public methods of the `Magento\PageBuilder\Block\GoogleMapsApi` core class.  
    It allows rendering the PageBuilder Google Maps template using a generic Template block.

    For details, please refer to [merge request #495](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/495).

### Changed

-   **Upgrade Alpine.js from 3.12.3 to 3.14.6**

    For details on what changed, please refer to the [Alpine.js release notes](https://github.com/alpinejs/alpine/releases).

-   **Fix PageBuilder Products content-type admin preview**

    Previously, the preview of product carousels or product grids in PageBuilder was broken, if a Hyvä frontend theme was used.

    For details, please refer to [merge request #478](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/478).

-   **Bundle mollie payments integration**

    For details, please refer to [merge request #497](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/497).

-   **Allow SVG Icon titles containing ampersand**

    Previously this caused an error to be thrown.

    For details, please refer to [issue #391](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/391).

-   **Fix PHP type error on `getConfiguredMaxCrosssellItemCount`**

    For details, please refer to [issue #393](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/393).

    Many thanks to Emils Malovka (magebit) for the contribution!

-   **Improve browser speculation rules**

    Speculation Rules no longer cause an error in Microsoft Edge.  
    Also, the default speculation rules now include cached pages without a .html suffix.  
    Finally, `type="speculationrules"` is used instead of JavaScript to render the rules.

    For details, please refer to [merge request #466](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/466).

    Many thanks to David Lambauer (run-as-root) for contributing to the improvements!

-   **Fix hyva-themes.json generation during setup:upgrade on Magento 2.4.7+**

    For details, please refer to [issue #399](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/399).

    Many thanks to Pieter Hoste (Baldwin) for the contribution!

-   **Use x-defer="idle" for cookie-banner to make it more reliable**

    For details, please refer to [merge request #492](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/492).

-   **Switch from getBaseUrl to getDirectUrl in LogoPathResolver**

    This improvement allows for easier modifications via plugins.

    For details, please refer to [merge request #476](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/476).

    Many thanks to Tjitse Efdé (Vendic) for the contribution!

-   **Fix SVG icon view model accidentally changing hex color values while disambiguating IDs**

    For details, please refer to [issue #362](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/362).

-   **Skip CSS and JS minification for assets from Hyvä Themes**

    The minification implementation in core Magento is not beneficial (as modern HTTP compression does a better job), and in some cases introduces incompatibilities with Hyvä code.

    For details, please refer to [merge request #482](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/482).

-   **Change default-section-data script type so it does not confuse Chrome on Linux**

    For details, please refer to [issue #409](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/409).

-   **Set version cookie to fix Login as customer from admin**

    For details, please refer to [issue #413](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/413).

-   **Access cache_lifetime with the magic method in case it is implemented on a block**

    For details, please refer to [issue #395](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/395).

-   **Merge records with duplicate src path in hyva-themes.json**

    For details, please refer to [issue #382](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/382).

-   **Exclude tailwind container query classes from being masked**

    For details, please refer to [issue #419](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/419).

-   **Allow specifying the product attribute to render using the attribute code as a string argument**

    For details, please refer to [issue #424](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/424).

-   **Fix caching of block identities in block_html cache during ESI requests**

    For details, please refer to [issue #418](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/418).

-   **Only apply page JavaScript dependencies to Hyvä themes**

    Third-party extensions using the feature previously could cause side effects during email rendering.

    For details, please refer to [issue #429](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/429).

### Removed

-   Nothing removed

## [1.3.9] - 2024-05-10

[1.3.9]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.8...1.3.9

### Added

-   Nothing added

### Changed

-   **Fixed: Default section data not dispatched on reload**

    Previously the default section data for new visitors was only dispatched once. This only affected visitors without a session.

    For details, please refer to [merge request #457](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/457).

-   **Fixed phpdoc type for class-string in ViewModelRegistry**

    For details, please refer to [merge request #458](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/458).

    Many thanks to Thomas Hauschild (E3n) for the contribution!

### Removed

-   **Remove accidental aria-hidden on SVG attributes**

    In version 1.3.6, the icon update unintentionally included an additional `aria-hidden="true"` attribute from the heroicon source on all bundled heroicons. This attribute is now removed so it is possible to use the SVG icons together with accessible technology.

    For details, please refer to [issue #387](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/387).  

## [1.3.8] - 2024-04-25

[1.3.8]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.7...1.3.8

### Added

-   Nothing added

### Changed

-   **Move the General section to the top of Hyvä system configuration setting sections**

    For details, please refer to [issue #363](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/363).  

    Many thanks to Ruud van Zuidam (Siteation) for the contribution!

-   **Verify the form key for customer/ajax/login after the fallback theme is applied**

    This plugin sort order change is part of a fix for the guest login in checkout for the Luma fallback checkout.

    For details, please refer to [merge request #452](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/452).

-   **Exclude products not visible in catalog from custom sliders**

    For details, please refer to [issue 368](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/368).

### Removed

-   Nothing removed

## [1.3.7] - 2024-04-08

[1.3.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.6...1.3.7

### Added

-   **Add Alpine.js x-defer directive**

    This new directive allows deferring the initialization of an alpine component until a given condition. Possible attribute
    values are `interact`, `intersect`, `idle`, and `event:eventname`.  
    It can help reducing the TBT on pages with many Alpine components.
    The theme-module automatically injects the directive into a number of components with JavaScript.  
    These automatically deferred components can be configured with both layout XML and in the Magento system configuration.  

    For details, please refer to [merge request #444](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/444) and [the x-defer documentation](https://docs.hyva.io/hyva-themes/view-utilities/alpine-defer-plugin.html).

-   **Add Alpine.js x-ignore directive**

    The x-ignore directive - available in Alpine v3 - has been backported to Alpine v2 for Hyvä 1.3.7.  
    It is utilized in the x-defer directive implementation.

    For details, please refer to [merge request #444](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/444) and [the x-defer documentation](https://docs.hyva.io/hyva-themes/view-utilities/alpine-ignore-plugin.html).

-   **Add PHP View Model to support Magento_OrderCancellationUi**

    For details, please refer to [merge request #445](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/445).

-   **Speculation-rules-api Page Pre-Render Experiment**

    The new speculation rule browser API allows for an improved user experience by pre-rendering pages a user is likely to visit next based on JSON based rules. For more information on the browser API please visit [developer.chrome.com/docs/web-platform/prerender-pages](https://developer.chrome.com/docs/web-platform/prerender-pages) and [nitropack.io/blog/post/speculation-rules-api](https://nitropack.io/blog/post/speculation-rules-api).  

    By default the feature is disabled. It can be enabled in the system configuration found at  
    *Hyvä Themes > Experimental > Experimental Features > Enable Preloading Speculation Rules*

    For details, please refer to [merge request 448](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/448).

-   **Call IsCaptchaEnabledInterface::isCaptchaEnabledFor for extension compatibility**

    Previously the recaptcha implementation only accessed the system configuration values directly.  
    By also using the interface compatiblity with related third party modules is improved.

    For details, please refer to [issue 356](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/356).

### Changed

-   **Gracefully handle browsers without intersection observer**

    Now intersection observer callbacks will fall back to executing immediately on supported browsers that don't
    implement InteractionObserver natively (currently Mobile Opera v73 is the only one of the supported browsers
    missing InteractionObserver).

    For details, please refer to [merge request 449](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/449). 

-   **Fix default section data generation**

    Previously the default section data implementation introduced in 1.3.6 caused some issues with the Magento_InstantPurchase module under some circumstances as well as potentially causing messages not to be shown to the customer.

    For details, please refer to [issue 371](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/371).

### Removed

-   Nothing removed

## [1.3.6] - 2024-03-28

[1.3.6]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.5...1.3.6

### Added

-   **Allow JS to be rendered on a page only when it is needed**

    This feature enables several performance improvements on product listing pages.  
    JavaScript can now be rendered on a page only when it is required.  

    For details, please refer to [merge request #425](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/425).

-   **Add hyva.activateScripts method**

    The `hyva.activateScripts` method can be used to have a browser process scripts in HTML snippets from Ajax reponses.

    For details, please refer to [merge request #434](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/434).

-   **Make the amount of crosssell items configurable in admin**

    For details, please refer to [issue #257](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/257) and the associated merge request.

    Many thanks to Antonio Carboni (Magenio) for the contribution!

-   **Enable the title attribute for SVG icons in CMS content**

    Allow icon directives in the backend to assign a title, e.g. `{{icon "heroicons/solid/shopping-cart" classes="w-12 h-12" title="Shopping Cart"}}`

    For details, please refer to [merge request #426](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/426).

    Many thanks to Andreas Pointner (Copex) for the contribution!

-   **Add formatting options argument to hyva.formatPrice()**

    This change allows for easier customization of frontend price rendering.  

    For details, please refer to [merge request #431](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/431).

### Changed

-   **Avoid loading section data for customers without a session**

    This is a performance improvement to reduce the number of Ajax requests to the server.  
    We don't expect this will affect many extensions (if any), but just in case please refer to the 1.3.6 upgrade notes for more information.

    For details, please refer to [issue #279](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/279) and the associated merge request.

-   **Restore view model registry param annotation to avoid having to type hint return values**

    This functionality was originally contributed to release 1.1.9 by Thijs de Witt and was accidentally removed in 1.1.18.

    For details, please refer to [merge request #411](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/411).

    Many thanks to Matthijs Perik (Ecomni) for the contribution!

-   **Memoize category tree data**

    This is a performance improvement to reduce server load in case both the mobile and the desktop category menus have the same depth.

    For details, please refer to [merge request #386](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/386).

    Many thanks to Jeroen Boersma (Elgentos) for the contribution!

-   **Fix Exception Return value must be of type string, null returned for payment method title**

    For details, please refer to [issue #339](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/339) and the associated merge request.

    Many thanks to Michiel Gerritsen for the contribution!

-   **Fix input validation message with step nearest allowed values**

    For details, please refer to [issue #340](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/340).

-   **Reload section data if a request fails**

    Previously the frontend would get stuck without section data if the request failed at the wrong moment.  

    For details, please refer to [issue #347](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/347).

    Many thanks to Harsh Tank (Graas.ai) for the contribution!

-   **Use optimized versions of Heroicons**

    Previously some Heroicons contained static colors or stroke width inside the SVG on the path element.

    For details, please refer to [merge request #429](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/429).

-   **Add missing form key validation for customer Ajax login controller**

    For details, please refer to [merge request #430](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/430).

    Many thanks to Talesh Seeparsan for reporting the issue!

-   **Use hyva.trapFocus for modals**

    Previously the Hyvä modal library used a different implementation relying on the `inert` element attribute.  
  This change to the more lightweight implementation improves consistency and resolves issues with some third-party libraries when used inside of modals.

    For details, please refer to [merge request #432](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/432).

-   **Fix: exclude hidden elements from focus targets**

    Previously hidden elements were used as focus targets leading to inconsistent behavior.

    For details, please refer to [merge request #433](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/433).

-   **Fix: do not call focus() on window object when modal is closed**

  Previously, if no valid focus-after-close target was specified when showing a modal dialog, the `focus()` method was called on the window object.

    For details, please refer to [issue #361](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/361).

### Removed

-   Nothing removed

## [1.3.5] - 2023-12-20

[1.3.5]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.4...1.3.5

### Added

-   **Add method hyva.setSessionCookie**

    The method was added because `hyva.setCookie` does not allow setting a cookie with Session duration if a default cookie lifetime is configured.

    For details, please refer to [issue #313](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/313).

-   **Add feature for showing HTML form validation messages**

    Previously all form validation messages were rendered as text. By returning a JS object with a `type` and a `content`
    property from the form validation rule it will render the HTML without escaping it:  
    `{ type: 'html', content '<a href="">click me</a>"' }

    For details, please refer to [merge request #413](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/413).

-   **Allow adding input type or attribute-based form validation rules**

    This change allows hooking into default INPUT types (e.g. `url`) and browser attributes (e.g. `accept` for allowed file extensions) and associate form validation rules that way.

    For details, please refer to [merge request #410](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/410).

-   **Add aria-expanded attribute state to modal trigger**

    Previously it was not possible to specify the element to focus after a modal was closed, if the element was outside of the modal component, or if the modal
    was opened by triggering a custom event.

    When opening a modal by calling the `show()` method, the element to focus after it is closed can be specified as a selector string argument, e.g. `@some-event.window="show('#some-trigger')"`.  
    When opening a modal with an `hyva-modal-show` event, the element to focus after it is closed can be specified as an argument in the event details: `{detail: {name: 'my-dialog', focusAfterHide: '#some-trigger'}}`  
    When rendering the JS to open the modal with the modal view model in PHP, the element selector can be specified as an argument to `getShowJs`: `@click="<?= $modal->getShowJs('#some-trigger') ?>"`

    For details, please refer to [merge request #381](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/381).

### Changed

-   Nothing changed

### Removed

-   Nothing removed


## [1.3.4] - 2023-11-21

[1.3.4]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.3...1.3.4

### Added

-   Nothing added

### Changed

-   **Restore Alpine.js v2 compatibility**

    The `hyva-themes/magento2-theme-module` has been incompatible with themes using Alpine v2 since release 1.2.6.

    For more details, please refer to merge request [#402](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/402).

-   **Fix etc/module.xml sequence tags**

    Previously the module sequence was declared in a way that it did not have any effect.

    For details, please refer to [issue #323](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/323).

-   **Apply cookie_secure setting instead of hardcoding to false**

    Previously the cookie_secure value was hardcoded to false instead of using the value set for PHP.

    For details, please refer to [issue #322](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/322).

### Removed

-   Nothing removed

## [1.3.3] - 2023-11-16

[1.3.3]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.2...1.3.3

### Added

-   **Add method getCustomerEmailsForReviews to ReviewList view model**

    This method is useful when rendering [Gravatars](https://gravatar.com/) for customer reviews.

    For details, please refer to [issue #321](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/321).

-   **Allow custom titles for SVG icons**

    Previously, the icon name was rendered as the title on SVG icons for accessibility purposes.  
    Now a custom title can be specified to replace the default, by passing it as a title in the attributes argument: `['title' => 'Example']`.

    For details, please refer to [issue #315](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/315).

### Changed

-   **Fix: Properly render boolean value SVG attributes**

    Previously, boolean attributes to SVG icons had to be specified as strings, for example `['aria-hidden' => 'true']`.  
    Specifying the value as a boolean `true` or `false` failed. With this change, both string and boolean values work as expected.

    For details, please refer to [merge request #387](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/387).

    Many thanks Stephanie Ehrling (ECOPLAN) for the contribution!

-   **Fix: Do not render SVG icon title if aria-hidden is 'true'**

    Previously the title was only omitted if the aria-hidden value was specified as a boolean `true`.

    For details, please refer to [merge request #388](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/388).

    Many thanks Stephanie Ehrling (ECOPLAN) for the contribution!

-   **Fix: Move SortableItemInterface preference into global scope**

    This change resolves the issue "Error: Cannot instantiate interface Hyva\Theme\Block\SortableItemInterface" when editing CMS content in the admin.

    For details, please refer to [merge request #391](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/391).

### Removed

-   Nothing removed

## [1.3.2] - 2023-09-30

[1.3.2]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.1...1.3.2

### Added

-   Nothing added

### Changed

-   **Build account top menu form individual blocks instead of a hardcoded template**

    This is a much-requested, although backward compatibility breaking, change. It allows items to be added to
    the account top menu using layout XML without requiring a template override.  
    In the past, this has often led to conflicting template overrides from third-party extensions.

    Please refer to [merge request #378](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/378), 
    or see the 1.3.2 upgrade notes for more information.

-   **Allow fields without rules have an `@input="onChange"` callback**

    This is a fix for a regression introduced with release 1.3.0.

    For more information please refer to [issue #314](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/314).

-   **Make section data cache handling more robust**

    This change fixes a reported but unreproducible issue on Adobe Cloud installations.

    For more information please refer to [issue #317](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/317).

### Removed

-   Nothing removed

## [1.3.1] - 2023-09-06

[1.3.1]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.3.0...1.3.1

### Added

-   Nothing added

### Changed

-   **Dispatch messages from cookie during alpine:initialize for Safari on iOS compatibility**

    Previously a theoretical race condition on cached pages could cause messages not to show on Safari on iOS.

    For more information please refer to [issue #309](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/309).

-   **Instantiate default product price renderer block if not present**

    During custom Ajax requests that render a single block without loading the default layout, the price renderer block   
    is not be instantiated. Previously this would cause no price to be rendered during such requests.

    For more information please refer to [issue #240](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/240).

### Removed

-   Nothing removed

## [1.3.0] - 2023-08-31

[1.3.0]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.6...1.3.0

### Added

-   **Added aria-live and aria-state to validation library**

-   **Add title child node and role="img" attribute to SVG icons if missing**

-   **Added ViewModel to render payment titles in order info if available**

    The class `\Hyva\Theme\ViewModel\Sales\PaymentInfo` was added.

-   **Added ViewModel to format radio option price adjustments**

    The class `\Hyva\Theme\ViewModel\Product\RadioPriceRenderer` was added.

### Changed

-   Nothing changed

### Removed

-   Nothing removed

## [1.2.9] - 2023-12-21

[1.2.9]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.8...1.2.9

### Added

-   **Add method hyva.setSessionCookie**

    The method was added because `hyva.setCookie` does not allow setting a cookie with Session duration if a default cookie lifetime is configured.

    For details, please refer to [merge request #420](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/420).

-   **Add feature for showing HTML form validation messages**

    Previously all form validation messages were rendered as text. By returning a JS object with a `type` and a `content`
    property from the form validation rule it will render the HTML without escaping it:  
    `{ type: 'html', content '<a href="">click me</a>"' }

    For details, please refer to [merge request #418](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/418).

-   **Allow adding input type or attribute-based form validation rules**

    This change allows hooking into default INPUT types (e.g. `url`) and browser attributes (e.g. `accept` for allowed file extensions) and associate form validation rules that way.

    For details, please refer to [merge request #419](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/419).

### Changed

-   Nothing changed

### Removed

-   Nothing removed

## [1.2.8] - 2023-11-22

[1.2.8]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.7...1.2.8

### Added

-   Nothing added

### Changed

-   **Restore Alpine.js v2 compatibility**

    The `hyva-themes/magento2-theme-module` has been incompatible with themes using Alpine v2 since release 1.2.6.

    For more details, please refer to merge request [#405](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/405).

-   **Fix etc/module.xml sequence tags**

    Previously the module sequence was declared in a way that it did not have any effect.

    For details, please refer to [issue #323](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/323).

-   **Apply cookie_secure setting instead of hardcoding to false**

    Previously the cookie_secure value was hardcoded to false instead of using the value set for PHP.

    For details, please refer to [issue #322](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/322).

### Removed

-   Nothing removed

## [1.2.7] - 2023-11-17

[1.2.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.6...1.2.7

### Added

-   Nothing added

### Changed

-   **Build account top menu form individual blocks instead of a hardcoded template**

    This change was released for the main branch as part of 1.3.2.

    This is a much-requested, although backward compatibility breaking, change. It allows items to be added to
    the account top menu using layout XML without requiring a template override.  
    In the past, this has often led to conflicting template overrides from third-party extensions.

    Please refer to [merge request #392](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/392),
    or see the 1.2.7 upgrade notes for more information.

-   **Fix: Properly render boolean value SVG attributes**

    Previously, boolean attributes to SVG icons had to be specified as strings, for example `['aria-hidden' => 'true']`.  
    Specifying the value as a boolean `true` or `false` failed. With this change, both string and boolean values work as expected.

    For details, please refer to [merge request #393](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/393).

    Many thanks Stephanie Ehrling (ECOPLAN) for the contribution!

### Removed

-   Nothing removed

## [1.2.6] - 2023-08-28

[1.2.6]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.5...1.2.6

### Added

-   **Add hyva.trapFocus() and hyva.releaseFocus() methods**

    The `trapFocus` method causes keyboard tab navigation to iterate only over focusable elements inside the given root element.  

    Please refer to the [documentation](https://docs.hyva.io/hyva-themes/writing-code/the-window-hyva-object.html#hyvatrapfocuselement-rootelement) for more information.

### Changed

-   **Update Hero icon library from 0.4.2  to 1.0.6**

    This is a backward-compatible upgrade.  

    For more information, please refer to [issue #222](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/222).

    Many thanks to Oleksandr Hasniuk (planeta-web.com.ua) for the contribution!

-   **Add product list item template name to product item cache key in ProductListItem view model**

    Previously the parent block template was used, which made it impossible to use a different list item template for different uses.

    For more information, please refer to [issue #296](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/296).

-   **Set product list item parent block**

    Previously it was not possible to refer to the parent block from within the product list item template.

    For more information, please refer to [issue #294](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/294).

-   **Add a missing quote to the exception thrown in the SVG icon view model**

    Previously the phrase `Unable to find the SVG icon "%1` was used. This now was changed to `Unable to find the SVG icon "%1"`.  
    This is a backward compatibility-breaking change, however, we do not believe it will have a wide impact.  
    Be sure to update your localization CSV files if they contain a translation if they include this phrase.  
    The `i18n/en_US.csv` translation file in `hyva-themes/magento2-default-theme` has also been updated to match this change in release 1.2.6.

-   **Ensure ID attributes inside of SVG icons are unique**

    Previously, rendering an SVG icon with internal IDs multiple times on a page would cause duplicate ID warnings.  
    Now, if an ID is used multiple times within separate SVGs, the ID value is changed to be unique each time the SVG is rendered.  
    This only applies to SVG icons rendered with the SvgIcons view model.

    For more information, please refer to [issue #275](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/275).

    Many thanks to the development team at Fronius for the contribution!

-   **Fix private-content not being loaded on Mobile Safari on iOS**

    This change fixes an annoying safari behavior change for pages cached in the local browser HTTP cache.

    For more information, please refer to [issue #304](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/304).

-   **Use staging product link field for crosssell items on commerce**

    This improves the fix introduced in 1.2.4.  

    For more information, please refer to [merge request #369](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/369).

    Many thanks to Sandra Kotowska from PHPro for the fix!

### Removed

-   Nothing removed

## [1.2.5] - 2023-07-31

[1.2.5]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.4...1.2.5

### Added

-   Nothing added

### Changed

-   **Fix property name reference in modal click handler**

    For more information, please refer to [issue #292](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/292).

-   **Fix Template Processor Plugin Regex to avoid matching non-alpine attributes**

    The previous release 1.2.4 introduced a regression that caused non-alpine attributes to be matched.

    For more information, please refer to [issue #291](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/291).

-   **Patch Alpine 3.12.3 for full mobile Safari 13 compatibility**

    The previous release 1.2.4 upgraded Alpine to 3.12.3. This release makes use of the nullish coalescing operator `??`, which is not supported by mobile Safari 13.3 and earlier (This roughly corresponds to the iPhone 6).
    This release contains a custom build of 3.12.3 that replaces the `??` operator with backward-compatible code.

    For more information, please refer to [issue #293](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/293).

### Removed

-   Nothing removed


## [1.2.4] - 2023-07-21

[1.2.4]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.3...1.2.4

### Added

-   **Magento_GTag compatibility**

    This release now provides compatibility with the Magento_GoogleGtag module.  
    It provided a basic Google Analytics 4 and Google Ads Gtag integration.

-   **Add JavaScript method to access currently active modal dialog**

    The method `hyva.modal.peek()` will now return the currently active modal dialog (or `false` if there is none).

    For more information, please refer to [issue #272](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/272).

-   **Add optional argument to allow skipping the uenc parameter in hyva.postForm**

    Previously the `hyva.postForm` method always automatically added the `unec` parameter to the payload.  
    Now it is possible to skip it by providing the key `unec: false` in the postParams argument.

    For more information, please refer to [merge request #340](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/340).

-   **Add system configuration setting to specify a success message default timeout**

    For more information, please refer to [merge request #343](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/343).

-   **Add viewmodel for escaping anchor tags keeping attributes intact**

    The default Magento `$escaper->escapeHtml` method removes all `<a>` tag attributes except `href`.  
    The new `\Hyva\Theme\ViewModel\Escaper\EscapeHtmlAllowingAnchorAttributes` view model allows escaping anchor tags while keeping attributes like `rel` and `target` intact.  

    For more information, please refer to [issue #284](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/284).

-   **Add methods to get cart shipping total display config to TotalsOutput view model**

    The methods `displayCartShippingExclTax`, `displayCartShippingInclTax`, `displayCartShippingBoth` were added to be able to display the shipping total incl. or excl. tax on the cart page.  

    For more information, please refer to [issue #345](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/345).

### Changed

-   **Upgrade Alpine.js from 3.10.4 to 3.12.3**

  For details on what changed, please refer to the [Alpine.js release notes](https://github.com/alpinejs/alpine/releases).

-   **Fixed: typo in variable in hyva.modal.excludeSelectorsFromFocusTrap**

    For more information, please refer to [merge request #321](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/321).

    Many thanks to Jesse de Boer (Elgentos) for the contribution!

-   **Adjusted workaround for PHP 8.1 core bug in Magento\Tax\Pricing\Render\Adjustment**

    The previously applied fix was subtely different from the one introduced upstream by Magento.
    Now the behavior of the backport in Hyvä behaves identically.  

    For more information, please refer to [merge request #326](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/326).

    Many thanks to Pieter Hoste (Baldwin) for the contribution!

-   **Fixed: model open event listener**
  
    Opening modal by events now works.

    For more information, please refer to [issue #2547](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/247).

    Many thanks to Valentyn Kuchak (Perspective) for the contribution!

-   **Fixed: ESI Block causing whole page to be invalidated in Varnish**

    Previously, changes to categories caused all catalog FPC records to be invalidated, even though only the top menu ESI 
    block should have been needed regenerated.

    For more information, please refer to [issue #256](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/256).

    Many thansk to Matt Walsh for the detailed report!

-   **Fixed: PageBuilder HTML content processing**

    Previously, subsequent HTML content elements would be escaped, thus rendering the HTML tags visibly on the page.  

    For more information, please refer to [issue #267](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/267).

-   **Add . to PageBuilder CSS class name validation rule**

    Previously, classes such as `mx-1.5`, `mb-4.5` and `md:mb-3.75` could not be saved because they contained a period `.` character.  

    For more information, please refer to [issue #277](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/277).

-   **Increase modal click-guard time**

    Previously, under some circumstances, possibly heavy main-thread load, the click opening the modal was registered as a closing click.

    For more information, please refer to [issue #270](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/270).

-   **Fixed: not-visible products in from product relations are shown**

    Products not visible in the catalog were previously included in related, upsell and crosssell product sliders.  
    Now products set to not-visible-individually or only-visible-in-search are excluded, as well as deactivated products.

    For more information, please refer to [merge request #341](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/341).

### Removed

-   Nothing removed.


## [1.2.3] - 2023-03-17

[1.2.3]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.2...1.2.3

### Added

-   Nothing added

### Changed

-   **Avoid dynamic class properties for PHP 8.2 compatibility**

    Previously the class property `\Hyva\Theme\Plugin\FrontController\HyvaHeaderPlugin::$theme` was undeclared,
    and thus treated as a dynamic public property. Now the property is declared with `private` visibility.
    Technically this is a backward compatibility-breaking change, as any child class referring to the parent class property
    will no longer work. However, we believe this scenario to be unlikely, and it is simple to work around by declaring and
    assigning the property in the child class, too.

    For more information, please refer to [merge request #309](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/309).

    Many thanks to Pieter Hoste (Baldwin) for the contribution!

-   **Refactor string variable interpolation deprecated in PHP 8.2**

    In the template files `src/view/frontend/templates/page/js/plugins/intersect.phtml` and `src/view/frontend/templates/page/js/alpinejs.phtml`
    a variable was interpolated into a string using the "${varname}". This syntax has been deprecated in PHP 8.2.  
    Instead, the syntax "{$varname}" is now used.

    For more information, please refer to [merge request #309](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/309) and [#322](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/322).

    Many thanks to Pieter Hoste (Baldwin) and Peter Jaap Blaakmeer (Elgentos) for the contribution!

### Removed

-   Nothing removed


## [1.2.2] - 2023-03-06

[1.2.2]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.1...1.2.2

### Added

-   Nothing added

### Changed

-   **Extract PageBuilder HTML content with ungreedy regex**

    Previously, the regular expression would extract PageBuilder content elements following a HTML content element as part of the HTML content element data.

    For more information, please refer to [issue #258](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/258).

    Many thanks to Aad Mathijssen (iO Digital) for reporting!

### Removed

-   Nothing removed


## [1.2.1] - 2023-01-19

[1.2.1]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.2.0...1.2.1

### Added

-   **Make $viewModels template variable available in Luma again**

    The template variable was removed in Hyvä release 1.2.0, but it turned out it had been used by many people, so it was decided to make it available again.

    For more information please refer to [issue #238](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/238).

-   **Add support for the pattern attribute to advanced form validation**

    For more information please refer to [merge request #290](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/290).

    Many thanks to Aad Mathijssen (Isaac) for the contribution!

-   **Add missing requirement for Magento_QuoteGraphQl**

    A class from the module is used by the theme-module, so it needs to be declared as a composer dependency and in the etc/module.xml file.

    For more information please refer to [issue #244](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/244).

    Many thanks to Thomas Hauschild (E3n) for the contribution!

### Changed

-   **Improve minlength and maxlength translations for advanced form validation**

    Previously there was no distinction between singlular and plural numbers.

    For more information please refer to merge [request #288](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/288).

    Many thanks to Aad Mathijssen (Isaac) for the contribution!

-   **Replace deprecated string interpolation to support PHP 8.2**

    Two instances of a deprecated string interpolation syntax are changed to be PHP 8.2 compatible.

    For more information please refer to [issue #241](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/241).

    Many thanks to Pieter Hoste (Baldwin) for opening the issue and providing helpful information on automated checks!

-   **Make client-side breadcrumbs more compatible with third-party modules**

    Previously an exception was thrown if no path property was set for a category.

    For more information please refer to [issue #242](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/242).

    Many thanks to Dung La (JaJuMa) for the contribution!

### Removed

-   Nothing removed


## [1.2.0] - 2022-12-21

[1.2.0]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.20...1.2.0

### Added

-   **Added Alpine v3 support**

    Alpine v3 was added in addition to Alpine v2. Backward compatibility is preserved.
    Please refer to the upgrade documentation for details.

    For more information please refer to [Merge Request #83](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/119).

-   **Added `ThemeLibrariesConfig` view model**

    This class can be used to determine the version of Alpine that should be loaded for the current theme.

    For more information please refer to [Merge Request #83](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/119)

-   **Added `require-alpine-v3` block class as extension point**

    Extensions may add a child block to `require-alpine-v3` in layout XML using the module name as the child block name.  
    If a theme then loads the module using a Alpine v2 theme, a warning is displayed in the browser console.

    For more information please refer to [Merge Request #83](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/119)

### Changed

-   Nothing changed

### Removed

-   Nothing removed


[1.1.26]: https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/compare/1.1.25...1.1.26

### Added

-   **Added new containers for Customer Custom Attributes**

    Containers were added to the customer_account_create, customer_account_edit, customer_address_form, and layout XML instructions to facilitate rendering custom customer attributes.

    Note: while these changes reference features in Adobe Commerce, no Commerce code is depended upon.

    For more information, please refer to [issue #812](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/812).

### Changed

-   **Fix: Mobile Safari iOS double click required to start swatch selection**

    For more information, please refer to [merge request #941](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/941).

-   **Allow adding additional links to header customer menu**

    Previously, it was not possible to add additional links to the customer-menu.phtml template without overriding the template.

    For more information please refer to the 1.1.26 upgrade notes or [issue #730](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/730).

-   **Render customer.account.dashboard.info.blocks container on customer dashboard**

    Additional blocks can now be rendered by assigning them as children of the  `customer.account.dashboard.info.blocks` container.

    For more information, please refer to [issue #812](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/812).

-   **Render SVG icons on customer dashboard with view model**

    Previously the SVG icons were declared as inline markup in the template without using the SVG icons view model.

    For more information, please refer to [issue #812](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/812).

-   **Update version constraint for hyva-themes/magento2-reset-theme to 1.1.5**

    The updated reset theme contains the resets for the Adobe Sensei related modules.
  
    For more information please refer to [merge request #951](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/951).

### Removed

-   Nothing removed

## [1.1.26] - 2023-11-17

[1.1.26]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.25...1.1.26

### Added

-   Nothing added

### Changed

-   **Build account top menu form individual blocks instead of a hardcoded template**

    This is a much-requested, although backward compatibility breaking, change. It allows items to be added to
    the account top menu using layout XML without requiring a template override.  
    In the past, this has often led to conflicting template overrides from third-party extensions.

    Please refer to [merge request #378](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/378),
    or see the 1.1.26 upgrade notes for more information.

    This change was released as part of 1.3.2 in the main development line.

-   **Fix: Properly render boolean value SVG attributes**

    Previously, boolean attributes to SVG icons had to be specified as strings, for example `['aria-hidden' => 'true']`.  
    Specifying the value as a boolean `true` or `false` failed. With this change, both string and boolean values work as expected.

    For details, please refer to [merge request #387](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/387).

    This change was released as part of 1.3.3 in the main development line.

### Removed

-   Nothing removed

## [1.1.25] - 2023-07-31

[1.1.25]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.24...1.1.25

### Added

-   Nothing added

### Changed

-   **Fix Template Processor Plugin Regex to avoid matching non-alpine attributes**

    The previous release 1.2.4 introduced a regression that caused non-alpine attributes to be matched.  

    For more information, please refer to [issue #291](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/291).

### Removed

-   Nothing removed

## [1.1.24] - 2023-07-21

[1.1.24]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.23...1.1.24

### Added

-   **Add JavaScript method to access currently active modal dialog**

    The method `hyva.modal.peek()` will now return the currently active modal dialog (or `false` if there is none).

    For more information, please refer to [issue #272](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/272).

### Changed

-   **Fixed: ESI Block causing whole page to be invalidated in Varnish**

    Previously, changes to categories caused all catalog FPC records to be invalidated, even though only the top menu ESI
    block should have been needed regenerated.

    For more information, please refer to [issue #256](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/256).

    Many thansk to Matt Walsh for the detailed report!

-   **Fixed: typo in variable in hyva.modal.excludeSelectorsFromFocusTrap**

    For more information, please refer to [merge request #321](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/321).

    Many thanks to Jesse de Boer (Elgentos) for the contribution!

-   **Fixed: PageBuilder HTML content processing**

    Previously, subsequent HTML content elements would be escaped, thus rendering the HTML tags visibly on the page.

    For more information, please refer to [issue #267](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/267).

-   **Add . to PageBuilder CSS class name validation rule**

    Previously, classes such as `mx-1.5`, `mb-4.5` and `md:mb-3.75` could not be saved because they contained a period `.` character.

    For more information, please refer to [issue #277](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/277).

### Removed

-   Nothing removed


## [1.1.23] - 2023-03-17

[1.1.23]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.22...1.1.23

### Added

-   Nothing added

### Changed

-   **Apply changes required for PHP 8.2 compatibility**

    Previously the class property `\Hyva\Theme\Plugin\FrontController\HyvaHeaderPlugin::$theme` was undeclared,
    and thus treated as a dynamic public property. Now the property is declared with `private` visibility.
    Technically this is a backward compatibility-breaking change, as any child class referring to the parent class property
    will no longer work. However, we believe this scenario to be unlikely, and it is simple to work around by declaring and
    assigning the property in the child class, too.

    For more information, please refer to [merge request #323](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/323).

    Many thanks to Pieter Hoste (Baldwin) for the contribution!

### Removed

-   Nothing removed

## [1.1.22] - 2023-03-06

[1.1.22]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.21...1.1.22

### Added

-   Nothing added

### Changed

-   **Extract PageBuilder HTML content with ungreedy regex**

    Previously, the regular expression would extract PageBuilder content elements following a HTML content element as part of the HTML content element data.

    For more information, please refer to [issue #258](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/258).

    Many thanks to Aad Mathijssen (iO Digital) for reporting!

### Removed

-   Nothing removed

## [1.1.21] - 2023-01-19

[1.1.21]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.20...1.1.21

### Added

-   **Make $viewModels template variable available in Luma again**

    The template variable was removed in Hyvä release 1.1.20, but it turned out it had been used by many people, so it was decided to make it available again.

    For more information please refer to [issue #238](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/238).

-   **Add support for the pattern attribute to advanced form validation**

    For more information please refer to [merge request #290](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/290).

    Many thanks to Aad Mathijssen (Isaac) for the contribution!

-   **Add missing requirement for Magento_QuoteGraphQl**

    A class from the module is used by the theme-module, so it needs to be declared as a composer dependency and in the etc/module.xml file.

    For more information please refer to [issue #244](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/244).

    Many thanks to Thomas Hauschild (E3n) for the contribution!

### Changed

-   **Improve minlength and maxlength translations for advanced form validation**

    Previously there was no distinction between singlular and plural numbers.

    For more information please refer to merge [request #288](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/288).

    Many thanks to Aad Mathijssen (Isaac) for the contribution!

-   **Replace deprecated string interpolation to support PHP 8.2**

    Two instances of a deprecated string interpolation syntax are changed to be PHP 8.2 compatible.

    For more information please refer to [issue #241](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/241).

    Many thanks to Pieter Hoste (Baldwin) for opening the issue and providing helpful information on automated checks!

-   **Make client-side breadcrumbs more compatible with third-party modules**

    Previously an exception was thrown if no path property was set for a category.

    For more information please refer to [issue #242](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/242).

    Many thanks to Dung La (JaJuMa) for the contribution!

### Removed

-   Nothing removed

## [1.1.20] - 2022-12-21

[1.1.20]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.19...1.1.20

### Added

-   **Add user interaction init JavaScript event**
  
    The `init-external-scripts` event is intended to be used for deferring the loading of external scripts until a user has interacted with the page.

    For more information please refer to [issue #226](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/226)
    The new event is documented in the documentation under [Loading External JavaScript](https://docs.hyva.io/hyva-themes/writing-code/patterns/loading-external-javascript.html#example-deferring-scripts-until-any-user-interaction-with-init-external-scripts).

    Many thanks to John Huges (Youwe) for the contribution!

-   **Add `Hyva\Theme\Block\Catalog\ProductBreadcrumbs` block for client side crumbs rendering on PDP pages**

    This new block uses the new system configuration setting `hyva_theme_catalog/hyva_breadcrumbs/client_side_enable/`
    to switch to a template rendering the breadcrumbs on product detail pages with JavaScript.

    For more information please refer to the [default theme issue #424](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/424).

    Many thanks to Dung La (JaJuMa) for the contribution!

-   **Make list item relation type available inside `item.phtml`**

    The slider type `crosssell`, `related` or `upsell` is now available inside the product list item template via `$block->getData('item_relation_type')`

    For more information, please refer to [issue #216](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/216).

-   **Add methods required to show product prices incl. and excl. tax**

    The following new methods are available on `Hyva\Theme\ViewModel\CustomOption`:

    * `getDateDropdownsHtml(int $optionId, array $additionalSelectAttributes = [])`
    * `getTimeDropdownsHtml(int $optionId, array $additionalSelectAttributes = [])`
    * `is24hTimeFormat()`

    The following new methods are available on `Hyva\Theme\ViewModel\ProductPrice`:

    * `getPriceValueInclTax(string $priceType, Product $product)`
    * `getPriceValueExclTax(string $priceType, Product $product)`
    * `displayPriceInclAndExclTax()`
    * `getCustomOptionPriceInclTax($option, string $priceType, Product $product)`
    * `getCustomOptionPriceExclTax($option, string $priceType, Product $product)`

-   **Add method to provide top countries system config value**

    The following new methods are available on `Hyva\Theme\ViewModel\Directory`:

    * `getTopCountryCodes()`

    This method is used in the default theme merge request [Fix foreground countries in country_id select]/https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/633).  

    For more information please refer to merge [request #281](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/281).

    Many thanks to Mark van der Sanden (Ecomni) for the contribution!

-   **Workaround Magento core type error with PHP 8.1 in `Magento\Tax\Pricing\Render\Adjustment`**

    The bug occurred when product prices were displayed including and excluding taxes for grouped products.  
    It is expected to be fixed in Magento 2.4.6 (unreleased at the time of writing).  

    This change allows using the feature on any Magento version.

    For more information please refer to [issue #232](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/232)

-   **Disable move JS to footer on cart page**

    This resolves an error where the PHP-Cart page refreshes endlessly after changing an item quantity.

    For more information, please refer to [issue #223](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/223)

### Changed

-   **Use default required field translation for form validation**

    This is a backward incompatible change.  

    Instead of using "This field is required.", use "This is a required field." as it is also in Luma.

    For more information please refer to [merge request 257](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/257).

    The change was not required but the impact of the change is expected to be very low, as the advanced form validation is still a new feature and existing translations from a default Magento language pack will be used. 

    Many thanks to Aad Mathijssen (Isaac) for the contribution!

-   **Do not automatically apply password validation to all password type inputs**

    This is a backward incompatible change.

    Previously, if advanced form validation was enabled for a form, password validation was applied automatically to any password field.  
    Now `data-validate='{"password": true}'` needs to be used.

    The change was required because otherwise it was impossible to use password type inputs without password a validation rule.

-   **Collect Hyvä system config fields into "Hyvä Themes" section**

    The configuration paths for the settings was not changed, and the fields can still also be found in the previous location in the system configuration.  
    Collecting them in one Tab makes it easier to find all Hyvä specific settings and avoids confusion why some settings might not apply to Luma store views.

    For more information please refer to [issue #230](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/230).

-   **Check if `product_list_item` block exists before using it**

    This fixes an incompatibility with indexing when using Algolia.

    For more information please refer to [issue #227](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/227).

    Many thanks to Rico Neitzel (riconeitzel.de) for the excellent issue!

-   **Use product list item image custom attributes in block_html cache key**

    Since release 1.1.20 and 1.2.0 some product images on product lists are eagerly loaded.  
    This requires adding the custom attribute values to the item cache key.  

    For more information please refer to [issue #229](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/229).

### Removed

-   Nothing


## [1.1.19] - 2022-10-22

[1.1.19]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.18...1.1.19

### Added

-   Nothing added

### Changed

-   **Guard against array to string conversion error if multipe attribute values can be specified**

    This is relevant for extensions like elasticsuite.

    For more information, please refer to [issue #113](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/213).

### Removed

-   Nothing removed


## [1.1.18] - 2022-10-15

[1.1.18]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.17...1.1.18


### Added

-   **Add class constants for ReCaptcha form field identifiers**

    For more information, please refer to [merge request #219](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/219).

    Many thanks to Kiel Pykett (Youwe - formerly Fisheye) for the contribution!

-   **Allow opening modal dialogs via event**

    Modal dialogs can now be opened by dispatching a JS event `$dispatch('hyva-modal-show', {dialog: modalName})`

    For more information, please refer to [merge request #241](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/242).

-   **Add CurrentCategory::fetch() method to return either the current category if present or null**

    The existing method `get()` throws an exception if the current category is not set, forcing a check with `exists()` to be used.
    The new `fetch()` method makes it more convenient to do things like `if ($cat = $currentCategory->fetch():`.

    For more information, please refer to [issue #194](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/194).

-   **Add useAnchorAttribute property to ProductList view model.**

    If set to `true` via `includeChildCategoryProducts()` and a single category ID filter of a anchor category is set, the
    return value will include all products assigned to child categories.

    For more information please refer to [merge request #237](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/237).

    Many thanks to Daniel Galla (IMI) for the contribution!

-   **Add backward compatibility classes for view models introduced in Magento 2.4.5**

    In versions of Magento before the new view models exists, the new Hyvä classes provide the required functionality.  
    In versions of Magento where the new view models exist (>= 2.4.5), the Magento core functionality will be used.

    The new view models added are:
    * `Hyva\Theme\ViewModel\Customer\Address\RegionProvider`
    * `Hyva\Theme\ViewModel\Customer\CreateAccountButton`
    * `Hyva\Theme\ViewModel\Customer\ForgotPasswordButton`
    * `Hyva\Theme\ViewModel\Customer\LoginButton`

    For more information please refer to [merge request #211](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/211).

-   **Add new template variable `$localeFormatter` for backward compatibility with Magento versions < 2.4.5**

    Since Magento 2.4.5 a new template variable `$localeFormatter` is available. Hyvä provides a version of the class for
    older versions of Magento for backward compatibility.

    For more information please refer to [merge request #244](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/244).

### Changed

-   **Fix GraphQL schema incompatibility with 2.4.5 and GraphQL Cart**

    The issue occurred only in combination with the Hyvä GraphQL cart. Because of a backward
    incompatible change in the GraphQL schema in Magento 2.4.5 visiting the cart page only displayed the error
    `Field "errors" of type "[CartItemError]" must have a sub selection.`, which is fixed now.

    For more information, please refer to [issue #204](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/204).

    Many thanks to Wilfried Wolf (sandstein.de) for the contribution!

-   **Automatically select text in first input field failing validation**

    This is an improvement to the advanced form validation library.

    For more information, please refer to [issue #207](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/207).

-   **Improve canonical URL for review pagination**

    his is a great improvement for merchants who use customer reviews a lot.

    For more information, please refer to [issue #201](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/201).

    Many thanks to Dung La (JaJuMa) for the contribution!  

-   **Fix modal feature initiallyVisible**

    When `$modal->initiallyVisible()` is called on a modal view model instance, this now causes the modal to correctly be
    visible when the page loads.

    For more information, please refer to [merge request #241](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/242).

-   **Avoid type error if no page layout is set on a category**

    On category pages, when the layout is not set, `getPageLayout()` will return `null`, which is incompatible with the return type string in strict mode.

    For more information, please refer to [merge request #238](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/238).

    Many thanks to Paul Savlovschi (Novicell) for the contribution!

-   **Fix ESI cache key generation for the menu block on product and category pages** 

    This is an important performance improvement.

    For more information, please refer to [issue #206](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/206).

    Many thanks to Lukas Jankauskas (Novicell) for the contribution!

### Removed

-   Nothing removed

## [1.1.17] - 2022-08-16

[1.1.17]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.16...1.1.17

### Added

-   **Add Hyva\Theme\ViewModel\Media view model**

    This class provides a method `getMediaUrl()` which returns the base URL to the media assets for the active store view.

    Many thanks to Kiel Pykett (Fisheye) for the contribution!

-   **Add .gitlab-ci file**

    Some tests and checks are now automatically executed in GitLab pipelines for new merge requests.  
    Currently some do not have to succeed (for example the code style check), but this will change at some point in the future. 

-   **Add JS string formatting function `hyva.str()`**

    The function is very similar to the already existing function `hyva.strf`, the only difference being that first positional argument to be replaced is `%1` instead of `%0`.  
    This alternative function was introduced because it matches the replacement behavior of the Magento PHP `__()` function and thus allows for better reuse of existing translation phrases.

    For more information please refer to [merge request #225](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/225).

-   **Add `hyva.getUenc()` method and form submit event listener to document to replace the uenc url placeholder**

    In 1.1.17, the add-to-cart form of product list items uses a placeholder in the form action for the uenc value.  
    The uenc value is used to hold an encoded version of the current URL, so the visitor can be redirected back after  
    adding a product to the cart. This is now done client side with JS to avoid a block caching issue.

    A new method `hyva.getUenc` was added to provide the properly encoded window location.

    For more information please refer to [issue #199](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/199).

### Changed

-   **Bugfix: Register the current product on the product review list page**

    For more information please refer to [issue #183](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/183).

-   **Bugfix: Avoid iOS 13 incompatible use of JS nullish assignment operator**

    For more information please refer to [merge request #221](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/221).

    Many thanks to Wahid Nory (Elgentos) for the contribution!

-   **Bugfix: Error during installation with Sample Data with Hyvä**

    The error happened when `bin/magento setup:install` was executed while the Hyvä and the Sample Data composer packages where present:
    `Base table or view not found: 1146 Table 'db.catalog_category_product_index_store1_tmp' doesn't exist,`  

    For more information please refer to [issue #186](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/186).

-   **Bugfix: Block PageBuilder previews in adminhtml**

    This fixes a bug introduced in 1.1.16 where the following error was shown as the preview:  
    `Error filtering template: Invalid block type: Magento\Catalog\Block\ShortcutButtons\InCatalog`.

    For more information please refer to [issue #190](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/190).

-   **Bugfix: Removed cached data from localstorage after logout**

    Previously if a logged in customer entered a new address using the Luma checkout, this address would still be stored in the browser local storage after logout.

    For more information please refer to [issue #192](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/192).

    Many thanks to Zach Nanninga (DEG Digital) for the detailed problem report and the suggested solution!

### Removed

-   Nothing removed

## [1.1.16] - 2022-06-16

[1.1.16]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.15...1.1.16

### Added

-   Nothing added

### Changed

-   **Fix Magento installation while theme-module is installed**

    This is a bugfix for the automatic `app/etc/hyva-themes.json` generation feature introduced in 1.1.15.  
    Now the file is not generated by `bin/magento setup:install`, only by `setup:upgrade`, `module:enable` and `module:disable`.  
    On the upside, installation does complete successfully.

    For more information please refer to [Issue #181](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/181).

### Removed

-   Nothing removed


## [1.1.15] - 2022-06-13

[1.1.15]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.14...1.1.15

### Added

-   **Automatically change the theme type for virtual themes to "physical" if it is found in the filesystem**

    This is a workaround for a core bug.  
    More details can be found in the [Issue #175](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/175).

-   **Automatically update app/etc/hyva-themes.json when a module is enabled/disabled**

    This happens when running `bin/magento module:enable` (or `disable`) and also when new modules are enabled while running `bin/magento setup:upgrade`.
    Now - in general - no more manual steps are required after installing a compatibility module.

    More details can be found in the [Merge Request #210](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/210).

-   **Support for reCaptcha v2 "I'm not a robot" and v2 invisible**

    To support this, the ReCaptcha view model has received some new methods.  
    This now provides feature parity with Luma. The implementation was also improved to make it easier to implement custom captcha integrations.

    More details can be found in the [Merge Request #122](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/202) and in the [default theme Merge Request #340](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/340).

    Many thanks to Amanda Bradley (Youwe - formerly Fisheye) for the contribution!

-   **Add feature to configure product list item processor view models in layout XML**

    This is used to modify the product list item cache key depending on the selected swatch attributes.
    Other uses like adding custom block data are possible, too.
    To use it, add a view model to the `additional_item_renderer_processors` block argument:
    ```xml
    <referenceBlock name="product_list_item">
        <arguments>
            <argument name="additional_item_renderer_processors" xsi:type="array">
                <item name="my_processor_name" xsi:type="object">My\Module\ViewModel\ClassName</item>
            </argument>
        </arguments>
    </referenceBlock>
    ```
    The view model then can implement a method that will be called for each item before it is rendered    
    `public function beforeListItemToHtml(AbstractBlock $itemRendererBlock, Product $product): void`

    More details can be found in the [Merge Request #211](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/211).

### Changed

-   **The ProductPage::format method now accepts a optional `$includeContainer` boolean parameter**

    This makes the method consistent with `Hyva\Theme\ViewModel\ProductPrice::format`.

    Many thanks to Simon Sprankel (CustomGento) for the contribution!

-   **Fixed "compact" mode static content deploy**

    More details can be found in the [Merge Request #122](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/202)

    Many thanks for Jeroen Boersma (Elgentos) for the contribution!

-   **Improved advanced JS form validation**

    There were a number of cases the form validation library that was introduced in the previous release did not handle, like hidden fields, checkboxes and grouped fields.
    The library now is a lot more mature.

-   **Fix PageBuilder attribute rendering in compare list**

    Product attributes edited with PageBuilder (like description) were rendered as escaped HTML. This is now corrected.

    More details can be found in the [Issue #174](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/174).

-   **Register the Hyva_Theme module to be included in the tailwind purge config**

    Now the path to the theme module no longer needs to be manually specified in a modules `tailwind.config.js` purge path.

    More information can be found in [Issue #170](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/170).

-   **The -f flag is no longer required when updating the hyva-themes.json file**

    Now running the command `bin/magento hyva:config:generate` will overwrite an existing config file even without the `-f` or `--force` flag.
    The flag still is allowed for backward compatibility, but it has no effect.

### Removed

-   Nothing

## [1.1.14] - 2022-04-29

[1.1.14]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.13...1.1.14

### Added

-   **Add JavaScript form validation library**

    This much requested feature adds an Alpine.js component for advanced form validation. 
    It contains only a few validation rules out of the box, so it is lightweight, but it is simple to add custom rules.

    Many thanks to Michal Gałężewski (macopedia) for the contribution!

    More information on [how to use the form validation library](https://docs.hyva.io/hyva-themes/writing-code/form-validation/javascript-form-validation.html) can be found in the documentation.

-   **Add CLI command to generate app/etc/hyva-themes.json**

    The new configuration file `app/etc/hyva-themes.json` contains a list of modules with tailwind config or tailwind css that should be merged when running `npm run build-prod` or one of the other build commands.  
    This feature allows compatibility modules to be register their templates and layout files in the Tailwind purge configuration, without requiring users to adjust a theme include path manually.

    The command to generate the file is `bin/magento hyva:config:generate`.
    More information can be found in [merge request #180](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/180).  
    Documentation will be published shortly, too.

-   **Add view model to determine developer deploy mode**

    This new view model allows conditional logic to be written in templates based on if developer mode is enabled or not.
    `<?php if ($viewModels->require(\Hyva\Theme\ViewModel\DeployMode::class)->isDeveloperMode()): ?>`

-   **Add view model to fetch the checkout configuration**

    This new view model allows building checkout related functionality in server side templates.
    `$viewModels->require(\Hyva\Theme\ViewModel\Cart\CheckoutConfig::class)->getSerializedCheckoutConfig()`

-   **Add JS helper hyva.replaceDomElement for Ajax page updates**

    The function takes a html string as uses it to replace a section of the current page specified by a `targetSelector`.
    This is useful for injecting Ajax responses containing rendered HTML into the current page.

### Changed

-   **Change modal backdrop default z-index to 50 to work with hyva-ui menus**

    Previously the hyva-ui menus where rendered above the modal overlay backdrop. This change now properly renders the overlay above the top menu.

-   **Improve x-intersect Alpine.js v2 plugin**

    The upstream Apline.js plugin received some improvements with regard to reliability and a new `margin` option.  
    These changes are now included in the Alpine.js v2 backport bundled with Hyvä.

    For more information see [merge request #197](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/197).


### Removed

-   Nothing

## [1.1.13] - 2022-04-12

[1.1.13]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.12...1.1.13

### Added

-   **Add Confirmation Modal Dialog**

    This is an extension of the modal dialogs. 

    More details can be found in [merge request #178](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/178)

-   **Add method displayCartTaxWithGrandTotal to get tax config to cart totals view model**

    More details can be found in [issue #156](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/156)

-   **Show unavailable shipping methods with error code on estimate shipping**

    This replicates the behavior on Luma more closely.

    More details can be found in the default theme [issue #292](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/292)

-   **Add method to render description excerpt for any product**

    Previously the method was only available for the current product on a PDP. The new method accepts any product instance.

    More details can be found in [issue #159](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/159)

### Changed

-   **Fix incompatibility with type changes introduced in Magento Core**

    Fixed an inconsistency where types that were not present in the original method are introduced by this [PR](https://github.com/magento/magento2-page-builder/pull/528) from Magento.

    More details can be found in [merge request #179](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/179)

    Big thanks to Mohamed Kaid (mokadev) for the contribution!
 
-   **Remove argument types for compatibility with TaxJar**

    The TaxJar module does not follow the same typing as core Magento. By relaxing the type constraints this change allows the code to work with the TaxJar module.

    More details can be found in [issue #146](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/146)

-   **Fix form_key race condition on slow internet connections**

    On slow internet connections there was an issue where when the page is submitted before everything has loaded, then it returned “Invalid Form Key. Please refresh the page.”.  

    More details can be found in [issue #140](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/140)

    Big thanks to Luke Collymore (Develo Design) for finding the bug and providing the solution!

-   **Use full locale to determine currency format**

    So far only the language was used to determine how to format the currency, but in some cases that is not enough, for example `de_CH` (Switzerland German) vs `de_DE` (Germany German). 

    More details can be found in the default theme [issue #345](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/345)
  
-   **Product Sliders: allow filtering by category and sorting by position**

    This scenario is treated by Magento as a special case, so it needs to be handled as such in the slider container, too.

    More details can be found in the default theme [issue #354](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/354)

-   **Centralize product list item block rendering**

    Previously the logic to render a product list item was repeated in several templates. This requried updating multiple
    files with the same change, and caused inconsistencies in regards to caching.

    More details can be found in [issue #154](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/154)

-   **Fix product list price for wrong group ID**
  
    This fix is related to the previous item. Previously tax setting depending on the customer group ID where cached
    using the wrong cache ID, so the price for the customer group that happened to visit a list page first got shown
    to every group.

    More details can be found in [issue #155](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/155)

-   **PageBuilder: fix unable to findSVG icon "X" in admin preview**

    More details can be found in [issue #157](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/157)

    Many thanks to Oli Jaufmann and Nguyen Miha (both from JaJuMa) for the contribution!

-   **Configurable Product cart image not using Product Image Itself as per admin settings**

    Previously the setting *Stores -> Configuration -> Sales -> Checkout -> Shopping Cart -> Configurable Product Image -> Product Image Itself* had no effect.  
    This was originally due to the Magneto GraphQL API not providing the parent product image. This data has been added to the GraphQL API in release 2.4.3, so now Hyvä supports showing the configurable product image, too if newer Magento versions.

    More details can be found in the default theme [issue #326](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/326)

    Big thank you to Lucas van Staden (ProxyBlue) for the contribution! 

-   **Allow manipulating modal event subscriber functions**

    More information can be found in the [issue #160](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/160)

### Removed

-   **Remove Magento_SendFriend dependency, so it can be removed if not needed**

    Without this change static-content:deploy failed if Magento_SendFriend was removed.

    More details can be found in the default theme [merge request #287](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/287)

    Many thanks to Peter Jaap Blaakmeer (Elgentos) for the contribution!

## [1.1.12] - 2022-02-07

[1.1.12]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.11...1.1.12

### Added

-   Nothing

### Changed

-   **Bugfix: error after removing last product from cart**

    After deleting the last product from the shopping cart, a red warning message "Internal server error" was shown.
    This bug was reported as fixed in the previous release, but in fact was not fixed in all cases.

    More information can be found in [issue #129](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/129)

-   **Bugfix: include attributes argument in SvgIcons cache key**

    Previously, when the `$arguments` parameter value was changed, the previously rendered SVG was returned.

    More information can be found in [Merge Request #172](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/172)

### Removed

-   Nothing

## [1.1.11] - 2022-01-28

[1.1.11]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.10...1.1.11

### Added

-   Nothing

### Changed

-   **Bugfix: error after removing last product from cart**

    After deleting the last product from the shopping cart, a red warning message "Internal server error" was shown.

    More information can be found in [issue #129](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/129) 

-   **Allow non-Hyvä SVG icon sets to be used with SvgIcons view model**

    Previously the entire SvgIcons class needed to be overridden because of a hardcoded value. Now the value can be set
    in di.xml through virtual types.

    Many thanks to Timon de Groot (Mooore) for the contribution!

    More information can be found in [Merge Request #147](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/147)

-   **Update HeroIcons method annotations**

    The `@method` annotations on the interface no longer matched the backing implementation in `SvgIcons::renderHtml`.

    More information can be found in [issue #128](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/128)

### Removed

-   Nothing


## [1.1.10] - 2022-01-14

[1.1.10]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.9...1.1.10

### Added

-   **Alpine.js x-intersect plugin**

    This is a 'forward-compatible' backport of x-intersect from Alpine V3. It will help make the transition to Alpine v3
    smoother. More info about the Alpine.js intersect plugin can be found at https://alpinejs.dev/plugins/intersect

-   **Allow excluding elements from the focus trapping in modals**

    When a modal with a backdrop is shown, the website elements outside the modal dialog can no longer be focused with Tab
    / Shift-Tab. Setting everything to inert has side effects for some use cases. Cookie consent is one of them. If you
    manage to open a modal any consent banner can't be used anymore even if it is in the foreground.

    To exclude elements from focus trapping, use the `excludeSelectorsFromFocusTrapping` method with selectors. For
    example: `$modalViewModel->createModal()->excludeSelectorsFromFocusTrapping('#cookie-consent', '[x-no-trap]')`

### Changed

-   **Updated Apline from 2.7.0 to 2.8.2**

    This change is related to the new Alpine x-intersect plugin backport and will help the future upgrade to Alpine v3
    will go smoother.

-   **Fix PageBuilder breaking Alpine HTML element attributes.**

    More information can be found in [issue #114](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/114)

-   **Allow modal renderer blocks to be mutated**

    This change allows setting data on the block to renders a modal template,  
    for example `$modal->getContentRenderer()->assign('foo', $foo)`.

    More information can be cound in the [merge request #157](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/157)

-   **Bugfix: If set, use logo_file block argument to render logo like in Luma**

    This allows setting the logo in layout XML as documented in the [devdocs](https://devdocs.magento.com/guides/v2.4/frontend-dev-guide/themes/theme-create.html#theme_logo).

    More information can be found in the [hyva-themes/magento2-default-theme issue #309](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/309).

-   **Bugfix: fix tax rate labels in GraphQL response to make cart totals consistent**

    This is a bug fix for an inconsistency in the Magento core behavior.

    More information can be found in [issue #120](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/120). 

-   **Allow exclamation mark in PageBuilder CSS classes**

    More information can be found in [issue #121](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/121)

-   **Bugfix: fix fetchPrivateContent failure when using BrowserSync proxy**

    More information can be found in [issue #122](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/122)

### Removed

-   Nothing


## [1.1.9] - 2021-11-29

[1.1.9]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.8...1.1.9

### Added

-   **Add View Model to fetch lists of products from templates**

    The new view model `Hyva\Theme\ViewModel\ProductList` can be used to fetch any type of product list inside a template,
    including related, upsell and crosssell products.
    It is used in hyva-themes/magento2-default-theme when rendering product sliders.

    More information an be found in [issue #84](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/84)

-   **Give access to a product image instance via the ProductPage view model**

    The new view model method `getImage` can be used to retrieve a product image instance without relying on the core
    abstract product block class. It is used for rendering product sliders.

-   **Merge PageBuilder compatibility from compat module into theme-module**

    Previously PageBuilder support required using a compatibility module. Now that PageBuilder is included with
    Magento Open Source, it makes sense to support it out-of-the-box in Hyvä Themes.
    The PageBuilder compatibility module still is maintained for backward compatibility.

    More information an be found in [issue #68](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/68)

    Many thanks to John Hughes (Fisheye) for the contribution!

-   **Allow setting additional HTML attributes on SVG icons**

    In Hyva a lot of additional attributes are used on element. For example, the Alpine.js `:class` binding is used is
    very often, but can't be set on an SVG icon at this moment.
    With this change an optional `array $attributes` argument is added to the method signature.

    More information an be found in [merge request #123](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/123)

    Many thanks to Arjen Miedema (Elgentos) for the contribution!

-   **Add ViewModel to provide access to product stock information**

    To render the appropriate product qty form some stock item information is required, for example the minimum order 
    quantity, or if decimal quantities are allowed or not.

    The new view model is used in the theme when rendering the product add-to-cart form.

    More information can be found in the [merge request #130](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/130)

-   **Add method to fetch catalog/seo/product_use_categories system config value**

    This change to the ProductPage view model is required to fix a product listing page caching issue in
    [hyva-themes/magento-default-theme](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/260).

    More information can be found in the [merge request #134](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/134)

-   **Add method to retrieve a blocks cache tags from a view model**

    This new method on the BlockCache view model is helpful because Hyvä often uses generic template blocks instead of 
    specific block classes. For this to work well with the block_html cache group, the cache_tags property has to be set
    within templates.

    More information can be found in the [merge request #135](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/135)

-   **Preserve local only section data for Luma checkout compatibility**

    More information an be found in [issue #99](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/99)

-   **Add ViewModelRegistry return type hint based on class argument**

    This change allows using `$viewModels->require($className)` without a PHPDoc type hint for the return value using
    PHPStorms new generics annotation.

    More information can be found in the [merge request #140](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/140)

    Many thanks to Thijs de Witt (Trinos) for the contribution!

### Changed

-   **Bugfix: Improve LogoPathResolver so it works with Magento 2.4.3 and newer**
  
    The LogoPathResolver also continues to work with Magento versions 2.4.0 - 2.4.2.

    More information an be found in [issue #82](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/82)

-   **Bugfix: Allow using multiple slider instances with the same template on one page**

    Previously the generated block name was determined by the slider template. Now `uniqid` is used to 
    generate the block names.

    More information an be found in [issue #78](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/78)

-   **Allow items for sliders rendered with PHP to be collections**

    Previously the items had to be an array, now they can be any iterable.

-   **Improve Tailwind CSS class name validation regex for PageBuilder**

    Now `/`, `(`, `)`, `%`, `,` and digits are also allowed enabling classes such as `w-1/2`,  `grid-[repeat(3,33%)]`

-   **Bugfix: fix converting camelCase to kebab-case for SVG icons with digits** 

    Previously: `menuAlt2 -> menu-alt2`
    Now (fixed): `menuAlt2 -> menu-alt-2`

    More information an be found in [issue #87](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/87)

    Many thanks to Thijs de Witt (Trinos) for the contribution!

-   **Bugfix: resolve ProductPrice being cached if multiple products use ProductPrice**

    More information an be found in [issue #88](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/88)

    Many thanks to Wahid Nory (Elgentos) for the contribution!

-   **Bugfix: use GraphQL variables instead ot JS string templates for all queries and mutations**

    This resolves a number of bugs related to escaping and serialization of query parameters, and also allows
    editing the queries with the GraphQL query editor as described in [the docs](https://docs.hyva.io/hyva-themes/writing-code/customizing-graphql.html).

    More information can be found in the [merge request #127](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/127)
    and the [related default theme MR](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/301).

-   **Bugfix: Fix constructor integrity check for preference in Magento > 2.4.1**

    The error `Extra parameters passed to parent construct` occurred when running `setup:di:compile` on Magento 2.4.2 or
    newer.

    More information an be found in [issue #85](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/85)

-   **Allow product-compare system.xml settings to be set on store scope**

    These compare product system config settings are not part of stock Magento.

    More information can be found in the [merge request #131](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/131)

    Many thanks to Timon de Groot (Mooore) for the contribution!

-   **Bugfix: Do not render double slash when using SVG icons with no svg iconset**

    More information can be found in [issue #93](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/93)

-   **Remove amount of cache tags for large top menus**

    With Varnish, the top menu is requested using ESI. In that case the cache tags for each category in the menu where
    included in a HTTP response header, which could lead to the header size limit being exceeded.  
    This change replaces the top menu cache tags with a single `hyva_nav`  cache tag if more than 200 cache tags
    would be included in the response.

    More information can be found in [issue #63](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/63)

-   **Bugfix: Do not assume very block inherits from AbstractBlock**

    This change fixes an issue on Magento Cloud in production mode, where a block instance implement BlockInterface
    without inheriting from AbstractBlock.

    More information can be found in the [merge request #137](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/137)

-   **Bugfix: Handle modal content exceeding screen height**

    More information can be found in [issue #96](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/96)

-   **Allow modals to be opened from within modals without nesting in the DOM**

    Also, allows access to values set on the modal block via layout XML by making the modal instance method
    `getContentRender` public.

    More information can be found in [issue #86](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/86)

-   **Set default SVG icon dimensions to 24x24**

    Previously icons rendered without a width and a height did not render those attributes on the SVG image.  
    With this change, the width and height default to 24. This allows rendering icons  
    using `<?= $heroicons->heartHtml($cssClasses) ?>`, instead of always  
    using `<?= $heroicons->heartHtml($cssClasses, 24, 24) ?>`.
    The previous behavior can still be achieved by explicitly passing `null` as the `$width` and `$height` parameters.

    More information can be found in [issue #81](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/81)

-   **Make recently viewed products configurable in the system config**

    A couple of new system config fields have been added to allow configuring recently viewed products without having to
    manually set up widget instances. The new fields can be found at `Stores > Config > Catalog > Frontend`.  
    The configured values can be accessed using the new view model `RecentlyViewedProducts`.

    More information can be found in [issue #107](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/107)

-   **Magento Coding Standard compliance**

    Many small changes where made to make the code pass the Magento Coding Standards phpcs rules.

    More information can be found in the [merge request #150](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/150)

-   **Bugfix: fix return value of ProductCompare::showCompareSidebar

    The method `\Hyva\Theme\ViewModel\ProductCompare::showCompareSidebar` now returns the value from the correct system
    config setting `catalog/frontend/show_sidebar_in_list`. Previously it returned the value from the system config 
    setting `frontend/show_add_to_compare_in_list`.  
    The method isn't used in the default theme, so the bug didn't surface until now. If you used the `showCompareSidebar`
    method in custom code and need the previous value, you need to refactor your code to use
    `\Hyva\Theme\ViewModel\ProductCompare::showInProductList` instead.

### Removed

-   Nothing

## [1.1.8] - 2021-09-24

[1.1.8]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.7...1.1.8

### Added

-   **Add ViewModel for SendFriend**

    The new view model `\Hyva\Theme\ViewModel\SendFriend` supports getting the requested product and product image for the
    send-to-friend feature.
    Thank you Lucas an Staden @ ProxiBlue (@Realproxiblue) for the contribution!

-   **Add Supporting code for Search Autocomplete**

    The QuoteGraphql cart item customizable option query resolver now provides file values via
    `\Hyva\Theme\Model\CartItem\DataProvider\CustomizableOptionValue\File`.  
    The new view model `\Hyva\Theme\ViewModel\Search` provides access to the relevant configuration settings and proxies
    method calls through to the Magento native helpers.

    Thank you to faran cheema @ Aware Digital (@faran) for the contribution!

-   **Add getRecentlyViewedLifeTime method to ProductPage view model**

    This method provides the configured lifetime for the recently viewed products list.

    Thank you to Graham Catterall @ Aware Digital (@grazima) for the contribution!

-   **Add getRegisterUrl method to CustomerRegistration view model**

    This method provides the URL to the customer registration page.

    Thank you to Rouven Rieker @ Semaio (@therouv) for the contribution!

-   **Add shipping information to CartGraphqlQueries view model**

    This information is used by the estimate shipping feature.

-   **Add logo size and path resolver view models**

    This change provides forward compatibility for Magento versions before 2.4.3 where the view models where added to the
    core.

### Changed

-   **Bugfix: Fix menu navigation when no category exists**

    This change is backward compatible. Check the [commit ed4db0](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/ed4db0537e73d92cbf3088258af95c64c7f42ad9)
    For more information.

    Thank you to Thibaut Faucher @ Magentizy (@tfaucher) for the contribution!

-   **Bugfix: Collect cache tags for nested blocks that are cached in both the FPC ESI and BLOCK_HTML cache**

    This backward compatible change fixes a bug introduced by the splitting of the mobile and desktop menu in the 
    default-theme.

-   **Remove double slash from URL when loading section data**

    This change is backward compatible.

    Thank you to Thomas Hauschild @ Ulferts Prygoda (@thomas.hauschild) for the contribution!

-   **Bugfix: Cast price value is cast to a float when custom product types return null price**

    This change is backward compatible.

-   **Bugfix: Fix issues with old Safari browser**

    Details on backward compatible change can be found in the [issue #75](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/75)

    Thank you to Guus Portegies @ Cees en Co (@gjportegies) and Ryan Copeland (@ryan-copeland) for investigating!

-   **Bugfix: Load hyva_ prefix layout handles for layout handles added with `<update>`**

    Implementation details can be found in the [merge request #112](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/112)

    Thank you to Michał Biarda @ ORBA (@michal.biarda) for the contribution!

### Removed

-   **Remove the `stock_status` field from the CartGraphqlQueries view model**

    The change makes the cart page work on instances without MSI.  
    The field is only available if MSI is active and since it is currently not used by
    `hyva-themes/magento2-default-theme` and was only added preemptively, it was decided that it is best to remove it.

## [1.1.7] - 2021-08-25

[1.1.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.6...1.1.7

### Added

-   **Add cart data required for shipping and tax estimation to GraphQL cart query**
 
    This is a preparatory change for shipping estimation support that will be part of the next release.

    - Add [billing and shipping address](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/19326809442a64afd6e83b3e7aa2c6681f744d9c)
    - Add [is_virtual field](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/85fe6f0404aafba21f16c179b93c92af26354a35)
  
-   **Changelog for release 1.1.6**

    The changelog updates for the previous release where missing and are now included below.

### Changed

-   **Bugfix: Render modal overlay above store-switcher**

    See this [commit](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/ea5b3964c07ef083c6b3c31ea2f30ae2bbd861c4)

-   **Bugfix: Use product short description if present**

    The change introduced in the previous release 1.1.6 contained a bug that is now [fixed](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/265bfabca403b00d493fdb11117ffc1cf7854282). 

-   **Bugfix: Remove PageBuilder style tag content from product description excerpt**

    The `strip_tags` command keeps styles as part of the return value, which is not intended. This is particularly relevant in combination with PageBuilder.

    See fix [commit](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/d0f5a959a8121e4b7376953cc4197612f1519be5)

### Removed

-   Nothing

## [1.1.6] - 2021-08-12

### Added

-   **ViewModel CurrentProduct::loop() now collects cache tags**
    The method `\Hyva\Theme\ViewModel\CurrentProduct::loop` now collects cache tags from loaded products so that a collection of products is used to iterate over list-items now adds cache tags to the block that loops through the products.


-   **Store section_data_ids in cookie**
    This is needed for Luma fallback, so that all sections are refreshed when Luma wants to reload all sections.

    Without it, it will clear all sectionData from localStorage and not reload all sections previously available.
  
    See commit [`840dde14`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/840dde14b934fc500db46a9777071763b9b6a2d9)


-   **Add samesite:lax to cookies**
  
    Cookies stored from the frontend now include the `samesite` setting.

    See commit [`bacc30c0`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bacc30c05b16d2359ed9e80b22356cc5e1a49b27)


-   **The directory `web/tailwind` is now excluded from deployments**

    Since all files in web/tailwind are not needed in pub/static, these should be excluded from deployment.
    Otherwise, also all files in node_modules are copied over to pub/static/frontend.
    On the default Hyvä Theme, this reduces the amount of files to deploy from 13k to 3k and deployment time roughly in half.

    See commit [`383df942`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/383df942d63f33d46db4a70964f50698058c2777)


-   **PageConfig ViewModel**

    We've added a ViewModel that enables you to pull in PageConfig data, such as the current page's layout.

    Implemented methods are `\Hyva\Theme\ViewModel\PageConfig::getPageLayout()` and `\Hyva\Theme\ViewModel\PageConfig::getPageConfig()`

    See `src/ViewModel/PageConfig.php`


-   **Resolved an error in `CartItemsResolverPlugin` if a cartItem contained an error**

    Magento adds cartItem errors as a `false` item to the cartitem results. This caused an error in the CartItemsResolverPlugin (`src/Plugin/QuoteGraphQL/CartItemsResolverPlugin.php`) 

    See commit [`bbefc0e8`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bbefc0e80a29c4fcbe4768a580a8b76a849f09ac)
  

-   **The GraphQl `CartItemInterface` now contains errors per cartItem**

    The CartItemInterface now returns `error` for each cartItem.

    See commit [`bbefc0e8`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bbefc0e80a29c4fcbe4768a580a8b76a849f09ac)
  

-   **The GraphQl `CartItemInterface` now contains stock_status per cartItem**

    The CartItemInterface now returns `stock_status` for each cartItem.

    See commit [`bbefc0e8`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bbefc0e80a29c4fcbe4768a580a8b76a849f09ac)


-   **Generic Modal dialogs**

    We now have robust support for Modals, including support for accessibility like focus traps.

    See `\Hyva\Theme\ViewModel\Modal` or [`Merge Request !91`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/91)

    Instructions on how to use the new Modals are added to the documentation (look for "Modal dialogs").

### Changed

-   **Method in `\Hyva\Theme\Service\Navigation` changed to public**

    The methods `\Hyva\Theme\Service\Navigation::getCategoryAsArray` and `\Hyva\Theme\Service\Navigation::getCategoryTree` are now public to enable plugins.

    See commit [`5eb555bd`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/5eb555bdfacdfc4e17fdf179345d757bc96c4aee)

    Thanks to Kiel Pykett (Fisheye) for contributing


-   **stripTags and excerpt are now optional for `ProductPage::getShortDescription()`**

    The method `\Hyva\Theme\ViewModel\ProductPage::getShortDescription` now accepts the optional boolean parameters`$excerpt` and `$stripTags`, both defaulting to `true`

    This is a non-breaking change.
  
    See commit [`dc858cdf`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/dc858cdf65064c57f3275236a14176d42db2b2a0)

### Removed

-   None

[1.1.6]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.5...1.1.6

## [1.1.5] - 2021-06-17

### Added

-   None

### Changed

- A bugfix for the ViewModelCacheTags class that expected at least one view model on a page to implement the IdentityInterface.  
  This situation could happen on customized Hyvä based themes. This fix removes this requirement, so no error is thrown any more.

### Removed

-   None

[1.1.5]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.4...1.1.5

## [1.1.4] - 2021-06-16

### Added

-   **ViewModel Cache Tags**

    ViewModels can now contain cache tags which are added to the block that renders output from that ViewModel.
    This enables you to, for example, render menu-items in any block and add the cache tags of the menu items to that block.
    
    This requires you to add a getIdentities method to the ViewModel you use to load in identities with.

    See commit [`cd8c38bb`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/cd8c38bb85f52641759d1ec0e70ee2c2f6062c99)

-   **Cookie Consent now prevents cookies from being stored until accepted**

    It is now possible to prevent the theme to store cookies in the browser if the client doesn't give consent and the Magento 2 cookie restriction feature is enabled.
    
    The cookies are stored in a temp js object window.cookie_temp_storage and only if the user already gave the consent (information stored in  user_allowed_save_cookie cookie) or after an explicit confirmation (the banner of hyva-themes/magento2-default-theme/Magento_Cookie/templates/notices.phtml), they will be saved.
    
    There is also a config to save necessary cookies that don't require confirmation (the e-commerce without these cookies cannot work, eg: form_key), stored in the window.cookie_consent_configuration object.
    
    In this object is also possible to add different categories to the cookie that requires different logic to be handled; the variable cookie_consent needs to be properly set for this.
    Cookies not declared in the cookie_consent_configuration are saved only after the confirmation.

    See commit [`a19f65d4`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/a19f65d4b9085bfd027fe376ea764b5801c1a955)

    Thanks to Mirko Cesaro (Bitbull) for contributing

-   **ProductPage ViewModel now has productAttributeHtml() method**

    This method parses template tags (directives) for attributes so that attributes like `description` now render store variables and other `{{directives}}`

    See `src/ViewModel/ProductPage.php`
    
    Thanks to Vincent MARMIESSE (PH2M) for contributing

-   **Cart GraphQl queries now contain available shipping methods**

    Added available methods with and without vat
    Added method_code to allow matching
    
    See `src/ViewModel/Cart/GraphQlQueries.php`

    Thanks to Alexander Menk (imi) for contributing.

-   **Added EmailToFriend viewModel** loading configuration values for SendFriend functionality

    See `src/ViewModel/EmailToFriend.php`

-   **Added `format` method to ProductPrice view model**

    When calling $priceViewModel->currency($amount), the amount is treated as the
    base currency and converted to the current currency before being formatted.

    If $amount already is in the store view currency, this leads to double conversions.

    Now, the pricecurrency format() method is exposed through the view model.

    See `src/ViewModel/ProductPrice.php`

-   **Add view model for easy use of generic slider**
  
    A new `Slider` View Model was added that allows you to create more generic sliders in conjection with a generic slider phtml file in `Magento_Theme::elements/slider-php.phtml` and `Magento_Theme::elements/slider-gql.phtml`

    See `src/ViewModel/Slider.php`

### Changed

-   None

### Removed

-   None

[1.1.4]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.3...1.1.4

## [1.1.3] - 2021-05-07

### Added

-   **Fix: polyfill baseOldPrice in priceinfo for Magento versions < 2.4.2**

    Hyva Themes 1.1.2 depends on the baseOldPrice being set, but that property only was added in Magento 2.4.2. This
    Release adds compatibility for older Magento versions by polyfilling the price info baseOldPrice if it doesn't exist.
  
### Changed

-   **Deprecated \Hyva\Theme\ViewModel\ProductPrice::setProduct()**

    Pass the $product instance as an argument to price methods instead of using internal state.
    This improves reusability of templates regardless of the order they are rendered in.
    The method still is preserved for backward compatibility, but is no longer used by default Hyva theme.
    https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/37

### Removed

-   None

[1.1.3]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.2...1.1.3

## [1.1.2] - 2021-05-03

### Added

-   **GQL support for customOption of `file` type**

    See `src/Model/CartItem/DataProvider/CustomizableOptionValue/File.php`, `src/Plugin/QuoteGraphQL/CustomizableOptionPlugin.php`

-   **GQL added custom options for Virtual, Downloadable and Bundle to cart**

    See `src/ViewModel/Cart/GraphQlQueries.php`
    Configurables are not yet included due to a core-bug that will be fixed in 2.4.3: https://github.com/magento/magento2/issues/31180


-   **customOptions viewModel** that allows to override the pthml file for customOptions of dropdown/multiselect/radio/checkbox types.

    By default, Magento renders `select` custom-options with a toHtml() method in `\Magento\Catalog\Block\Product\View\Options\Type\Select\Multiple`. This can now be replaced with a proper pthml file using this viewModel.

    See `src/ViewModel/CustomOption.php`

-   **ProductPrices viewModel** that calculates product prices, tier prices and custom options on Product Detail pages.

    See `src/ViewModel/ProductPrice.php`

### Changed

-   None

### Removed

-   None

[1.1.2]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.1...1.1.2

## [1.1.1] - 2021-04-08

### Added

-   **SwatchRenderer ViewModel**

    Used to determine wheter an attribute should render as swatch: `isSwatchAttribute($attribute)`

    See `src/ViewModel/SwatchRenderer.php`

### Changed

-   None

### Removed

-   None

[1.1.1]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.0...1.1.1

## [1.1.0] - 2021-04-02

### Added

-   **Icon Directive** `{{icon}}` to render SVG icons from PHTML files or CMS content

    Icons can now be rendered with a directive: `{{icon "heroicons/solid/shopping-cart" classes="w-6 h-6" width=12 height=12}}`

    See `src/Model/Template/IconProcessor.php`

    Thanks to Helge Baier (integer_net) for contributing


-   **Current Category Registry ViewModel**

    The current category can now be fetched with: `$viewModels->require(\Hyva\Theme\ViewModel\CurrentCategory::class);`

    See `src/ViewModel/CurrentCategory.php`

    Thanks to Gennaro Vietri (Bitbull) for contributing

-   **Cart Items ViewModel** that loads the items currently in cart

    See `src/ViewModel/Cart/Items.php`

    Thanks to Vincent MARMIESSE (PH2M) for contributing


-   **Compare Products ViewModel** that loads preferences for showing compared products in the sidebar.

    Additionally, product images are added to the compared product in customer section data.

    See `src/ViewModel/ProductCompare.php` and `src/Plugin/CompareCustomerData/AddImages.php`

    Thanks to Timon de Groot (Mooore)


-   **Customer Registration ViewModel** that loads `isAllowed()` for customer registration from config

    See `src/ViewModel/CustomerRegistration.php`

    Thanks to Barry vd. Heuvel (Fruitcake) 


-   **Currency ViewModel** that retrieves current currency (and currency symbol) and currency-switcher url/postData

    See `src/ViewModel/Currency.php`

-   **ProductListItem ViewModel** that retrieves formatted product prices in product lists

    See `src/ViewModel/ProductListItem.php`

-   **StoreSwitcher ViewModel** that loads available groups/stores for the store/language switchers.

    See `src/ViewModel/StoreSwitcher.php`


-   **Built-With header added**

    We've added a `x-built-with: Hyva Themes` header to pages on the frontend that are rendered with Hyvä.

### Changed

-   **Customer Section data invalidation**  
    on store-switch. The JavaScript variable `CURRENT_STORE_CODE` is now added to `src/view/frontend/templates/page/js/variables.phtml`  
    and checked against in `src/view/frontend/templates/page/js/private-content.phtml` to invalidate customer section-data when switching between stores.

    Thanks to Gennaro Vietri (Bitbull) for contributing

-   **FormKey retrieval is now global under hyva.getFormKey()**

    Form Keys are no longer generated in `src/view/frontend/templates/page/js/cookies.phtml` (though still initialized from here).

    `hyva.getFormKey()` can now be used globally instead of `document.querySelector('input[name=form_key]').value`. This will be refactored in the default theme in the future.

    Thanks to Gennaro Vietri (Bitbull) for contributing

-   **formatPrice() is now a global function hyva.getFormKey()**

    `hyva.getFormKey()` has been added to `src/view/frontend/templates/page/js/hyva.phtml`

-   **SvgIcons are now cached per Theme**

    See `src/ViewModel/SvgIcons.php`

    Thanks to Paul van der Meijs (RedKiwi) for contributing

-   **CSP Whitelist added for unsplash.com** Magento_Csp can now be enabled by default. Previously, the unsplash.com images on the homepage would throw console errors.

    Thanks to Aad Mathijssen (Isaac) for requesting this

-   **SVG files with preset width and height now work with SvgIcons**

    Previously, an error would be thrown: 

    ```Exception #0 (Exception): Warning: SimpleXMLElement::addAttribute(): Attribute already exists in /Dev/www/chlobo/vendor/hyva-themes/magento2-theme-module/src/ViewModel/SvgIcons.php on line 84```

    Thanks to Fabian Schmengler (integer_net) for contributing

-   **The `ProductInterface` in GraphQL calls now contain `visibility` and `status`**

    We can now filter product-lists, loaded through GraphQL, by visibility code and status.
    
    This has been added because 'linked products' (upsells, cross-sells, upsells) are not filtered by visibility in store.

### Removed

-   **`<script>` tags no longer contain the `defer` attribute**

  Since these have no effect...

[1.1.0]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.0.7...1.1.0  


## [1.0.7] - 2021-02-15

[1.0.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.0.0...1.0.7

### Added

-   Added readme
-   Added this changelog

### Changed

-   Fix compare configuration path

### Removed

-   None

## 1.0.0 - 2020-12-03

Initial Release 🎉
