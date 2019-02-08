# Query Handler

When you create handler, implements interface
Ferdyrurka\CommandBus\Query\Handler\QueryHandlerInterface

```php
namespace App\Query\Handler;

use Ferdyrurka\CommandBus\Query\Handler\QueryHandlerInterface;
use Ferdyrurka\CommandBus\Query\ViewObject\ViewObjectInterface;

class FindUserHandler implements QueryHandlerInterface
{
    public function handle(QueryCommandInterface $command): ViewObjectInterface
    {
        // Your logic business
    }
}
```