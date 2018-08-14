# Installation

**Note:** It's highly not recommended to install the bundle to the production environment.

## With Composer
1. Require with composer as a `dev` dependanty
```bash
php composer require --dev "oro/twig-inspector:1.0.x-dev"
```

2. Enable the bundle in the `AppKernel.php`
```php
<?php
// src/AppKernel.php

public function registerBundles()
{   
    if ('dev' === $this->getEnvironment()) {
      //...
      $bundles[] = new Oro\TwigInspector\Bundle\OroTwigInspectorBundle();
      //...
    }
}
```

3. Update the application configuration:
```yaml
# config/config_dev.yml
framework:
    ide: phpstorm # to open files in a PhpStorm IDE

# Uncomment if you want to change the base directory where from files can be opened. 
# Useful if you use some files from the outside of project root
# oro_twig_inspector:
#     base_dir: "%kernel.project_dir%/"

security:
    firewalls:
        twig_inspector:
            pattern:   ^/_template/
            security: false
```

4. import the routing to `routing_dev.yml`
```yaml
# config/routing_dev.yml
oro_twig_inspector:
    resource: "@OroTwigInspectorBundle/Resources/config/oro/routing.yml"
```
5. Warm up the cache
```bash
php bin/console cache:warmup --env=dev
```