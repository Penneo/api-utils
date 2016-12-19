var prompt = require('prompt');
var Penneo = require('penneo-js-sdk');
var write  = require('write');
var args   = require('optimist')
      .usage('Create authentication token')
      .demand('uri')
      .demand('output-file')
      .argv;

var uri       = args.uri,
    tokenFile = args['output-file'];

// Prompt input
//
var input = [{
  name: 'username',
  required: true
}, {
  name: 'password',
  hidden: true,
  replace: '*',
  conform: function (value) {
    return true;
  }
}];


// Auth API
//
var authApi = new Penneo({
  url: uri + "/auth/api/v1/",
  auth: 'JWT'
});


prompt.start();

console.log('Please enter your classic credentials to generate an authentication token:');
prompt.get(input, function (err, result) {
  return authApi.post('/token/password', {
    username: result.username,
    password: result.password
  }).then(function(res) {
    write(tokenFile, res.data, function(err) {
      if (err) {
        errorHandler(err);
        return;
      }
      console.log('Token saved in ' + tokenFile);
    });
  }).catch(errorHandler);
});

function errorHandler(res) {
  if (res.error) {
    console.log(res.error);
  } else {
    console.log(res);
  }
}
