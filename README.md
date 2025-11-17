# Hyvä Themes - Theme Module

[![Hyvä Themes](https://hyva.io/media/wysiwyg/logo-compact.png)](https://hyva.io/)

## hyva-themes/magento2-theme-module

![Supported Magento Versions][ico-compatibility]

Compatible with Magento 2.4.0 and higher.
 
## Installation

You need a valid Hyvä packagist.com key.

Get a free key by registering an account at [www.hyva.io](https://www.hyva.io) and creating one from your account dashboard.

You will receive instruction like the following after creating your packagist.com key:

```
# this command adds your key to your projects auth.json file
# replace yourLicenseAuthentificationKey with your own key
composer config --auth http-basic.hyva-themes.repo.packagist.com token yourLicenseAuthentificationKey
# replace yourProjectName with your project name
composer config repositories.private-packagist composer https://hyva-themes.repo.packagist.com/yourProjectName/
```

Run those commands, and then, install the theme and its dependencies with composer:

```sh
composer require hyva-themes/magento2-default-theme
```
Next, run the Magento setup command:

```sh
bin/magento setup:upgrade
```
Navigate to the `Content > Design > Configuration` admin section and activate the hyva/default theme.

Please see the [Getting Started](https://docs.hyva.io/hyva-themes/getting-started/index.html#getting-started) documentation for further information.

## License

This package is primarily licensed under the **Open Software License (OSL 3.0)**.

* **Copyright:** Copyright © 2020-present Hyvä Themes. All rights reserved.
* **License Text (OSL 3.0):** The full text of the OSL 3.0 license can be found in the `LICENSE.txt` file within this package, and is also available online at [http://opensource.org/licenses/osl-3.0.php](http://opensource.org/licenses/osl-3.0.php).

### Additional Licenses for Included Assets

This package also contains Alpine.js and SVG icons under separate licenses:

* **Alpine.js:** Copyright © 2019-2025 Caleb Porzio and contributors and distributed under the MIT license. The full text of this license can be found in `src/view/base/web/js/ALPINE_LICENSE_MIT.txt` or online at [raw.githubusercontent.com/alpinejs/alpine/refs/heads/main/LICENSE.md](https://raw.githubusercontent.com/alpinejs/alpine/refs/heads/main/LICENSE.md)  
* **Heroicons:** SVG icons from [https://heroicons.com/](https://heroicons.com/) are licensed under the MIT license. The full text of this license can be found in `src/view/frontend/web/svg/heroicons/HEROICONS_LICENSE_MIT.txt`.
* **Lucide Icons:** SVG icons from [https://lucide.dev/](https://lucide.dev/) are licensed under the Lucide license. The full text of this license can be found in `src/view/base/web/svg/lucide/LUCIDEICONS_LICENSE.txt` and online at [https://lucide.dev/license](https://lucide.dev/license).
* **AlpineJS Dialog** library from Fylgja (https://github.com/fylgja/alpinejs-dialog) is distributed under the MIT license. The full text of this license can be found in `src/view/base/templates/page/js/plugins/HTMLDIALOG_LICENSE_MIT.txt` or online at [raw.githubusercontent.com/fylgja/alpinejs-dialog/refs/heads/main/LICENSE](https://raw.githubusercontent.com/fylgja/alpinejs-dialog/refs/heads/main/LICENSE)

## Changelog
Please see [The Changelog](CHANGELOG.md).

[ico-compatibility]: https://img.shields.io/badge/magento-%202.4-brightgreen.svg?logo=magento&longCache=true&style=flat-square
