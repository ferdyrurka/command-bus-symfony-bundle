# QueryBus

Using by Ferdyrurka\CommandBus\QueryBus

```php
namespace App\Controller;

use Ferdyrurka\CommandBus\QueryBusInterface;

class HomeController
{
    public function checkExistUserAction(QueryBusInterface $queryBus): array 
    {
        $handler = new FindUserHandler(13);
        $viewObject = $queryBus->handle($handler);
        
        return ['data_user' => $viewObject];
    }
}
```