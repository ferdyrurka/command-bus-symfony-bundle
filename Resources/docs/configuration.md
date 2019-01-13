# Configuration

```yaml
command_bus_symfony:

    # Name dirs
  
    # Default: Handler
    # Is required
    # Example 'Handlers' real path to search: 'src/Handlers'
    handler_name: 'your_name_folder_handler'  
    # Default: Command
    # Is required
    # Example 'Commands' real path to search: 'src/Commands'
    command_name: 'your_name_folder_command'
    
    # Do you want to save data from the execution of the trader
    # Is required
    # Save only all throws
      # Messages, name, path etc.
    save_statistic_handler: true
   
    # Required if save_statistic_handler: true
    # elasticsearch
    database_type: elasticsearch
   
    # Data to elasticsearch
    # Is required if save_statistic_handler: true and database_type: elasticsearch
    connection: 
        elasticSearch:
    
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
