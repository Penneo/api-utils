# Creating Penneo Users in Bulk

## Run

```
node main.js --token-file ~/.penneo-auth-token-sandbox \
     --customer-id CUSTOMER_ID \
     --uri 'https://sandbox.penneo.com' \
     --csv-file '/tmp/user-emails-names.csv';
```

## Authentication

The authentication token should be stored in a file and the `token-file` switch
has to be used to use the token. At this point in time, we don't have long
living tokens so this script can only be used within 3 hrs of creation of an
authentication token. 

**todo** write more about how to generate an authentication token..

## CSV file with User emails and names

The `csv-file` option takes a file that has `email` as the first column and
`name` as the second column:

```
john@doe.com,"John Doe"
jane@acme.com,"Jane Doe"
```
