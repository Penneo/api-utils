# Create a Case File using a Template

When creating case files with templates, there is not need to create signature lines (which map the signer with the document explicitly). A case file template keeps a mapping of document types with signer types, so instead of creating a signature line, document types need to be set for documents and signer types need to be set for signers.

## Run

```
php run.php \
    --endpoint="https://sandbox.penneo.com/api/v1/" \
    --key=key \
    --secret=secret \
    --file=document.pdf
```
