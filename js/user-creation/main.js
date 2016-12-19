var Penneo    = require('penneo-js-sdk');
var read      = require('read-file');
var babyparse = require('babyparse');
var args      = require('optimist')
      .usage('Bulk creation of Penneo users.')
      .demand('uri')
      .demand('token-file')
      .demand('customer-id')
      .demand('csv-file')
      .default('credentials', ['nemid', 'api'])
      .default('rights', ['send', 'validation'])
      .argv;

var base        = args.uri,
    customerId  = args['customer-id'],
    tokenFile   = args['token-file'],
    csvFile     = args['csv-file'],
    credentials = args.credentials,
    rights      = args.rights;

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
var token = read.sync(tokenFile, 'utf8');
authApi.setToken(token);
signApi.setToken(token);

// Process
//
var csvConents = read.sync(csvFile, 'utf8');
babyparse.parse(csvConents).data.forEach(function(row) {
  var email = row[0];
  var name = row[1];

  if (!name && !email) {
    return;
  }

  signApi.post('/customers/' + customerId + '/users', { // Create User
    fullName: name,
    email: email,
    enabled: 1,
    rights: rights
  }).then(function(res) {
    var userId = res.data.id;
    console.log(
      userId + ': User created: '
        + name + ',' + email
    );
    return authApi.post('/credentials', {               // Create credentials
      customerId: customerId,
      userId: userId,
      allowed: credentials
    }).catch(requestError);

  }).then(function(res) {
    var userId = res.data.userId;
    console.log(
      userId + ': Set allowed credentials: '
        + res.data.id
    );

    return authApi.post('/cred/requests', {             // Create setup requests
      userId: userId,
      type: 'initialize'
    }).catch(requestError);

  }).then(function(res) {
    var userId = res.data.userId;
    console.log(
      userId + ': Created setup request: '
        + res.data.id
    );
  }).catch(requestError);
});


function requestError(res) {
  if (res.error) {
    console.log(res.error);
  } else {
    console.log(res);
  }
}
