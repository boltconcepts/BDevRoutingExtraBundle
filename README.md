BDevRoutingExtraBundle
=============

The `BDevRoutingExtraBundle` is a easy way to add information about routes to your Symfony2 application.
This for example can avoid having to override a title block in each template within your application by using `{{ route_extra('title') }}`.
You can also use it to generate a breadcrumb using '{{ route_breadcrumb() }}' or add an item to a menu.

[![Build Status](https://travis-ci.org/boltconcepts/BDevRoutingExtraBundle.png?branch=master)](https://travis-ci.org/boltconcepts/BDevRoutingExtraBundle)

Installation
=============

Step 1) Get the bundle
-------------

### Composer
```
"require" :  {
    // ...
    "bdev/bdev-routing-extra-bundle":"dev-master",
}
```

Step 2) Register the bundle
-------------
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new BDev\Bundle\RoutingExtraBundle\BDevRoutingExtraBundle(),
    );
    // ...
}
```

Step 2) Configure the bundle
-------------
```yaml
# app/config/config.yml
bdev_routing_extra:
    resource: %kernel.root_dir%/config/routing.ext.yml #
    twig: true # use "twig: false" to disable the Twig extension
    breadcrumb: # enable the breadcrumb twig extension (for this twig must be enabled)
        label_attr: [breadcrumb, title] # This a a array that references to the options set per route
    knp_menu: # set knp menu bundle extension
      menus: # define the menu's and there specific options
        name:
          children_attributes:
            class: 'mymain_menu'
```

```yaml
# app/config/routing.ext.yml
myBundle: # import route extra's
    resource: "@MyBundle/Resources/config/routing.ext.yml"

home: # the route name and it's options
  title: Home
  summary: Welcome home
  menu: # add the route to a menu
    main:
      attributes:
        class: home
    footer: ~

profile:
  parent: home # the parent route name
  title: Profile
  summary: Your information
  menu:
    footer: ~
```


Todo
=============

- Improve usage documentation
- Use context configuration to only cache what is needed
- Add i18n support
- Add a way to order KnpMenu items
- Add unit tests for KnpMenu code
- Improve Breadcrumb by using a library or extend current code to support multiple renders?
- Move KnpMenu code to a separate bundle?
- Add security extension?
