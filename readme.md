# TorTipper
A tipping system implemented in PHP using the Laravel framework to split donations across tor relays.

## Hosted Instance
Once this project reaches a MVP state, I plan on hosting an instance at [https://tortipper.elyc.in](https://tortipper.elyc.in).

## Running your own instance
This project is based off of the laravel framework and will require the following package requirements.
```bash
mysql-server - The database engine that will store the relay information.
php7.2-fpm - The language this application is written in.
composer - Used to download PHP Libraries.
node - Compiling the latest frontend.
bitcoind - Bitcoin Daemon

```

Configure bitcoin daemon  
Bitcoin stores its configuration at `~/.bitcoin/bitcoin.conf` but is not created by default.  
Please add the following to the newly created file for a basic configuration.
```text
# Bind the RPC interface to localhost so nobody can access it but the server.
rpcbind=127.0.0.1

# RPC Username and password
rpcuser=CHANGE_ME
rpcpassword=CHANGE_ME

# Allow localhost to access the RPC.
rpcallowip=127.0.0.1/32

# Do not use more than 550 MB of blockchain disk space (prune node)
prune=550
```

Installation Instructions
```bash
# Download the project source code
git clone git@github.com:Elycin/TorTipper.git
cd TorTipper

# Install the packages 
composer install
npm install

# Compile the frontend
npm run prod

# Copy the example environment file to the production environment file.
cp .env.example .env

# Edit the .env, set APP_DEBUG to `false` and edit other fields: MySQL and Bitcoin RPC.
nano .env
```

Automatic Maintenance  
Please add the following string to your crontab to allow that application to automatically download new relay information.
```bash
* * * * * cd /path-to-repository && php artisan schedule:run >> /dev/null 2>&1
```

At this point, you will need to configure your webserver to provide access to the project by referencing the `/public` directory in the project's working directory.

## Contributing
All forms of contribution or suggestions are welcome in the form of a issue or a pull request.

## License
MIT - Please see `license.md` for the full details.

## Contributors
[Phoul](https://twitter.com/Phoul) - Advice 