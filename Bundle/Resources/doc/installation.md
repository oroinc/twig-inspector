# Installation

**Note:** It is not recommended to install the bundle in the production environment.

## With Composer
1. Using composer, install the package as a `dev` dependency:
```bash
php composer require --dev "oro/twig-inspector:1.0.x-dev"
```

2. Enable the bundle in `AppKernel.php`
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

3. Add `twig_inspector` firewall to`security.yml`:
```yaml
# app/config/security.yml
security:
    firewalls:
        twig_inspector:
            pattern:   ^/_template/
            security: false
```
4. Import the routing to `routing_dev.yml`:
```yaml
# config/routing_dev.yml
oro_twig_inspector:
    resource: "@OroTwigInspectorBundle/Resources/config/oro/routing.yml"
```

5. (Optional) Update the [framework.ide](https://symfony.com/doc/current/reference/configuration/framework.html#ide) configuration, for example:
```yaml
# app/config/config_dev.yml
framework:
    ide: phpstorm # to open files in a PhpStorm IDE
```

6. Warm up the cache
```bash
php bin/console cache:warmup --env=dev
```
## Next Step
[How to Use Twig Inspector?](./usage.md)
