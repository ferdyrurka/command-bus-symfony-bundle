# Query Command

When you create query command, implements interface 
Ferdyrurka\CommandBus\Query\Command\QueryCommandInterface

```php
namespace App\Command;

use Ferdyrurka\CommandBus\Query\Command\QueryCommandInterface;

class FindUserCommand implements QueryCommandInterface
{
    public function __construct()
    {
        //Your variables and arguments  
    }
}
```