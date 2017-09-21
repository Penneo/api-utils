#!/usr/bin/python3

import base64
import datetime
import getopt
import hashlib
import json
import random
import requests
import sys

def generate_nonce():
    return base64.b64encode(bytes(str(random.randrange(10**19)), 'utf-8')).decode('utf-8')

def generate_headers(key, secret):
    timestamp = datetime.datetime.now().strftime("%Y-%m-%dT%H:%M:%SZ")
    nonce = generate_nonce()
    digest = '{}{}{}'.format(base64.b64decode(nonce).decode('utf-8'), timestamp, secret)
    hashed_digest = hashlib.sha1(bytes(digest, 'utf-8')).digest()
    encoded_hashed_digest = base64.b64encode(hashed_digest).decode('utf-8')

    username_token = 'UsernameToken Username="{}", PasswordDigest="{}", Nonce="{}", Created="{}"'.format(key, encoded_hashed_digest, nonce, timestamp)

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


