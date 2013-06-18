WebPageScreenShotBundle
=======================

Bundle used to generate web page screenshots

Installation
===========

To install this bundle please follow the next steps:

First add the dependency in your `composer.json` file:

```json
"require": {
    ...
    "idci/webpagescreenshot-bundle": "dev-master"
},
```

Then install the bundle with the command:

```sh
php composer update
```

Enable the bundle in your application kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new IDCI\Bundle\WebPageScreenShotBundle\IDCIWebPageScreenShotBundle(),
    );
}
```

Now the Bundle is installed.

TODO
====

To test with all image types
Add lip imagine bundle to resize png according to the conf parameters
Images should be cached

Example: how to retrieve and decode a png in php
```php
    $curl = curl_init(http://myScreenshotServerAddress/screenshot?url=http://www.test.com);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $bin_file = base64_decode(curl_exec($curl));
    file_put_contents("test.png", $png_bin);
```
