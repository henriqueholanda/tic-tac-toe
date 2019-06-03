var Game = function () {};

Game.prototype = {
    constructor: Game,
    humanTeam: 'O',
    humanTeamColor: 'blue',
    botTeam: 'X',
    botTeamColor: 'red',
    humanMove: [],
    gameId: '',

    registerNewGame: function (game) {
        $('.newGame').on('click', function() {
            game.requestNewGame(game);
            game.registerRestartEvent(game);
        });
    },

    requestNewGame: function(game) {
        return $.ajax({
            url: 'http://localhost:3001/v1/games',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({botPlayer: game.botTeam}),
            context: this
        }).done(function (data) {
            game.gameId = data.gameId;
            $('.newGame').hide();
            $('.restart').show();
            game.registerEvents(game);
        }).fail(function (response) {
            console.log(response);
            alert('Something goes wrong, sorry, try again later.');
        });
    },

    registerEvents: function (game) {
        $('.cell')
            .filter(function() {
                return !$(this).find('input').val();
            })
            .on('click', function(e) {
                game.moveRequest(
                    game,
                    $(this).data('row'),
                    $(this).data('column'),
                    game.humanTeam,
                    game.humanTeamColor
                );

                $(this).unbind('click');
            });
    },

    registerRestartEvent: function (game) {
        $('.restart').on('click', function() {
            game.removeEvents();
            var cell = $('.cell');

            cell.find('input').val('');
            cell.contents().filter(function(){ return this.nodeType !== 1; }).remove();

            $('.message p').remove();
            $('.notifications').css("background", "none");


            game.humanMove = [];
            game.gameId = '';

            game.requestNewGame(game);
        });
    },

    removeEvents: function () {
        $('.cell').unbind('click');
    },

    makeMove: function (game, row, column, team, color) {
        console.log(game);
        this.removeEvents();
        $('input[name="position[' + row + '][' + column + ']"]')
            .val(team)
            .parent()
            .append(team)
            .css("color", color);
    },

    moveRequest: function(game, row, column, team, color) {

        game.makeMove(game, row, column, team, color);

        game.humanMove = [];
        this.humanMove.push({
            'row'    : row,
            'col' : column
        });

        return $.ajax({
            url: 'http://localhost:3001/v1/games/' + game.gameId + '/move',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({move: this.humanMove}),
            context: this,
        }).done(function (data) {
            game.makeMove(
                game,
                data.row,
                data.col,
                game.botTeam,
                game.botTeamColor
            );

            if (data && data.status === 'gameover') {
                game.writeMessage('<p>' + data.message + '</p>');
                game.removeEvents();
            }

            game.registerEvents(game);
        }).fail(function (response) {
            console.log(response);
            alert('Something goes wrong, sorry, try again later.');
        });
    },

    writeMessage: function (message) {
        $('.notifications').css("background", "gray");
        $('.message').append(message);
    }
};

$(document).ready(function() {
    var game = new Game();
    game.registerNewGame(game);
});
