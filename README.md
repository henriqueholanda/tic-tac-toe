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

## API Resources

### /v1/game/status

This resource will give you the game status based on game turns.

Request example:
```bash
Request
POST http://localhost:3001/v1/game/status

Body:
Content-Type: application/json
"boardTurns": [
    {
        "team": "X",
        "row": 1,
        "column": 2
    },
    {
        "team": "O",
        "row": 0,
        "column": 1
    }
]
```

Response example when game is not over:
```bash
204 No Content
```

Response example when game is over and has a winner:
```bash
200 Ok

{
    "status": "gameover",
    "message": "The X team is the winner!",
    "board" : [
        ["X", "O", "O"],
        ["", "X", ""],
        ["", "", "X"]
    ]
}
```

Response example when game is over in a draw:
```bash
200 Ok

{
    "status": "gameover",
    "message": "Draw game!",
    "board" : [
        ["X", "O", "O"],
        ["O", "X", "X"],
        ["X", "O", "O"]
    ]
}
```

### /v1/bot/{team}/move

This resource will give you the next BOT movement based on game board.

Request example:
```bash
Request
POST http://localhost:3001/v1/bot/O/move

Body:
Content-Type: application/json
"board" : [
        ["X", "O", ""],
        ["X", "", ""],
        ["", "", ""]
    ]
```
Response example:
```bash
200 Ok

{
    "row": 0,
    "column": 2,
}
```

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
