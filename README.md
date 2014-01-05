# Procedure

Standard project layout:

```
app/
    Config/
        routing.yml
    Controller/
        home.php
    View/
        home.html.twig
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
