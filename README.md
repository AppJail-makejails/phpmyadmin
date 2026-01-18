# phpMyAdmin

phpMyAdmin is a free and open source administration tool for MySQL and MariaDB. As a portable web application written primarily in PHP, it has become one of the most popular MySQL/MariaDB administration tools, especially for web hosting services.

<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/PhpMyAdmin_logo.svg/800px-PhpMyAdmin_logo.svg.png" width="80%" height="auto">

www.phpmyadmin.net

## How to use this Makejail

### Usage with external server

You can specify a MySQL/MariaDB host in the `pma_host` argument. You can also use `pma_port` to specify the port of the server in case it's not the default one:

```sh
appjail makejail \
    -j pma \
    -f gh+AppJail-makejails/phpmyadmin \
    -o virtualnet=":<random> default" \
    -o nat \
    -o expose=80 -- \
        --pma_host mariadb.ajnet.appjail
```

### Usage with arbitrary server

```sh
appjail makejail \
    -j pma \
    -f gh+AppJail-makejails/phpmyadmin \
    -o virtualnet=":<random> default" \
    -o nat \
    -o expose=80 -- \
        --pma_arbitrary 1
```

### Usage with appjail-director and arbitrary server

This will run phpMyAdmin with the arbitrary server option, allowing you to specify any MySQL/MariaDB server on the login page.

```sh
appjail-director up
```

**appjail-director.yml**:

```yaml
options:
  - virtualnet: ":<random> default"
  - nat:

services:
  db:
    makejail: gh+AppJail-makejails/mariadb
    name: mariadb
    arguments:
      - mariadb_root_host: '%'
      - mariadb_disallow_root_login_remotely: 0
      - mariadb_root_password: notSecureChangeMe

  phpmyadmin:
    makejail: gh+AppJail-makejails/phpmyadmin
    arguments:
      - pma_arbitrary: 1
    options:
      - expose: 80
```

### Adding Custom Configuration

You can add your own custom `config.inc.php` settings (such as Configuration Storage setup) by creating a file named `config.user.inc.php` with the various user defined settings in it, and then linking it into the container using:

```sh
# Open config.user.inc.php and make some changes.
$EDITOR config.user.inc.php
# Profit!
appjail makejail \
    -j pma \
    -f gh+AppJail-makejails/phpmyadmin \
    -o virtualnet=":<random> default" \
    -o nat \
    -o expose=80 \
    -o fstab="$PWD/config.user.inc.php usr/local/www/apache24/data/config.user.inc.php" -- \
        --pma_host mariadb.ajnet.appjail
```

**Note**: If you use php-fpm, change `/usr/local/www/apache24/data/config.user.inc.php` to `/usr/local/www/phpMyAdmin/config.user.inc.php`.

Be sure to have `<?php` as your first line of the configuration file or the contents will not be detected as PHP code.

Example:

```php
<?php

$cfg['ShowPhpInfo'] = true; // Adds a link to phpinfo() on the home page
```

### Usage behind a reverse proxy

Set the argument `pma_absolute_uri` to the fully-qualified path (e.g.: `https://pma.example.net/`) where the reverse proxy makes phpMyAdmin available.

### Arguments

* `pma_tag` (default: `14.3-php82-apache`): see [#tags](#tags).
* `pma_ajspec` (default: `gh+AppJail-makejails/phpmyadmin`): Entry point where the `appjail-ajspec(5)` file is located.
* `pma_blowfish_secret` (optional): AES password to use when `auth_type` is `cookie`. If not defined, a random secret will be generated.
* `pma_arbitrary` (optional): If enabled, allows you to log in to arbitrary servers using cookie authentication.
* `pma_host`: Define address/host name of the MySQL/MariaDB server.
* `pma_hosts`: Define comma separated list of address/host names of the MySQL/MariaDB servers.
* `pma_verbose` (optional): Define verbose name of the MySQL/MariaDB server. 
* `pma_verboses` (optional): Define comma separated list of verbose names of the MySQL/MariaDB servers.
* `pma_socket` (optional): Socket file for the database connection.
* `pma_sockets` (optional): Comma-separated list of socket files for the database connections. 
* `pma_port` (optional): Define port of the MySQL/MariaDB server.
* `pma_ports` (optional): Define comma separated list of ports of the MySQL/MariaDB servers.
* `pma_absolute_uri` (optional): Sets here the complete URL (with full path) to your phpMyAdmin installation's directory. E.g. `https://www.example.net/path_to_your_phpMyAdmin_directory/`.
* `pma_hide_php_version` (optional): If defined, this option will hide the PHP version ([expose_php = Off](https://www.php.net/manual/es/ini.core.php#ini.expose-php)).
* `pma_upload_limit` (default: `2048K`): This option will change [upload_max_filesize](https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize) and [post_max_size](https://www.php.net/manual/en/ini.core.php#ini.post-max-size) values. Format: `[0-9]+[KMG]`.
* `pma_memory_limit` (default: `512M`): This option will override the memory limit for phpMyAdmin ([$cfg\['MemoryLimit'\]](https://docs.phpmyadmin.net/en/latest/config.html#cfg_MemoryLimit)) and PHP ([memory_limit](https://www.php.net/manual/en/ini.core.php#ini.memory-limit)). Format: `[0-9]+[KMG]`.
* `pma_max_execution_time` (default: `600`): This option will override the maximum execution time in seconds for phpMyAdmin ([$cfg\['ExecTimeLimit'\]](https://docs.phpmyadmin.net/en/latest/config.html#cfg_ExecTimeLimit)) and PHP ([max_execution_time](https://www.php.net/manual/en/info.configuration.php#ini.max-execution-time)).
* `pma_session_save_path` (default: `/sessions`): See [session.save_path](https://www.php.net/manual/en/session.configuration.php#ini.session.save-path).
* `pma_user` (optional): Define username and password to use only with the config authentication method.
* `pma_password` (optional).
* `pma_uploaddir` (optional): If defined, this option will set the path where files can be saved to be available to import ([$cfg\['UploadDir'\]](https://docs.phpmyadmin.net/en/latest/config.html#cfg_UploadDir)).
* `pma_savedir` (optional): If defined, this option will set the path where exported files can be saved ([$cfg\['SaveDir'\]](https://docs.phpmyadmin.net/en/latest/config.html#cfg_SaveDir)).
* `pma_controlhost` (optional): When set, this points to an alternate database host used for storing the [phpMyAdmin Configuration Storage database](https://docs.phpmyadmin.net/en/latest/setup.html#phpmyadmin-configuration-storage).
* `pma_controlport` (optional): If set, will override the default port (`3306`) for connecting to the control host for storing the [phpMyAdmin Configuration Storage database database](https://docs.phpmyadmin.net/en/latest/setup.html#phpmyadmin-configuration-storage).
* `pma_controluser` (optional):  Define the username for phpMyAdmin to use for advanced features (the [controluser](https://docs.phpmyadmin.net/en/latest/config.html#cfg_Servers_controluser)).
* `pma_controlpass` (optional): Define the password for phpMyAdmin to use with the [controluser](https://docs.phpmyadmin.net/en/latest/config.html#cfg_Servers_controlpass).
* `pma_pmadb` (optional): Define the name of the database to be used for the [phpMyAdmin Configuration Storage database](https://docs.phpmyadmin.net/en/latest/setup.html#phpmyadmin-configuration-storage). When not set, the advanced features are not enabled by default: they can still potentially be enabled by the user when logging in with the zero conf (zero configuration) feature. Suggested values: `phpmyadmin` or `pmadb`.
* `pma_queryhistorydb` (optional): When set to [true](https://docs.phpmyadmin.net/en/latest/config.html#cfg_QueryHistoryDB), enables storing [SQL history](https://docs.phpmyadmin.net/en/latest/config.html#cfg_Servers_history) to the [phpMyAdmin Configuration Storage database](https://docs.phpmyadmin.net/en/latest/setup.html#phpmyadmin-configuration-storage). When [false](https://docs.phpmyadmin.net/en/latest/config.html#cfg_QueryHistoryDB), history is stored in the browser and is cleared when logging out.
* `pma_queryhistorymax` (default: `25`): When set to an integer, controls the number of history items.
* `pma_tz` (default: `UTC`): Change [date.timezone](https://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone).
* `pma_php_type` (default: `production`) The PHP configuration file to link to `/usr/local/etc/php.ini`. Valid values: `development`, `production`. Only valid for apache, use the `php_type` argument when using php-fpm.
* `pma_enable_curl_support` (default: `1`): Install with cURL extension.

## Tags

| Tag                 | Arch    | Version        | Type   | `pma_version` |
| ------------------- | ------- | -------------- | ------ | ------------- |
| `14.3-php82-apache` | `amd64` | `14.3-RELEASE` | `thin` |      `82`     |
| `14.3-php82-fpm`    | `amd64` | `14.3-RELEASE` | `thin` |      `82`     |
| `15-php82-apache` | `amd64` | `15` | `thin` |      `82`     |
| `15-php82-fpm`    | `amd64` | `15` | `thin` |      `82`     |

## Notes

1. The ideas present in the Docker image of phpMyAdmin are taken into account for users who are familiar with it.
