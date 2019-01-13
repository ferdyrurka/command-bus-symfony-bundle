# Require by Composer

This package doesn't including by packagist.org

In composer.json add:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/ferdyrurka/command-bus-symfony-bundle"
        }
    ],
    "require": {
        "ferdyrurka/command-bus-symfony-bundle": "0.0.1-alpha"
    }
}
```