#!/bin/sh

# https://owasp.org/www-community/password-special-characters
LC_ALL=C tr -dc '" !"#$%&()*+,-./:;<=>?@[\]^_`{|}~' < /dev/urandom | fold -w 32 | head -c 32
