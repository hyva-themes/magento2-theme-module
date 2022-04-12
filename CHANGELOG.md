# Changelog - Theme Module

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

[Unreleased]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.13...main

## [1.1.13] - 2022-04-12

[1.1.13]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.12...1.1.13

### Added

- **Add Confirmation Modal Dialog**

  This is an extension of the modal dialogs. 

  More details can be found in [merge request #178](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/178)

- **Add method displayCartTaxWithGrandTotal to get tax config to cart totals view model**

  More details can be found in [issue #156](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/156)

- **Show unavailable shipping methods with error code on estimate shipping**

  This replicates the behavior on Luma more closely.

  More details can be found in the default theme [issue #292](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/292)

- **Add method to render description excerpt for any product**

  Previously the method was only available for the current product on a PDP. The new method accepts any product instance.

  More details can be found in [issue #159](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/159)

### Changed

- **Fix incompatibility with type changes introduced in Magento Core**

  Fixed an inconsistency where types that were not present in the original method are introduced by this [PR](https://github.com/magento/magento2-page-builder/pull/528) from Magento.

  More details can be found in [merge request #179](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/179)

  Big thanks to Mohamed Kaid (mokadev) for the contribution!
 
- **Remove argument types for compatibility with TaxJar**

  The TaxJar module does not follow the same typing as core Magento. By relaxing the type constraints this change allows the code to work with the TaxJar module.

  More details can be found in [issue #146](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/146)

- **Fix form_key race condition on slow internet connections**

  On slow internet connections there was an issue where when the page is submitted before everything has loaded, then it returned “Invalid Form Key. Please refresh the page.”.  

  More details can be found in [issue #140](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/140)

  Big thanks to Luke Collymore (Develo Design) for finding the bug and providing the solution!

- **Use full locale to determine currency format**

  So far only the language was used to determine how to format the currency, but in some cases that is not enough, for example `de_CH` (Switzerland German) vs `de_DE` (Germany German). 

  More details can be found in the default theme [issue #345](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/345)
  
- **Product Sliders: allow filtering by category and sorting by position**

  This scenario is treated by Magento as a special case, so it needs to be handled as such in the slider container, too.

  More details can be found in the default theme [issue #354](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/354)

- **Centralize product list item block rendering**

  Previously the logic to render a product list item was repeated in several templates. This requried updating multiple
  files with the same change, and caused inconsistencies in regards to caching.

  More details can be found in [issue #154](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/154)

- **Fix product list price for wrong group ID**
  
  This fix is related to the previous item. Previously tax setting depending on the customer group ID where cached
  using the wrong cache ID, so the price for the customer group that happened to visit a list page first got shown
  to every group.

  More details can be found in [issue #155](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/155)
  

- **PageBuilder: fix unable to findSVG icon "X" in admin preview**

  More details can be found in [issue #157](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/157)

  Many thanks to Oli Jaufmann and Nguyen Miha (both from JaJuMa) for the contribution!

- **Configurable Product cart image not using Product Image Itself as per admin settings**

  Previously the setting *Stores -> Configuration -> Sales -> Checkout -> Shopping Cart -> Configurable Product Image -> Product Image Itself* had no effect.  
  This was originally due to the Magneto GraphQL API not providing the parent product image. This data has been added to the GraphQL API in release 2.4.3, so now Hyvä supports showing the configurable product image, too if newer Magento versions.

  More details can be found in the default theme [issue #326](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/326)

  Big thank you to Lucas van Staden (ProxyBlue) for the contribution! 

- **Allow manipulating modal event subscriber functions**

  More information can be found in the [issue #160](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/160)

### Removed

- **Remove Magento_SendFriend dependency, so it can be removed if not needed**

  Without this change static-content:deploy failed if Magento_SendFriend was removed.

  More details can be found in the default theme [merge request #287](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/287)

  Many thanks to Peter Jaap Blaakmeer (Elgentos) for the contribution!

## [1.1.12] - 2022-02-07

[1.1.12]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.11...1.1.12

### Added

- Nothing

### Changed

- **Bugfix: error after removing last product from cart**

  After deleting the last product from the shopping cart, a red warning message "Internal server error" was shown.
  This bug was reported as fixed in the previous release, but in fact was not fixed in all cases.

  More information can be found in [issue #129](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/129)

- **Bugfix: include attributes argument in SvgIcons cache key**

  Previously, when the `$arguments` parameter value was changed, the previously rendered SVG was returned.

  More information can be found in [Merge Request #172](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/172)

### Removed

- Nothing

## [1.1.11] - 2022-01-28

[1.1.11]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.10...1.1.11

### Added

- Nothing

### Changed

- **Bugfix: error after removing last product from cart**

  After deleting the last product from the shopping cart, a red warning message "Internal server error" was shown.

  More information can be found in [issue #129](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/129) 

- **Allow non-Hyvä SVG icon sets to be used with SvgIcons view model**

  Previously the entire SvgIcons class needed to be overridden because of a hardcoded value. Now the value can be set
  in di.xml through virtual types.

  Many thanks to Timon de Groot (Mooore) for the contribution!

  More information can be found in [Merge Request #147](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/147)

- **Update HeroIcons method annotations**

  The `@method` annotations on the interface no longer matched the backing implementation in `SvgIcons::renderHtml`.

  More information can be found in [issue #128](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/128)

### Removed

- Nothing


## [1.1.10] - 2022-01-14

[1.1.10]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.9...1.1.10

### Added

- **Alpine.js x-intersect plugin**

  This is a 'forward-compatible' backport of x-intersect from Alpine V3. It will help make the transition to Alpine v3
  smoother. More info about the Alpine.js intersect plugin can be found at https://alpinejs.dev/plugins/intersect

- **Allow excluding elements from the focus trapping in modals**

  When a modal with a backdrop is shown, the website elements outside the modal dialog can no longer be focused with Tab
  / Shift-Tab. Setting everything to inert has side effects for some use cases. Cookie consent is one of them. If you
  manage to open a modal any consent banner can't be used anymore even if it is in the foreground.

  To exclude elements from focus trapping, use the `excludeSelectorsFromFocusTrapping` method with selectors. For
  example: `$modalViewModel->createModal()->excludeSelectorsFromFocusTrapping('#cookie-consent', '[x-no-trap]')`

### Changed

- **Updated Apline from 2.7.0 to 2.8.2**

  This change is related to the new Alpine x-intersect plugin backport and will help the future upgrade to Alpine v3
  will go smoother.

- **Fix PageBuilder breaking Alpine HTML element attributes.**

  More information can be found in [issue #114](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/114)

- **Allow modal renderer blocks to be mutated**

  This change allows setting data on the block to renders a modal template,  
  for example `$modal->getContentRenderer()->assign('foo', $foo)`.

  More information can be cound in the [merge request #157](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/157)

- **Bugfix: If set, use logo_file block argument to render logo like in Luma**

  This allows setting the logo in layout XML as documented in the [devdocs](https://devdocs.magento.com/guides/v2.4/frontend-dev-guide/themes/theme-create.html#theme_logo).

  More information can be found in the [hyva-themes/magento2-default-theme issue #309](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/309).

- **Bugfix: fix tax rate labels in GraphQL response to make cart totals consistent**

  This is a bug fix for an inconsistency in the Magento core behavior.

  More information can be found in [issue #120](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/120). 

- **Allow exclamation mark in PageBuilder CSS classes**

  More information can be found in [issue #121](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/121)

- **Bugfix: fix fetchPrivateContent failure when using BrowserSync proxy**

  More information can be found in [issue #122](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/122)

### Removed

- Nothing


## [1.1.9] - 2021-11-29

[1.1.9]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.8...1.1.9

### Added

- **Add View Model to fetch lists of products from templates**

  The new view model `Hyva\Theme\ViewModel\ProductList` can be used to fetch any type of product list inside a template,
  including related, upsell and crosssell products.
  It is used in hyva-themes/magento2-default-theme when rendering product sliders.

  More information an be found in [issue #84](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/84)

- **Give access to a product image instance via the ProductPage view model**

  The new view model method `getImage` can be used to retrieve a product image instance without relying on the core
  abstract product block class. It is used for rendering product sliders.

- **Merge PageBuilder compatibility from compat module into theme-module**

  Previously PageBuilder support required using a compatibility module. Now that PageBuilder is included with
  Magento Open Source, it makes sense to support it out-of-the-box in Hyvä Themes.
  The PageBuilder compatibility module still is maintained for backward compatibility.

  More information an be found in [issue #68](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/68)

  Many thanks to John Hughes (Fisheye) for the contribution!

- **Allow setting additional HTML attributes on SVG icons**

  In Hyva a lot of additional attributes are used on element. For example, the Alpine.js `:class` binding is used is
  very often, but can't be set on an SVG icon at this moment.
  With this change an optional `array $attributes` argument is added to the method signature.

  More information an be found in [merge request #123](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/123)

  Many thanks to Arjen Miedema (Elgentos) for the contribution!

- **Add ViewModel to provide access to product stock information**

  To render the appropriate product qty form some stock item information is required, for example the minimum order 
  quantity, or if decimal quantities are allowed or not.

  The new view model is used in the theme when rendering the product add-to-cart form.

  More information can be found in the [merge request #130](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/130)

- **Add method to fetch catalog/seo/product_use_categories system config value**

  This change to the ProductPage view model is required to fix a product listing page caching issue in
  [hyva-themes/magento-default-theme](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/260).

  More information can be found in the [merge request #134](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/134)

- **Add method to retrieve a blocks cache tags from a view model**

  This new method on the BlockCache view model is helpful because Hyvä often uses generic template blocks instead of 
  specific block classes. For this to work well with the block_html cache group, the cache_tags property has to be set
  within templates.

  More information can be found in the [merge request #135](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/135)

- **Preserve local only section data for Luma checkout compatibility**

  More information an be found in [issue #99](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/99)

- **Add ViewModelRegistry return type hint based on class argument**

  This change allows using `$viewModels->require($className)` without a PHPDoc type hint for the return value using
  PHPStorms new generics annotation.

  More information can be found in the [merge request #140](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/140)

  Many thanks to Thijs de Witt (Trinos) for the contribution!

### Changed

- **Bugfix: Improve LogoPathResolver so it works with Magento 2.4.3 and newer**
  
  The LogoPathResolver also continues to work with Magento versions 2.4.0 - 2.4.2.

  More information an be found in [issue #82](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/82)

- **Bugfix: Allow using multiple slider instances with the same template on one page**

  Previously the generated block name was determined by the slider template. Now `uniqid` is used to 
  generate the block names.

  More information an be found in [issue #78](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/78)

- **Allow items for sliders rendered with PHP to be collections**

  Previously the items had to be an array, now they can be any iterable.

- **Improve Tailwind CSS class name validation regex for PageBuilder**

  Now `/`, `(`, `)`, `%`, `,` and digits are also allowed enabling classes such as `w-1/2`,  `grid-[repeat(3,33%)]`

- **Bugfix: fix converting camelCase to kebab-case for SVG icons with digits** 

  Previously: `menuAlt2 -> menu-alt2`
  Now (fixed): `menuAlt2 -> menu-alt-2`

  More information an be found in [issue #87](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/87)

  Many thanks to Thijs de Witt (Trinos) for the contribution!

- **Bugfix: resolve ProductPrice being cached if multiple products use ProductPrice**

  More information an be found in [issue #88](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/88)

  Many thanks to Wahid Nory (Elgentos) for the contribution!

- **Bugfix: use GraphQL variables instead ot JS string templates for all queries and mutations**

  This resolves a number of bugs related to escaping and serialization of query parameters, and also allows
  editing the queries with the GraphQL query editor as described in [the docs](https://docs.hyva.io/hyva-themes/writing-code/customizing-graphql.html).

  More information can be found in the [merge request #127](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/127)
  and the [related default theme MR](https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/merge_requests/301).

- **Bugfix: Fix constructor integrity check for preference in Magento > 2.4.1**

  The error `Extra parameters passed to parent construct` occurred when running `setup:di:compile` on Magento 2.4.2 or
  newer.

  More information an be found in [issue #85](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/85)

- **Allow product-compare system.xml settings to be set on store scope**

  These compare product system config settings are not part of stock Magento.

  More information can be found in the [merge request #131](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/131)

  Many thanks to Timon de Groot (Mooore) for the contribution!

- **Bugfix: Do not render double slash when using SVG icons with no svg iconset**

  More information can be found in [issue #93](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/93)

- **Remove amount of cache tags for large top menus**

  With Varnish, the top menu is requested using ESI. In that case the cache tags for each category in the menu where
  included in a HTTP response header, which could lead to the header size limit being exceeded.  
  This change replaces the top menu cache tags with a single `hyva_nav`  cache tag if more than 200 cache tags
  would be included in the response.

  More information can be found in [issue #63](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/63)

- **Bugfix: Do not assume very block inherits from AbstractBlock**

  This change fixes an issue on Magento Cloud in production mode, where a block instance implement BlockInterface
  without inheriting from AbstractBlock.

  More information can be found in the [merge request #137](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/137)

- **Bugfix: Handle modal content exceeding screen height**

  More information can be found in [issue #96](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/96)

- **Allow modals to be opened from within modals without nesting in the DOM**

  Also, allows access to values set on the modal block via layout XML by making the modal instance method
  `getContentRender` public.

  More information can be found in [issue #86](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/86)

- **Set default SVG icon dimensions to 24x24**

  Previously icons rendered without a width and a height did not render those attributes on the SVG image.  
  With this change, the width and height default to 24. This allows rendering icons  
  using `<?= $heroicons->heartHtml($cssClasses) ?>`, instead of always  
  using `<?= $heroicons->heartHtml($cssClasses, 24, 24) ?>`.
  The previous behavior can still be achieved by explicitly passing `null` as the `$width` and `$height` parameters.

  More information can be found in [issue #81](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/81)

- **Make recently viewed products configurable in the system config**

  A couple of new system config fields have been added to allow configuring recently viewed products without having to
  manually set up widget instances. The new fields can be found at `Stores > Config > Catalog > Frontend`.  
  The configured values can be accessed using the new view model `RecentlyViewedProducts`.

  More information can be found in [issue #107](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/107)

- **Magento Coding Standard compliance**

  Many small changes where made to make the code pass the Magento Coding Standards phpcs rules.

  More information can be found in the [merge request #150](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/150)

- **Bugfix: fix return value of ProductCompare::showCompareSidebar

  The method `\Hyva\Theme\ViewModel\ProductCompare::showCompareSidebar` now returns the value from the correct system
  config setting `catalog/frontend/show_sidebar_in_list`. Previously it returned the value from the system config 
  setting `frontend/show_add_to_compare_in_list`.  
  The method isn't used in the default theme, so the bug didn't surface until now. If you used the `showCompareSidebar`
  method in custom code and need the previous value, you need to refactor your code to use
  `\Hyva\Theme\ViewModel\ProductCompare::showInProductList` instead.

### Removed

- Nothing

## [1.1.8] - 2021-09-24

[1.1.8]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.7...1.1.8

### Added

- **Add ViewModel for SendFriend**

  The new view model `\Hyva\Theme\ViewModel\SendFriend` supports getting the requested product and product image for the
  send-to-friend feature.
  Thank you Lucas an Staden @ ProxiBlue (@Realproxiblue) for the contribution!

- **Add Supporting code for Search Autocomplete**

  The QuoteGraphql cart item customizable option query resolver now provides file values via
  `\Hyva\Theme\Model\CartItem\DataProvider\CustomizableOptionValue\File`.  
  The new view model `\Hyva\Theme\ViewModel\Search` provides access to the relevant configuration settings and proxies
  method calls through to the Magento native helpers.

  Thank you to faran cheema @ Aware Digital (@faran) for the contribution!

- **Add getRecentlyViewedLifeTime method to ProductPage view model**

  This method provides the configured lifetime for the recently viewed products list.

  Thank you to Graham Catterall @ Aware Digital (@grazima) for the contribution!

- **Add getRegisterUrl method to CustomerRegistration view model**

  This method provides the URL to the customer registration page.

  Thank you to Rouven Rieker @ Semaio (@therouv) for the contribution!

- **Add shipping information to CartGraphqlQueries view model**

  This information is used by the estimate shipping feature.

-**Add logo size and path resolver view models**

  This change provides forward compatibility for Magento versions before 2.4.3 where the view models where added to the
  core.

### Changed

- **Bugfix: Fix menu navigation when no category exists**

  This change is backward compatible. Check the [commit ed4db0](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/ed4db0537e73d92cbf3088258af95c64c7f42ad9)
  for more information.

  Thank you to Thibaut Faucher @ Magentizy (@tfaucher) for the contribution!

- **Bugfix: Collect cache tags for nested blocks that are cached in both the FPC ESI and BLOCK_HTML cache**

  This backward compatible change fixes a bug introduced by the splitting of the mobile and desktop menu in the 
  default-theme.

- **Remove double slash from URL when loading section data**

  This change is backward compatible.

  Thank you to Thomas Hauschild @ Ulferts Prygoda (@thomas.hauschild) for the contribution!

- **Bugfix: Cast price value is cast to a float when custom product types return null price**

  This change is backward compatible.

- **Bugfix: Fix issues with old Safari browser**

  Details on backward compatible change can be found in the [issue #75](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/75)

  Thank you to Guus Portegies @ Cees en Co (@gjportegies) and Ryan Copeland (@ryan-copeland) for investigating!

- **Bugfix: Load hyva_ prefix layout handles for layout handles added with <update>**

  Implementation details can be found in the [merge request #112](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/112)

  Thank you to Michał Biarda @ ORBA (@michal.biarda) for the contribution!

### Removed

- **Remove the `stock_status` field from the CartGraphqlQueries view model**

  The change makes the cart page work on instances without MSI.  
  The field is only available if MSI is active and since it is currently not used by
  `hyva-themes/magento2-default-theme` and was only added preemptively, it was decided that it is best to remove it.

## [1.1.7] - 2021-08-25

[1.1.7]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.6...1.1.7

### Added

- **Add cart data required for shipping and tax estimation to GraphQL cart query**
 
  This is a preparatory change for shipping estimation support that will be part of the next release.

  - Add [billing and shipping address](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/19326809442a64afd6e83b3e7aa2c6681f744d9c)
  - Add [is_virtual field](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/85fe6f0404aafba21f16c179b93c92af26354a35)
  
- **Changelog for release 1.1.6**

  The changelog updates for the previous release where missing and are now included below.

### Changed

- **Bugfix: Render modal overlay above store-switcher**

  See this [commit](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/ea5b3964c07ef083c6b3c31ea2f30ae2bbd861c4)

- **Bugfix: Use product short description if present**

  The change introduced in the previous release 1.1.6 contained a bug that is now [fixed](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/265bfabca403b00d493fdb11117ffc1cf7854282). 

- **Bugfix: Remove PageBuilder style tag content from product description excerpt**

  The `strip_tags` command keeps styles as part of the return value, which is not intended. This is particularly relevant in combination with PageBuilder.

  See fix [commit](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/d0f5a959a8121e4b7376953cc4197612f1519be5)

### Removed

- nothing

## [1.1.6] - 2021-08-12

### Added
- **ViewModel CurrentProduct::loop() now collects cache tags**
  The method `\Hyva\Theme\ViewModel\CurrentProduct::loop` now collects cache tags from loaded products so that a collection of products is used to iterate over list-items now adds cache tags to the block that loops through the products.


- **Store section_data_ids in cookie**
  This is needed for Luma fallback, so that all sections are refreshed when Luma wants to reload all sections.

  Without it, it will clear all sectionData from localStorage and not reload all sections previously available.
  
  See commit [`840dde14`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/840dde14b934fc500db46a9777071763b9b6a2d9)


- **Add samesite:lax to cookies**
  
  Cookies stored from the frontend now include the `samesite` setting.

  See commit [`bacc30c0`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bacc30c05b16d2359ed9e80b22356cc5e1a49b27)


- **The directory `web/tailwind` is now excluded from deployments**

  Since all files in web/tailwind are not needed in pub/static, these should be excluded from deployment.
  Otherwise, also all files in node_modules are copied over to pub/static/frontend.
  On the default Hyvä Theme, this reduces the amount of files to deploy from 13k to 3k and deployment time roughly in half.

  See commit [`383df942`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/383df942d63f33d46db4a70964f50698058c2777)


- **PageConfig ViewModel**

  We've added a ViewModel that enables you to pull in PageConfig data, such as the current page's layout.

  Implemented methods are `\Hyva\Theme\ViewModel\PageConfig::getPageLayout()` and `\Hyva\Theme\ViewModel\PageConfig::getPageConfig()`

  See `src/ViewModel/PageConfig.php`


- **Resolved an error in `CartItemsResolverPlugin` if a cartItem contained an error**

  Magento adds cartItem errors as a `false` item to the cartitem results. This caused an error in the CartItemsResolverPlugin (`src/Plugin/QuoteGraphQL/CartItemsResolverPlugin.php`) 

  See commit [`bbefc0e8`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bbefc0e80a29c4fcbe4768a580a8b76a849f09ac)
  

- **The GraphQl `CartItemInterface` now contains errors per cartItem**

  The CartItemInterface now returns `error` for each cartItem.

  See commit [`bbefc0e8`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bbefc0e80a29c4fcbe4768a580a8b76a849f09ac)
  

- **The GraphQl `CartItemInterface` now contains stock_status per cartItem**

  The CartItemInterface now returns `stock_status` for each cartItem.

  See commit [`bbefc0e8`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/bbefc0e80a29c4fcbe4768a580a8b76a849f09ac)


- **Generic Modal dialogs**

  We now have robust support for Modals, including support for accessibility like focus traps.

  See `\Hyva\Theme\ViewModel\Modal` or [`Merge Request !91`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/merge_requests/91)

  Instructions on how to use the new Modals are added to the documentation (look for "Modal dialogs").

### Changed
- **Method in `\Hyva\Theme\Service\Navigation` changed to public**

  The methods `\Hyva\Theme\Service\Navigation::getCategoryAsArray` and `\Hyva\Theme\Service\Navigation::getCategoryTree` are now public to enable plugins.

  See commit [`5eb555bd`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/5eb555bdfacdfc4e17fdf179345d757bc96c4aee)

  Thanks to Kiel Pykett (Fisheye) for contributing


- **stripTags and excerpt are now optional for `ProductPage::getShortDescription()`**

  The method `\Hyva\Theme\ViewModel\ProductPage::getShortDescription` now accepts the optional boolean parameters`$excerpt` and `$stripTags`, both defaulting to `true`

  This is a non-breaking change.
  
  See commit [`dc858cdf`](https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/commit/dc858cdf65064c57f3275236a14176d42db2b2a0)

### Removed
- none

[1.1.6]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.5...1.1.6

## [1.1.5] - 2021-06-17

### Added
- none

### Changed
- A bugfix for the ViewModelCacheTags class that expected at least one view model on a page to implement the IdentityInterface.  
  This situation could happen on customized Hyvä based themes. This fix removes this requirement, so no error is thrown any more.

### Removed
- none

[1.1.5]: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/compare/1.1.4...1.1.5

## [1.1.4] - 2021-06-16
### Added
- **ViewModel Cache Tags**

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
