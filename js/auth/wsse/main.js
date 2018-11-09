var request = require('request-promise');
var Wsse    = require('wsse');
var config  = require('./config.json');

function checkInit(config) {
    if (!config || !config.endpoint || !config.key || !config.secret) {
        throw new Error('API not initialized. Make sure that the config file exists and has key, secret, and endpoint');
    }
}

function generateAuthHeader(config) {
    checkInit(config);
    var token = new Wsse({
        username: config.key,
        password: config.secret
    });
    return {
        'X-WSSE': 'UsernameToken ' + token.getWSSEHeader({nonceBase64: true})
    };
};

// Get information about the logged in user
request({
    method: 'GET',
    baseUrl: config.endpoint,
    url: '/user',
    headers: generateAuthHeader(config)
}).then(function(response) {
    console.log(response);
});
