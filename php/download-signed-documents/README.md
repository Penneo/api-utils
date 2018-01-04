# Download signed documents

## Resolve dependencies

```
composer install
```

## Run

`run.php` contains a script that demonstrates how to download signed documents for a case file. You can run is as follows:

```
php run.php \
    --endpoint='https://sandbox.penneo.com/api/v1/' \
    --key='key' \
    --secret='secret' \
    --case-file-id=1234 \
    ;
```

**Note:** Please note that passing credentials on the command line is not recommended. Modify the script according to your needs
