#!/usr/bin/python3

import base64
import datetime
import getopt
import hashlib
import json
import random
import requests
import sys

debug=True

def generate_nonce():
    return base64.b64encode(bytes(str(random.randrange(10**19)), 'utf-8')).decode('utf-8')

def generate_token(url, username, password):
    headers = {
        'Accept-charset': 'utf-8',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
    payload = {
        "username": username,
        "password": password
    }
    r = requests.post(url + '/auth/api/v1/token/password', headers=headers, data=json.dumps(payload), verify=not debug)
    return r.text

def generate_headers(token):
    headers = {
        'Authorization': 'JWT',
        'x-auth-token': token,
        'Accept-charset': 'utf-8',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
    return headers

def get_user(url, headers):
    r = requests.get(url + '/api/v1/user', headers=headers, verify=not debug)
    print(r.status_code)
    print(r.text)

def main(argv):

    url = 'https://sandbox.penneo.com'
    username = ''
    password = ''

    try:
        opts, args = getopt.getopt(argv,"hu:",["url=", "username=", "password="])
    except getopt.GetoptError:
        print ('auth.py -u <url> --username <username> --password <password>')
        sys.exit(2)

    for opt, arg in opts:
        if opt == '-h':
            print ('auth.py -u <url> --username <username> --password <password>')
            sys.exit()
        elif opt in ("-u", "--url"):
            url = arg
        elif opt in ("--username"):
            username = arg
        elif opt in ("--password"):
            password = arg

    token   = generate_token(url, username=username, password=password)
    headers = generate_headers(token)

    get_user(url, headers=headers)

if __name__ == "__main__":
    main(sys.argv[1:])


