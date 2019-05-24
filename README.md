# Tic Tac Toe Game

This is a functional implementation of Tic Tac Toe game.

## Getting started

### Requirements

To run this project you must have installed this following tools:

* [Docker](https://docs.docker.com/engine/installation/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Git](https://git-scm.com/)

### Running the project

To run this project you just need to follow the steps bellow:

1 - Clone project repository:
```bash
$ git clone https://github.com/henriqueholanda/tic-tac-toe.git
```

2 - Start the project
```bash
$ make start
```
> Note: This command may spend some time to complete, mainly when you run for the first
time, because it will download all Docker images that project needs from [Docker Store](https://store.docker.com)
and up two applications `backend` and `frontend`.

3 - Access the application on your favorite browser
```bash
http://localhost:3000
```

### Stopping the project

If you want to stop you just need to run the following command:
```bash
$ make stop
```

## API Documentation

The API documentation was on OpenAPI format.

To see the doc you can access `http://localhost:3002` on your browser.

> Note: You must need to start project before it.

If you prefer to see doc on other platform, you only need to import the `openapi.json`
file to them.

> Note: To generate a new version of API doc, you need to run `php bin/console app:generate-doc`
on API root.

## Run tests

```bash
$ make test
```

## Run tests with coverage

```bash
$ make coverage
```
> Note: The coverage will be generated in HTML format
and will be available on the folder `tic-tac-toe/var/coverage`

## Run lint
```bash
$ make lint
```

## Run static analysis
```bash
$ make analyse
```

## Technologies

### Backend

* [Symfony](https://symfony.com) - PHP Framework
* [Composer](https://getcomposer.org) - Dependency Manager 
* [Nginx](https://www.nginx.com) - Webserver

### Frontend

* [PureCSS](https://purecss.io) - CSS library
* [jQuery](https://jquery.com) - Javascript library
* [Nginx](https://www.nginx.com) - Webserver

## Author

[Henrique Holanda](https://henriqueholanda.dev) 
