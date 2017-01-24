# Download signed documnets

## Resolve dependencies

```
composer install
```

## Run

`run.php` contains a script that demonstrates how to download signed documents. You can run is as follows:

```
php run.php \
    'https://sandbox.penneo.com/api/v1/' \
    'key' \
    'secret' \
    ;
```

**Note:** Please note that passing credentials on the command line is not recommended. Modify the script according to your needs
