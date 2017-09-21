#!/usr/bin/python3

import datetime
import hashlib
import base64
import requests
import json
import sys
import getopt

def generate_nonce(length=8):
    import random
    """Generate pseudorandom number."""
    return ''.join([str(random.randint(0, 9)) for i in range(length)])

def generate_headers(key, secret):
    """Generate wsse headers."""

    timestamp = datetime.datetime.now().strftime("%Y-%m-%dT%H:%M:%SZ")

    nonce = generate_nonce()
    hashedNonce = hashlib.sha512(bytes(nonce, 'utf-8')).hexdigest()

    decodedNonce = base64.b64decode(hashedNonce)
    digest = decodedNonce + timestamp.encode() + secret.encode();

    digest = hashlib.sha1(digest).digest()
    digest = base64.b64encode(digest)
    digest = str(digest).strip("b'")

    username_token = 'UsernameToken Username="{}", PasswordDigest="{}", Nonce="{}", Created="{}"'.format(key, digest, hashedNonce, timestamp)

    headers = {
        'Authorization': 'WSSE profile="UsernameToken"',
        'X-WSSE': username_token,
        'Accept-charset': 'utf-8',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }

    return headers

def get_user(url, headers):
    r = requests.get(url + '/api/v1/user', headers=headers)
    print(r.status_code)
    print(r.text)

def create_casefile(url, headers):
    payload = {
        "sensitiveData": False,
        "documentDisplayMode": 0,
        "visibilityMode": 0,
        "title": "ABC Test"
    }

    r = requests.post(url + '/api/v1/casefiles', headers=headers, data=json.dumps(payload))
    print(r.status_code)
    print(r.text)

def main(argv):

    url = 'https://sandbox.penneo.com'
    key = ''
    secret = ''

    try:
        opts, args = getopt.getopt(argv,"hu:k:s:",["url=", "key=", "secret="])
    except getopt.GetoptError:
        print ('auth.py -u <url> -k <key> -s <secret>')
        sys.exit(2)

    for opt, arg in opts:
        if opt == '-h':
            print ('auth.py -u <url> -k <key> -s <secret>')
            sys.exit()
        elif opt in ("-u", "--url"):
            url = arg
        elif opt in ("-k", "--key"):
            key = arg
        elif opt in ("-s", "--secret"):
            secret = arg

    headers = generate_headers(key, secret)

    get_user(url, headers)
    # create_casefile(url, headers)

if __name__ == "__main__":
    main(sys.argv[1:])


