var Game = function () {};

Game.prototype = {
    constructor: Game,
    humanTeam: 'X',
    humanTeamColor: 'blue',
    botTeam: 'O',
    botTeamColor: 'red',
    turns: [],

    registerEvents: function (game) {
        $('.cell')
            .filter(function() {
                return !$(this).find('input').val();
            })
            .on('click', function(e) {
                game.makeMove(
                    $(this).data('row'),
                    $(this).data('column'),
                    game.humanTeam,
                    game.humanTeamColor
                ).done(function (data) {
                    if (data && data.status === 'gameover') {
                        this.writeMessage('<p>' + data.message + '</p>');
                        this.removeEvents();
                        return;
                    }

                    this.requestBotMove(this);
                });

                $(this).unbind('click');
            });
    },

    registerRestartEvent: function (game) {
        $('.restart').on('click', function() {
            var cell = $('.cell');

            cell.find('input').val('');
            cell.contents().filter(function(){ return this.nodeType !== 1; }).remove();

            $('.message p').remove();
            $('.notifications').css("background", "none");

            game.removeEvents();
            game.registerEvents(game);

            game.turns = [];
        });
    },

    removeEvents: function () {
        $('.cell').unbind('click');
    },

    requestBotMove: function (game) {
        this.removeEvents();

        var board = this.currentBoardState();

        $.ajax({
            url: 'http://localhost:3001/v1/bot/' + game.botTeam + '/move',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({board: board}),
        }).done(function (data) {
            game.makeMove(
                data.row,
                data.column,
                game.botTeam,
                game.botTeamColor
            ).done(function(data) {
                if (data && data.status === 'gameover') {
                    this.writeMessage('<p>' + data.message + '</p>');
                    return;
                }

                this.registerEvents(this);
            });
        }).fail(function (response) {
            console.log(response);
            alert('Something goes wrong, sorry, try again later.');
        });
    },

    requestGameStatus: function () {
        return $.ajax({
            url: 'http://localhost:3001/v1/game/status',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({boardTurns: this.turns}),
            context: this,
        }).fail(function (response) {
            console.log(response);
            alert('Something goes wrong, sorry, try again later.');
        });
    },

    makeMove: function (row, column, team, color) {
        this.removeEvents();
        $('input[name="position[' + row + '][' + column + ']"]')
            .val(team)
            .parent()
            .append(team)
            .css("color", color);

        this.turns.push({
            'team'   : team,
            'row'    : row,
            'column' : column
        });

        return this.requestGameStatus();
    },

    currentBoardState: function () {
        board = [
            ['', '', ''],
            ['', '', ''],
            ['', '', '']
        ];

        $('.cell').each(function (index, item) {
            board[$(this).data('row')][$(this).data('column')] = $(this).find('input').val();
        });

        return board;
    },

    writeMessage: function (message) {
        $('.notifications').css("background", "gray");
        $('.message').append(message);
    }
};

$(document).ready(function() {
    var game = new Game();
    game.registerEvents(game);
    game.registerRestartEvent(game);
});
