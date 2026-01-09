# Hyvä Themes - Theme Module

[![Hyvä Themes](https://hyva.io/media/wysiwyg/logo-compact.png)](https://hyva.io/)

## hyva-themes/magento2-theme-module

Core Hyvä Themes framework module for Magento 2, providing shared theme infrastructure used by Hyvä storefront themes.

![Supported Magento Versions][ico-compatibility]

## Requirements

Compatible with Magento 2.4.4-p9 and higher.

## Scope

This package provides the Hyvä theme framework module. It does not include a storefront theme; install `hyva-themes/magento2-default-theme` for the default frontend theme assets.
 
## Hyvä Theme Module Installation

The Hyvä Theme module is commonly installed as a dependency of the Hyvä default theme `hyva-themes/magento2-default-theme`.  
The following instructions describe how to install the Hyvä Theme module explicitly.

### Step 1: Configure composer repositories

The recommended way to install the Hyvä Theme module is with a Hyvä Private Packagist key. This will make related Hyvä packages available.
Alternatively, the module can be installed from GitHub.

Choose one of the following options:

#### Option 1: Installation from Hyvä Private Packagist (Recommended)

Get a free key by registering an account at [www.hyva.io](https://www.hyva.io) and creating one from your account dashboard.

You will receive instructions like the following after creating your Packagist key:

```sh
# this command adds your key to your project's auth.json file
# replace yourLicenseAuthenticationKey with your own key
composer config --auth http-basic.hyva-themes.repo.packagist.com token yourLicenseAuthenticationKey
# replace yourProjectName with your project name
composer config repositories.private-packagist composer https://hyva-themes.repo.packagist.com/yourProjectName/
```

#### Option 2: Installation from GitHub

To install the module directly from GitHub, configure the repository as a composer repository.

```sh
composer config repositories.hyva-themes/magento2-theme-module git https://github.com/hyva-themes/magento2-theme-module.git
composer config repositories.hyva-themes/magento2-mollie-theme-bundle git https://github.com/hyva-themes/magento2-mollie-theme-bundle.git
```

### Step 2: Install the module

Install the module and its dependencies with composer:

```sh
composer require hyva-themes/magento2-theme-module
```

### Step 3: Post-install actions

- After installing the module, run the Magento setup command:

  ```sh
  bin/magento setup:upgrade
  ```

## Support / Docs

- Documentation: [https://docs.hyva.io](https://docs.hyva.io)
- Account and downloads: [https://www.hyva.io](https://www.hyva.io)

## License

This package is licensed under the **Open Software License (OSL 3.0)**.

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
