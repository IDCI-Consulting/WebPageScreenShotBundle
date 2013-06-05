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
