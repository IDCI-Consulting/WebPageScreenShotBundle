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

Include a resource in your `config.yml`

```yml
imports:
    ....
    - { resource: @IDCIWebPageScreenShotBundle/Resources/config/config.yml }
```

Add a controller in your routing.yml file.
```yml
idci_web_page_screen_shot:
    resource: "../../vendor/idci/webpagescreenshot-bundle/IDCI/Bundle/WebPageScreenShotBundle/Controller"
    type:     annotation
```

You must specify some default values in your `parameters.yml` files. Here is an example.

```yml
parameters:
    ...
    screenshot_phantomjs_bin_path: "/usr/bin/phantomjs"
    screenshot_width: 800
    screenshot_height: 600
    screenshot_mode: file
    screenshot_format: png
    screenshot_cache_enabled: true
    screenshot_cache_delay: 26000
```

  * screenshot_phantomjs_bin_path refers to the phantomjs executable path.

You may find it with the command

```sh
whereis phantomjs
```

  Then, You can specify a **width**, a **height**, a render **mode** and a render **format**. Two modes are available : **file** and **base64**. Formats include **png**, **jpg** and **gif**.
  * **enabled** cache parameter specify whether or not you want to put images in cache. The **delay** parameter refers to the TTL (time to live) of images.

Usage
=====

You can create screenshots in 2 ways. In both cases, screenshots will be stored in /web/screenshots_cache.

### Create a screenshot with a command

A command allows you to create screenshots. It's as simple as this: 
```sh
php app/console idci:create:screenshot [url] [width] [height] [mode] [format]
```

For instance, a working command would give :
```sh
php app/console idci:create:screenshot http://symfony.com 800 600 file jpg
```

In case you chose base64 render mode, the base64 encoded string is output on the console.

If you don't indicate any parameters except the url, a prompt will suggest default configuration values (those in your parameters.yml file). You can press enter to accept them, or change them if you wish.

### Using the service in controllers

A controller already exists. You might want to check it out [here](https://github.com/IDCI-Consulting/WebPageScreenShotBundle/blob/master/Controller/FrontController.php "front-controller").

This controller handle a request, and return the generated image as a response.
The request should look like **http://mysymfonyapp/screenshot?url=http://mywebsite.com&format=jpg**

But you might want to do something else.
The Screenshot Manager is accessible via a service called idci_web_page_screen_shot.manager. So you can do in your controllers:

```php
  $screenshot = $this->get('idci_web_page_screen_shot.manager')->createScreenshot($url, $params);
```

$params is an array containing parameters to overload values in parameters.yml. In the existing controller, it's build with the parameters of the request.
It should look like something like that:

```php
  $params = array(
      "mode" => "base64",
      "width" => 1024,
      "file" => "gif"
  );
```








