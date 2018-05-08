<?php

use GuzzleHttp\Client;

require_once __DIR__.'/vendor/autoload.php';

require_once('player.php');

$player = new Player(new Client());

switch($_POST['action'])
{
    case 'bet_request':
        echo $player->betRequest(json_decode($_POST['game_state'], true));
        break;
    case 'showdown':
        $player->showdown(json_decode($_POST['game_state'], true));
        break;
    case 'version':
        echo Player::VERSION;
}
