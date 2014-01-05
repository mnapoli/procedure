# Procedure

[![Build Status](https://travis-ci.org/mnapoli/procedure.png?branch=master)](https://travis-ci.org/mnapoli/procedure) [![Coverage Status](https://coveralls.io/repos/mnapoli/procedure/badge.png)](https://coveralls.io/r/mnapoli/procedure)

Standard project layout:

```
app/
    Config/
        routing.yml
    Controller/
        home.php
    View/
        home.html.twig
web/
    index.php
```

Routing configuration example:

```yml
homepage:
    path:   /
    defaults:  { _controller: MyApp\Controller\home() }
```

Controller:

```php
<?php

namespace MyApp\Controller;

function home(Request $request, ResponseHelper $helper)
{
    return $helper->render('home.html.twig');
}
```

## Dependency injection in the controller

[PHP-DI](http://php-di.org/) is used for dependency injection in the function's parameters:

```php
function home(Request $request, MyService $service)
{
}
```

You can inject:

- the request by type-hinting the parameter with Symfony's request object (`Symfony\Component\HttpFoundation\Request`)
- a request parameter by naming your function parameter like the request parameter
- a service from the container, by type-hinting the parameter with the class name

## Getting started

This project is just an experiment, so this is not really serious stuff (for now at least).

To use it, require `"mnapoli/procedure"` in composer, and then here is an example of a front controller (`web/index.php`):

```php
// Create and configure the container (see PHP-DI's documentation)
$builder = new ContainerBuilder();
$container = $builder->build();

$request = Request::createFromGlobals();
$eventDispatcher = new EventDispatcher();

// Routing
$locator = new FileLocator(array(__DIR__ . '/../app/'));
$context = new RequestContext();
$context->fromRequest($request);
$router = new Router(
    new YamlFileLoader($locator),
    'routing.yml',
    array(),
    $context
);
$eventDispatcher->addSubscriber(new RouterListener());

// Configure the HttpKernel
$resolver = new FunctionControllerResolver($container, new PSR4FunctionLoader());
$kernel = new HttpKernel($eventDispatcher, $resolver);

// Handle the request
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

(this is just a mock up, not tested)
