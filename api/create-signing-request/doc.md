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

```
POST /casefiles
{
  "sensitiveData": false,
  "documentDisplayMode": 0,
  "visibilityMode": 0,
  "title": "ABC Test"
}
```

Case file id: `1001`

## Create document
```
POST /documents
{
  "pdfFile": "base64encoded file",
  "type": "signable",
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

## Map the document and signer with Signature Lines
```
POST /documents/2001/signaturelines
{
  "signOrder": 0,
  "role": "MySignerRole"
}
```

Signature line id: 4001

```
LINK /documents/2001/signaturelines/4001/signers/3001
```

## [Optional] Send the signing request using Penneo

Get the signing request
```
GET /casefiles/1001/signers/3001/signingrequests

```
Signing request : 3001

Update the email details:
```
PUT /signingrequests/3001
{
  "emailText": "Email Text",
  "email": "dummy@dummy.com"
}
```

## Activate the case file
```
PATCH /casefiles/1001/send
```

[doc-auth-wsse]: https://github.com/penneo/api-utils/tree/master/doc/auth.md
