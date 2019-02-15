# Creating Penneo Users in Bulk

## Run

```
node main.js \
     --customer-id CUSTOMER_ID \
     --uri 'https://sandbox.penneo.com' \
     --csv-file '/tmp/user-emails-names.csv';
```

Optionally, you can add `--allowed-credentials bankid_se,classic` and `--rights send,validation`. Both are comma separated lists.

## Authentication

You can either use api credentials or a JWT token (if you already have one). If the `jwt` is in the config file, then it will be used, otherwise the `key` and the `secret` will be used.

Instructions on generating an authentication token can be found here: [Generate an authentication token using classic credentials][js-authentication]

## CSV file with User emails and names

The `csv-file` option takes a file that has `email` as the first column and
`name` as the second column:

```
john@doe.com,"John Doe"
jane@acme.com,"Jane Doe"
```

[js-authentication]: https://github.com/penneo/api-utils/tree/master/js/authentication
