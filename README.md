WebPageScreenShotBundle
=======================

Bundle used to generate web page screenshots

Installation
============

To install this bundle please follow the next steps:

### Step 1: Download the WebPageScreenShotBundle

First add the dependency in your `composer.json` file:

```json
"require": {
    ...
    "idci/webpagescreenshot-bundle": "dev-master",
    "gregwar/image-bundle": "dev-master"
},
```

Then install the bundles with the command:

```sh
php composer.phar update
```

### Step 2: Enable the bundle

Register the bundles in your `app/AppKernel.php`:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new IDCI\Bundle\WebPageScreenShotBundle\IDCIWebPageScreenShotBundle(),
        new Gregwar\ImageBundle\GregwarImageBundle(),
    );
}
```

### Step 3: Install phantomjs

This bundle uses [phantomjs](http://phantomjs.org/ "phantomjs") to generate website screenshots. You can install it on linux via `apt-get`.

```sh
sudo apt-get install phantomjs
```

Now the Bundle is installed.

### Step 4: Configure the bundles and set up the directories

You must specify the default configuration in `app/config/config.yml`:

```yml
idci_web_page_screen_shot:
    phantomjs_bin_path: "/usr/bin/phantomjs"
    render:
        width: 160
        height: 144
        mode: file
        format: png
    cache:
        enabled: true
        delay: 19600
```

  * phantomjs_bin_path refers to the phantomjs executable path.

You may find it with the command

```sh
whereis phantomjs
```

  * render parameters are used to output a screenshot. You can specify a **width**, a **height**, a render **mode** and a render **format**. Two modes are available : **file** and **base64**. Formats include **png**, **jpg** and **gif**.
  * **enabled** cache parameters specify whether or not you want to put images in cache. The **delay** parameter refers to the TTL (time to live) of images.

Usage
=====

### Generating a screenshot with a command

### Generating screenshots in controllers

TODO





