# QueryBus

Using by Ferdyrurka\CommandBus\QueryBus

```php
namespace App\Controller;

use Ferdyrurka\CommandBus\QueryBusInterface;

class HomeController
{
    public function checkExistUserAction(QueryBusInterface $queryBus): array 
    {
        $command = new FindUserCommand(13);
        $viewObject = $queryBus->handle($command);
        
        return ['data_user' => $viewObject];
    }
}
```