var Penneo    = require('penneo-js-sdk');
var read      = require('read-file');
var babyparse = require('babyparse');
var auth      = require('./auth');
var args      = require('optimist')
    .usage('Bulk creation of Penneo users.')
    .demand('uri')
    .demand('customer-id')
    .demand('csv-file')
    // .default('allowed-credentials', 'nemid,classic,api')
    .default('allowed-credentials', 'bankid_no,classic')
    .default('rights', 'send')
    .argv;

var base               = args.uri,
    customerId         = args['customer-id'],
    csvFile            = args['csv-file'],
    allowedCredentials = args['allowed-credentials'].split(',').map(s => s.trim()),
    rights             = args.rights.split(',').map(s => s.trim());

// Init Apis
//
var authApi = new Penneo({
    url: base + "/auth/api/v1/",
    auth: 'JWT'
});
var signApi = new Penneo({
    url: base + "/api/v1/",
    auth: 'JWT'
});

// Helpers

function getUsers(csvFile) {
    var csvConents = read.sync(csvFile, 'utf8');
    return babyparse.
        parse(csvConents).
        data.
        map(function(row) {
            var email = row[0];
            var name = row[1];

            return {
                fullName: name,
                email: email,
                enabled: 1,
                rights: rights
            };
        }).
        filter(function(user) {
            return user.fullName && user.email;
        });
}

function requestError(res) {
    if (res.error) {
        console.log(res.error);
    } else {
        console.log('Unknown error: ');
        console.log(res);
    }
}

// Process

var users = getUsers(csvFile);

if (users.length > 40) {
    console.log('Creating more than 40 users is not supported yet.');
    process.exit(-1);
}

auth.authenticate(base + '/api/v1').then(function(token) {

    authApi.setToken(token);
    signApi.setToken(token);

    // Create users
    signApi.post('/customers/' + customerId + '/users', users).then(function(res) {

        // Log the users created
        res.data.forEach(function(user) {
            console.log(user.id + ': User created: ' + user.fullName+ ',' + user.email);
        });

        // Create allowed credentials
        var credentials = res.data.map(function(user) {
            return {
                customerId: customerId,
                userId: user.id,
                allowed: allowedCredentials
            };
        });
        return authApi.post('/credentials', credentials).catch(requestError);

    }).then(function(res) {

        // Log the credentials created
        res.data.forEach(function(allowed) {
            console.log(allowed.userId + ': Set allowed credentials: ' + allowed.id);
        });

        // Create setup requests
        var requests = res.data.map(function(allowed) {
            return {
                userId: allowed.userId,
                type: 'initialize'
            };
        });
        return authApi.post('/cred/requests', requests).catch(requestError);

    }).then(function(res) {

        // Log the setup requests created
        res.data.forEach(function(request) {
            console.log(request.userId + ': Created setup request: ' + request.id);
        });

    }).catch(requestError);
});



