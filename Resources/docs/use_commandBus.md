# Use CommandBus

Using by Ferdyrurka\CommandBus\CommandBus

```php

namespace App\Controller;

use Ferdyrurka\CommandBus\CommandBusInterface;

class HomeController
{
    public function indexAction(CommandBusInterface $commandBus): void 
    {
        $command = new CreateUserCommand();
        $commandBus->handle($command);
    }
}
``` 