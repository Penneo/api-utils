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

function makeRequest(method, url, data) {
    return request({
        method: method,
        baseUrl: config.endpoint,
        url: url,
        headers: generateAuthHeader(config)
    });
}

// @todo: The function doesn't check for response status codes
function getFormData(caseFileId) {
    return makeRequest('GET', `/casefiles/${caseFileId}`)           // 1. Get the case File

        .then(data => {                                             // 2. Get documents
            var caseFile = JSON.parse(data);
            console.log(`Case file: ${caseFile.title}`);
            var url = `casefiles/${caseFile.id}/documents`;
            return makeRequest('GET', url);
        })

        .then(data => {                                             // 3. Extract the document Ids
            var documents = JSON.parse(data);
            return documents.map(doc => doc.id);
        })
        .then(data => {                                             // 4. Use the first document
            // Get the form data for the first one
            var documentId = data[0];
            return makeRequest('GET', `/documents/${documentId}`);
        })
        .then(data => {                                             // 5. Extract the meta Data from the document
            var document = JSON.parse(data);
            var metaData = document.metaData;
            return metaData;
        })
        .then(metaData => {                                         // 6. If we have json in the meta data, parse it
            return JSON.parse(metaData);
        });

}

getFormData(81500).then(data => {
    console.log(data);
});
