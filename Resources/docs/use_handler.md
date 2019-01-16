# Use Handler

When you create handler, implements interface
Ferdyrurka\CommandBus\Handler\HandlerInterface

```php
namespace App\Handler;

use Ferdyrurka\CommandBus\Handler\HandlerInterface;

class CreateUserHandler implements HandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        // Your logic business
    }
}
```