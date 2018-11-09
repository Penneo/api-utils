# Penneo Utilities

Do stuff with Penneo's public API

## Authentication
### WSSE
- [Documentation][doc-auth-wsse]
- [Python][py-auth-wsse]
- [C#][cs-1-auth-wsse]
- [Node JS][js-auth-wsse]
### Classic (Username/Password)
- [Node JS][js-auth-classic]
- [Python][py-auth-classic]


## Case Files
### Filter case files
- [C#][cs-1-filter-case-files]
- [PHP][php-filter-case-files]
### Create Case Files in Bulk
- [PHP][php-bulk-create-case-files]
### Create Case File with a Template
- [PHP][php-case-file-template]
### Signing request link
#### One Document One Signer
- [PHP][php-casefile-one-doc-one-signer]
- [C#][cs-1-casefile-one-doc-one-signer]
- [API][api-casefile-one-doc-one-signer]
#### One Document Two Signers
- [C#][cs-1-casefile-one-doc-two-signers]

## Case File Types
- [C#][cs-1-casefiletype]

## Validations
- [C#][cs-1-create-validation]

## Folders
### Linking folders
- [C#][cs-1-folder-link]

## Archiving
### Download signed documents ###
- [PHP][php-download-signed-documents]
- [C#][cs-1-download-signed-documents]

## Users
### Create Penneo users in bulk
- [Node JS][js-user-creation]

### Fetch Users for a Customer
- [C#][cs-1-user-customer-users]

## Logging
- [PHP][php-enable-logging]

## Email Templates
- [C#][cs-1-email-templates]
- [PHP][php-email-templates]

## Integration with Azure AD
- [Azure AD][doc-azuread]

<!-- Authentication -->
[js-auth-classic]: js/auth/classic
[js-auth-wsse]: js/auth/wsse
[py-auth-wsse]: py/auth/wsse
[py-auth-classic]: py/auth/classic
[cs-1-auth-wsse]: cs/1.x/auth/wsse

<!-- Case files -->
[cs-1-filter-case-files]: cs/1.x/filter-case-files
[php-filter-case-files]: php/filter-case-files
[php-bulk-create-case-files]: php/bulk-case-file-creation
[php-case-file-template]: php/casefile/case-file-template
[php-casefile-one-doc-one-signer]: php/casefile/one-doc-one-signer
[cs-1-casefile-one-doc-one-signer]: cs/1.x/casefile/one-doc-one-signer
[cs-1-casefile-one-doc-two-signers]: cs/1.x/casefile/one-doc-two-signers
[api-casefile-one-doc-one-signer]: api/casefile/one-doc-one-signer/doc.md

<!-- Case file types -->
[cs-1-casefiletype]: cs/1.x/casefiletype

<!-- Validations -->
[cs-1-create-validation]: cs/1.x/create-validation

<!-- Folders -->
[cs-1-folder-link]: cs/1.x/folder-link

<!-- Archiving -->
[php-download-signed-documents]: php/download-signed-documents
[cs-1-download-signed-documents]: cs/1.x/download-signed-documents

<!-- Users -->
[js-user-creation]: js/user-creation
[cs-1-user-customer-users]: cs/1.x/customer-users

<!-- Logging -->
[php-enable-logging]: php/enable-logging

<!-- Email templates -->
[cs-1-email-templates]: cs/1.x/email-templates
[php-email-templates]: php/email-templates

<!-- documenation -->
[doc-auth-wsse]: doc/auth.md
[doc-azuread]: doc/azuread/azuread.md
