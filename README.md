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
