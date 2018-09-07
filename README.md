# Penneo Utilities

Do stuff with Penneo's public API

## Authentication
### WSSE
- [Documentation][doc-auth-wsse]
- [Python][py-auth-wsse]
- [C#][cs-auth-wsse]
### Classic (Username/Password)
- [Node JS][js-auth-classic]
- [Python][py-auth-classic]


## Case Files
### Filter case files
- [C#][cs-filter-case-files]
- [PHP][php-filter-case-files]
### Create Case Files in Bulk
- [PHP][php-bulk-create-case-files]
### Create Case File with a Template
- [PHP][php-case-file-template]
### Signing request link
#### One Document One Signer
- [PHP][php-casefile-one-doc-one-signer]
- [C#][cs-casefile-one-doc-one-signer]
- [API][api-casefile-one-doc-one-signer]
#### One Document Two Signers
- [C#][cs-casefile-one-doc-two-signers]

## Case File Types
- [C#][cs-casefiletype]

## Validations
- [C#][cs-create-validation]

## Folders
### Linking folders
- [C#][cs-folder-link]

## Archiving
### Download signed documents ###
- [PHP][php-download-signed-documents]
- [C#][cs-download-signed-documents]

## Users
### Create Penneo users in bulk
- [Node JS][js-user-creation]

### Fetch Users for a Customer
- [C#][cs-user-customer-users]

## Logging
- [PHP][php-enable-logging]

## Email Templates
- [C#][cs-email-templates]
- [PHP][php-email-templates]

## Integration with Azure AD
- [Azure AD][doc-azuread]

<!-- Authentication -->
[js-auth-classic]: js/auth/classic
[py-auth-wsse]: py/auth/wsse
[py-auth-classic]: py/auth/classic
[cs-auth-wsse]: cs/auth/wsse

<!-- Case files -->
[cs-filter-case-files]: cs/filter-case-files
[php-filter-case-files]: php/filter-case-files
[php-bulk-create-case-files]: php/bulk-case-file-creation
[php-case-file-template]: php/casefile/case-file-template
[php-casefile-one-doc-one-signer]: php/casefile/one-doc-one-signer
[cs-casefile-one-doc-one-signer]: cs/casefile/one-doc-one-signer
[cs-casefile-one-doc-two-signers]: cs/casefile/one-doc-two-signers
[api-casefile-one-doc-one-signer]: api/casefile/one-doc-one-signer/doc.md

<!-- Case file types -->
[cs-casefiletype]: cs/casefiletype

<!-- Validations -->
[cs-create-validation]: cs/create-validation

<!-- Folders -->
[cs-folder-link]: cs/folder-link

<!-- Archiving -->
[php-download-signed-documents]: php/download-signed-documents
[cs-download-signed-documents]: cs/download-signed-documents

<!-- Users -->
[js-user-creation]: js/user-creation
[cs-user-customer-users]: cs/customer-users

<!-- Logging -->
[php-enable-logging]: php/enable-logging

<!-- Email templates -->
[cs-email-templates]: cs/email-templates
[php-email-templates]: php/email-templates

<!-- documenation -->
[doc-auth-wsse]: doc/auth.md
[doc-azuread]: doc/azuread/azuread.md
