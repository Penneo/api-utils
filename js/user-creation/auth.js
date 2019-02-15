var request     = require('request-promise');
var Wsse        = require('wsse');
var credentials = require('./credentials.json');

function checkInit(cred) {
    if (!cred || !cred.key || !cred.secret) {
        throw new Error('API not initialized. Make sure that the cred file exists and has key, secret, and endpoint');
    }
}

function generateAuthHeader(cred) {
    checkInit(credentials);
    var token = new Wsse({
        username: cred.key,
        password: cred.secret
    });
    return {
        'X-WSSE': 'UsernameToken ' + token.getWSSEHeader({nonceBase64: true})
    };
};

// Get information about the logged in user
function authenticate(endpoint) {
    if  (credentials.jwt) {
        return credentials.jwt;
    }
    return request({
        method: 'GET',
        baseUrl: endpoint,
        // url: 'user',
        url: '/auth/token',
        headers: generateAuthHeader(credentials)
    }).then(function(response) {
        return JSON.parse(response);
    });
}

module.exports = {
    authenticate: authenticate
}
