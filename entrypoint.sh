#!/usr/bin/env bash

if [[ "$1" == httpd-foreground ]] || [ "$1" == php-fpm ]; then

    if [ ! -f /usr/local/www/phpMyAdmin/config.secret.inc.php ]; then
        cat > /usr/local/www/phpMyAdmin/config.secret.inc.php <<EOT
<?php
\$cfg['blowfish_secret'] = '$(tr -dc 'a-zA-Z0-9~!@#$%^&*_()+}{?></";.,[]=-' < /dev/urandom | fold -w 32 | head -n 1)';
EOT
    fi
    chgrp www /usr/local/www/phpMyAdmin/config.secret.inc.php

    if [ ! -f /usr/local/www/phpMyAdmin/config.user.inc.php ]; then
        touch /usr/local/www/phpMyAdmin/config.user.inc.php
    fi
fi

if [ ! -z "${HIDE_PHP_VERSION}" ]; then
    echo "PHP version is now hidden."
    echo -e 'expose_php = Off\n' > $PHP_INI_DIR/conf.d/phpmyadmin-hide-php-version.ini
fi

if [ ! -z "${PMA_CONFIG_BASE64}" ]; then
    echo "Adding the custom config.inc.php from base64."
    echo "${PMA_CONFIG_BASE64}" | base64 -d > /usr/local/www/phpMyAdmin/config.inc.php
fi

if [ ! -z "${PMA_USER_CONFIG_BASE64}" ]; then
    echo "Adding the custom config.user.inc.php from base64."
    echo "${PMA_USER_CONFIG_BASE64}" | base64 -d > /usr/local/www/phpMyAdmin/config.user.inc.php
fi

# start: Apache specific settings
if [ -n "${APACHE_PORT+x}" ]; then
    echo "Setting apache port to ${APACHE_PORT}."
    gsed -i "/VirtualHost \*:80/c\\<VirtualHost \*:${APACHE_PORT}\>" /usr/local/etc/apache24/extra/httpd-vhosts.conf
    gsed -i "/Listen 80/c\Listen ${APACHE_PORT}" /usr/local/etc/apache24/httpd.conf
    service apache24 oneconfigtest
fi
# end: Apache specific settings

get_secret() {
    local env_var="${1}"
    local env_var_file="${env_var}_FILE"

    # Check if the variable with name $env_var_file (which is $PMA_PASSWORD_FILE for example)
    # is not empty and export $PMA_PASSWORD as the password in the Docker secrets file

    if [[ -n "${!env_var_file}" ]]; then
        export "${env_var}"="$(cat "${!env_var_file}")"
    fi
}

get_secret PMA_USER
get_secret PMA_PASSWORD
get_secret MYSQL_ROOT_PASSWORD
get_secret MYSQL_PASSWORD
get_secret PMA_HOSTS
get_secret PMA_HOST
get_secret PMA_CONTROLHOST
get_secret PMA_CONTROLUSER
get_secret PMA_CONTROLPASS

exec "$@"
