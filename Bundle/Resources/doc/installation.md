# Installation

**Note:** It is not recommended to install the bundle in the production environment.

## With Composer
1. Using composer, install the package as a `dev` dependency:
```bash
composer require --dev "oro/twig-inspector:~1.1.0"
```

2. Enable the bundle in the `config/bundles.php` file:
```php
# config/bundles.php
<?php

return [
    # ...
    Oro\TwigInspector\Bundle\OroTwigInspectorBundle::class => ['dev' => true]
];
```

3. Add `twig_inspector` firewall to the `security.yaml` file:
```yaml
# config/packages/security.yaml
security:
    firewalls:
        twig_inspector:
            pattern:   ^/_template/
            security: false
```
4. Create the `twig_inspector.yaml` routing file in the `config/routes/dev`:
```yaml
# config/routes/dev/twig_inspector.yaml
oro_twig_inspector:
    resource: "@OroTwigInspectorBundle/Resources/config/oro/routing.yml"
```

5. (Optional) Update the [framework.ide](https://symfony.com/doc/current/reference/configuration/framework.html#ide) configuration, for example:
```yaml
# config/packages/framework.yaml
framework:
    ide: phpstorm # to open files in a PhpStorm IDE
```

6. Warm up the cache
```bash
php bin/console cache:warmup --env=dev
```
## Next Step
[How to Use Twig Inspector?](./usage.md)
