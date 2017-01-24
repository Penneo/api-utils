# Download signed documnets

## Resolve dependencies

```
composer install
```

## Run

`run.php` contains a script that demonstrates how to download signed documents for a case file. You can run is as follows:

```
php run.php \
    'https://sandbox.penneo.com/api/v1/' \
    'key' \
    'secret' \
    1234       # case file id
    ;
```

**Note:** Please note that passing credentials on the command line is not recommended. Modify the script according to your needs
