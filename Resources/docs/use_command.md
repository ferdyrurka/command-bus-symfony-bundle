# Use Command

When you create command, implements interface Ferdyrurka\CommandBus\Command\CommandInterface

```php
namespace App\Command;

use Ferdyrurka\CommandBus\Command\CommandInterface;

class CreateUserCommand implements CommandInterface
{
    public function __construct()
    {
        //Your variables and arguments  
    }
}
```