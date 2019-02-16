# Configuration

Added in bundles.php this class:

```php
    Ferdyrurka\CommandBus\CommandBusSymfonyBundle::class => ['all' => true],
```

Create file in config/packages

command_bus_symfony.yaml

```yaml
command_bus_symfony:

    #### CommandBus
    
        # Default: Handler
        # Is required
    handler_prefix: 'your_handler_prefix'
      
        # Default: Command
        # Is required
    command_prefix: 'your_command_prefix'
    
        # Default: Command
        # Is required
    query_prefix: 'your_query_prefix'
  
        # Default: Command
        # Is required
    query_handler_prefix: 'your_query_handler_prefix'
  
    # Do you want to save data from the execution of the trader
    
    # Save only all throws
        # CommandBus
        # Default: true
        # Is required
    save_command_bus_log: true
        # QueryBus
        # Default: true
        # Is required
    save_query_bus_log: true
   
    # Required if save_statistic_handler: true
    # elasticsearch
    database_type: elasticsearch
   
    # Data to elasticsearch
    # Is required if save_statistic_handler: true and database_type: elasticsearch
    connection: 
        elasticsearch:
    
            # Default: elasticsearch 
            host: 'example.com'
            # Default: 9200
            port: 9200
            # Default: https 
            scheme: 'https'
            
            index: 'your_index'
            
            # Not required, default is not auth
            # Not required, default is not auth
            
            # Default: null
            user: 'username'
            # Default: null
            pass: 'password'
```
