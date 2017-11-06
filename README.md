# Magento 1 - Url Cache Control

Magento 1.x Extension

## Overview

This module appends the modified timestamp to skin url's, to automatically burst Browser Cache on changes.

## Requirements and setup

### Requirements

Tested with Magento 1.9.3.6

This extension can be installed using [Composer](https://getcomposer.org/doc/01-basic-usage.md)

### Setup

Simple add repository to your composer.json

````json
{
    "repositories": [
        {"type": "git", "url": "https://github.com/kirchbergerknorr/Magento1_UrlCacheControl.git"}
    ],
    "require": {
        "kirchbergerknorr/magento1_url-cache-control": "^1.*"
    }
}
````

## Details

This Module just add a unix-timestamp parameter to each skin-url which uses

````php
// skin
\Mage_Core_Model_Design_Package::getSkinUrl()

// merged css
\Mage_Core_Model_Design_Package::getMergedCssUrl()

// merged js
\Mage_Core_Model_Design_Package::getMergedJsUrl()
````

to get url's like that example:

````html
<!-- merged css -->
<link rel="stylesheet" type="text/css" href="http://my-domain.com/media/css/80155168870b61c9ca5c888e4b01857c.css?1509972745">

<!-- vendor include of jQuery -->
<script type="text/javascript" src="http://my-domain.com/skin/frontend/my-package/my-design/js/vendor/jquery.min.js?1478112303"></script>

<!-- It will append it to every url which is a file in skin folder. So it also works for images -->
<link rel="icon" href="http://my-domain.com/skin/frontend/my-package/my-design/favicon.ico?1478112292" type="image/x-icon">
````

By default there is no need to update `.htaccess`. 

### Configuration

enable Module

## Support

If you have any problems with this extension, please open an [issue](https://github.com/kirchbergerknorr/Magento1_UrlCacheControl/issues).

## Contribution

Any contribution is highly appreciated. The best way to contribute is to [open a pull request](https://help.github.com/articles/about-pull-requests/).

## Authors

Nick Dilssner [nd@kirchbergerknorr.de](mailto:nd@kirchbergerknorr.de)

## License

[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)