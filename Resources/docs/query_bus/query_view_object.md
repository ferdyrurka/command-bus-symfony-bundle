# Query ViewObject

When you create ViewObject, implements interface
Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface

```php
<?php

namespace App\Query\ViewObject;

use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

class FindUserViewObject implements ViewObjectInterface 
{
    private $a;
    
    public function __construct(int $a) 
    {
        $this->a = $a;
    }  
    
    public  function getA(): int
    {
       return $this->a;
    }
}
```