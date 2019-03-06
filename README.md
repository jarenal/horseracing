# Welcome to Horse Racing!

Horse Racing is a game for testing purposes using websockets and PHP.

## Setup

Clone or download the project in a folder and then install dependencies using composer:

```bash
    $ cd PROJECT_FOLDER
    $ composer install
```

## Database

Import the database schema located at:

```textmate
misc/schema/schema.sql
```

into your database.

## Config file

Before to run the project you need to create the config file and update it with your settings.

Please copy the default config file:

```textmate
cp config/config.default.yaml config/config.yaml
```

and then update the new one with your database settings.


## Start Websocket daemon

This project requires run a websocket daemon. Execute the next command from the root of the project.

```bash
    $ php bin/daemon.php
```

## Setup Aapache

For to load the homepage we will need create a new VirtualHost on Apache.

```textmate
<VirtualHost *:80>
    ServerName horseracing.local

    DocumentRoot /var/www/horseracing/www
    <Directory /var/www/horseracing/www>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>

    ErrorLog /var/log/apache2/horseracing_error.log
    CustomLog /var/log/apache2/horseracing_access.log combined
</VirtualHost>

```

Don't forget reload Apache configuration and modify /etc/hosts file.

## Let's play!

Finally visit horseracing.local from your browser for to load the homepage.

## Authors

* **Jose Antonio** - *Initial work*

## Donations

If you found this useful. Please, consider support with a small donation:

* **BTC** - 1PPn4qvCQ1gRGFsFnpkufQAZHhJRoGo2g5
* **BCH** - qr66rzdwlcpefqemkywmfze9pf80kwue0v2gsfxr9m
* **ETH** - 0x5022cf2945604CDE2887068EE46608ed6B57cED8

## License

This project is licensed under the ISC License - see the [LICENSE](LICENSE) file for details