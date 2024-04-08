<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://banners.beyondco.de/OLW.png?theme=light&packageManager=&packageName=by+Beer+%26+Code&pattern=architect&style=style_1&description=OPEN+LARAVEL+WEEK&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" width="650"></a></p>

# Project Installation
The project make use of Docker through the *Laravel Sail* package in order to facilitate its local environment configuration. So, it is necessary that you have Docker Engine installed on your machine.

Feel free to run the project on your local environment but this tutorial will not handle this situation.

Links to Docker installation and config:

- [Windows](https://docs.docker.com/docker-for-windows/install/)
- [Linux (Debian based)](https://docs.docker.com/engine/install/ubuntu/) 

### Steps to run the project locally

- Faça um clone do projeto para sua máquina local
- Git clone it to your machine
- Crie um arquivo `.env`, recomendamos usar `.env-example` como base
- Create a `.env` file, we recommend using `.env-example` as example
- You can add or alter as you may need
- acesse a pasta do projeto via console (terminal/PowerShell/CMD)
- Access the project folder through a shell (terminal/PowerShell/CMD)
- Run the following to install the packages on `composer.json`. Once it is finished, check if the *vendor* folder it is created.:
```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
 ```
- After installation is done, run the following `./sail up` to get on containers going. Additionally, you can pass the `-d` parameter to run in unattached mode. The containers needed for this project are declared in the file `docker-compose.yml`.
 
By default, it is not necessary any additional configuration on *.env* file. If you need to make changes to binding ports or database credential that is the right place to do it. 

# Working with Containers

Once your project are running on Docker containers, you will not be able to run anything outside the docker containers like `php artisan`, `composer` or `npm`. These will not work properly, if you need to run any commands for your project, you have to do it like so:

```bash
docker-compose exec \ #Running command on existing container
    -u sail \ # Especify the name of the user to be used inside the container
    projeto_laravel.test \ # Especify which container will the command be run
    php artisan route:list # Command to be executed
```
The command execution make it very verbose causing potential errors to happen. So the *Laravel Sail* have a script named `sail` it is stored on *vendor/bin/*. This command allows that the commands on the example above can be run through aliases so that the development  can be more natural and less complex. Example:

 ```bash
 ./vendor/bin/sail artisan route:list

 #or

 ./vendor/bin/sail art route:list
 ```

### Available commands

To know all the commands available by sail, you can run `./vendor/bin/sail -h` to obtain the full list with description and possible parameters.

# Next steps 
Migrations are a way to version the database tables. To have your DB up and running 
- Run `./vendor/bin/sail art migrate` to create the databases table

- Run `./vendor/bin/sail art db:seed` to populate your database with fake data to run the project

# Testing
To run the tests made on the project:
- Run `./vendor/bin/sail composer pest`
