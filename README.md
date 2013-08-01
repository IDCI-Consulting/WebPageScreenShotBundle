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
    prefix:   /screenshot
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
    screenshot_cache_delay: 86400
    screenshot_cache_directory:       %kernel.cache_dir%/screenshot/cache/
```

screenshot_phantomjs_bin_path refers to the phantomjs executable path.

You may find it with the command

```sh
whereis phantomjs
```

Then, You can specify a **width**, a **height**, a render **mode** and a render **format**. Three modes are available : **file**, **url** and **base64**. Formats include **png**, **jpg** and **gif**.
The **enabled** cache parameter specify whether or not you want to put images in cache. The **delay** parameter refers to the TTL (time to live) of images in **seconds**.

Usage
=====

You can create screenshots in 2 ways. In both cases, screenshots will be stored in the directory specified in the parameters.yml file. The maximum resolution of screenshots is 1440*900.

### Create a screenshot with a command

A command allows you to create screenshots. It's as simple as this: 
```sh
php app/console idci:create:screenshot [url] [width] [height] [mode] [format]
```

For instance, a working command would give :
```sh
php app/console idci:create:screenshot http://symfony.com 800 600 file jpg
```

If you don't indicate any parameters except the url, a prompt will suggest default configuration values (those in your parameters.yml file). You can press enter to accept them, or change them if you wish.

### Using the service in controllers

A controller already exists. You might want to check it out [here](https://github.com/IDCI-Consulting/WebPageScreenShotBundle/blob/master/Controller/ApiController.php "api-controller").

There are 2 actions availables.

The first one handle a request, and return the generated image as a response.  
The request should look like **http://mysymfonyapp/screenshot/capture?url=http://mywebsite.com&format=jpg&mode=url**  
The url mode is used to retrieve an url matching the second action.    

The second one simply retrieve an already generated screenshot.  
The request should look like **http://mysymfonyapp/screenshot/get/800x600_website.com.png**    

You might want to do something else. The Screenshot Manager is accessible via a service called idci_web_page_screen_shot.manager. So you can do in your controllers:

```php
$renderer = $screenshotManager
    ->capture($request->query->all())
    ->resizeImage()
    ->getRenderer()
;
```

The renderer take care of rendering the screenshot, according to the chosen mode. To retrieve the content of the screenshot, use the render function.
```php
$screenshot = $renderer->render();
```
Depending on the mode, it can be either a url, or a file, or a base64 encoded string.

The **createScreenshot** function return either the relative path of the image from the web directory, or a base64 encoded string.

**$params** is an array containing parameters. In the existing controller, it's build with the parameters of the request.
Whatever you do, it should look like something like that:

```php
$params = array(
    "url" => "http://mywebsite.com",
    "mode" => "base64",
    "width" => 1024,
    "file" => "gif"
);
```

Only the url is required in this array. Other parameters will overload the values of the parameters.yml file.