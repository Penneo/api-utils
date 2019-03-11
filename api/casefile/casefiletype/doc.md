<!-- markdown-toc start - Don't edit this section. Run M-x markdown-toc-generate-toc again -->
**Table of Contents**

- [Example for creating a Signing Request using the REST API](#example-for-creating-a-signing-request-using-the-rest-api)
    - [Create case file](#create-case-file)
    - [Create document](#create-document)
    - [Create Signer](#create-signer)
    - [Map the document and signer with Signature Lines](#map-the-document-and-signer-with-signature-lines)
    - [[Optional] Send the signing request using Penneo](#optional-send-the-signing-request-using-penneo)
    - [Activate the case file](#activate-the-case-file)

<!-- markdown-toc end -->

# Example for creating a Signing Request using the REST API

Here is a list of calls that need to be made to the server. Please add the appropriate headers e.g:

```
GET _endpoint_
X-WSSE: _auth_token_
Accept-charset: utf-8
Accept: application/json
Content-Type: application/json
{
    _payload_
}
```

See details on [generating the authentication header using WSSE][doc-auth-wsse]
to add the correct value for the header.

## Create case file

You can get all the case file types (and their ids) from this endpoint:
https://app.penneo.com/api/docs#get--api-v1-casefile-casefiletypes and use the
appropriate id in the following endpoint:

```
POST /casefiles
{
  "title": "ABC Test",
  "caseFileTypeId": CASE_FILE_TYPE_ID
}
```

Case file id: `1001`

## Create document

You can get all the document types (and their ids) from this endpoint:
https://app.penneo.com/api/docs#get--api-v1-casefiles-{caseFileId}-documenttypes
and use the appropriate id in the following endpoint:

```
POST /documents
{
  "pdfFile": "base64encoded file",
  "documentTypeId": DOCUMENT_TYPE_ID,
  "title": "Demo Document",
  "caseFileId": 1001
}
```

Document id: 2001

## Create Signer
```
POST /casefiles/1001/signers
{
  "name": "Jane Andersen"
}
```

Signer id: 3001

## Set signer type (role)
You can get all the signer types (and their ids) from this endpoint:
https://app.penneo.com/api/docs#get--api-v1-casefiles-{caseFileId}-signers-{signerId}-signertypes
and use the appropriate id in the following endpoint:

```
POST /casefiles/1001/signers/3001/signertypes
{
  "signerTypeId": SIGNER_TYPE_ID
}
```

## Activate the case file
```
PATCH /casefiles/1001/send
```

[doc-auth-wsse]: https://github.com/penneo/api-utils/tree/master/doc/auth.md
