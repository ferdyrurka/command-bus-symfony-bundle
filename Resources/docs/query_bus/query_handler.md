# Query Handler

When you create handler, implements interface
Ferdyrurka\CommandBus\Query\Handler\QueryHandlerInterface

```php
<?php

namespace App\Query\Handler;

use Ferdyrurka\CommandBus\Query\Handler\QueryInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

class FindUserHandler implements QueryInterface
{
    public function __construct(int $a) 
    {
        // Your logic business
    }

    public function handle(): ViewObjectInterface
    {
        // Your logic business
    }
}
```