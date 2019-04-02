<!-- markdown-toc start - Don't edit this section. Run M-x markdown-toc-refresh-toc -->
**Table of Contents**

- [Example for creating a Signing Request using the REST API](#example-for-creating-a-signing-request-using-the-rest-api)
    - [Create case file](#create-case-file)
    - [Create document](#create-document)
    - [Create Signer](#create-signer)
    - [Map the document and signer with Signature Lines](#map-the-document-and-signer-with-signature-lines)
    - [Send the signing request](#send-the-signing-request)
        - [- Either: Send the signing request using Penneo](#--either-send-the-signing-request-using-penneo)
        - [- Or: Distribute the signing request link yourself](#--or-distribute-the-signing-request-link-yourself)
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

## Send the signing request

Get the signing request
```
GET /casefiles/1001/signers/3001/signingrequests
```

Signing request : 3001

Now, you have two options:

### - Either: Send the signing request using Penneo


Update the email details:
```
PUT /signingrequests/3001
{
  "email": "jane@acme.com",
  "emailSubject": "You contract is ready to be signed",
  "emailText": "Dear {{recipient.name}}, Please sign the contract using the link: {{link}}. From {{sender.name}}"
}
```

### - Or: Distribute the signing request link yourself

Extract the signing request links:

```
GET /signingrequests/3001/link
```

Once you have the link, you can distribute it yourself. Just make sure that the
case file is active (see below) when the recipient receives the links.

## Activate the case file
```
PATCH /casefiles/1001/send
```

[doc-auth-wsse]: https://github.com/penneo/api-utils/tree/master/doc/auth.md
