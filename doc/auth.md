# Authentication with the API

Here are the different ways that can be used to authenticate using the API:

1. Classic credentials (limited validity)
2. WSSE

## WSSE Authentication

### Getting the credentials ###

You can get your integration credentials from your profile in Penneo. Once you have them, you have to generate a new authentication headers for every API request that you make to the Penneo server.

### Generating the authentication header ###

#### Structure ####

```
X-WSSE: UsernameToken Username="_your_key_", PasswordDigest="_digest_", Nonce="_nonce_", Created="2015-10-19T10:22:35Z"
```

#### Calculating the digest ####

```
$digest = base64_encode(sha1(base64_decode($nonce) . $timestamp . $secret, true));
```

#### Generating the nonce

The nonce can be any random string with the following requirements:

- It should be unique for every request 
- It needs to be a string not longer than 64 chars

### Helper utilities ###

You can have a look at this utility (first one I could find online) that helps generate the wsse header:

http://www.teria.com/~koseki/tools/wssegen/

(Keep in mind that the nonce is base64 encoded in the above utility)

### Reference implementation ###

As an example, please have a look how we add the authentication headers when creating a request:

1. [Generating headers in .NET][.net]
2. [Generating headers in PHP][php]

[.net]: https://github.com/Penneo/sdk-net/blob/master/Src/Penneo/Connector/WSSEAuthenticator.cs#L49
[php]: https://github.com/davedevelopment/guzzle-wsse-auth-plugin/blob/master/src/Atst/Guzzle/Http/Plugin/WsseAuthPlugin.php#L80
