<?php

use GuzzleHttp\Client;

require_once __DIR__ . '/vendor/autoload.php';

$client = new Client();

$gameState = array(
    'tournament_id' => '5a76d98a8a55dc000400cda6',
    'game_id' => '5af17092f84ad50004000090',
    'round' => 27,
    'players' =>
        array(
            0 =>
                array(
                    'name' => 'PocketRockets',
                    'stack' => 1058,
                    'status' => 'folded',
                    'bet' => 0,
                    'time_used' => 1531064,
                    'version' => '0.1',
                    'id' => 0,
                ),
            1 =>
                array(
                    'name' => 'Successful Dolphin',
                    'stack' => 1113,
                    'status' => 'folded',
                    'bet' => 0,
                    'time_used' => 1659730,
                    'version' => 'Dumb Java player',
                    'id' => 1,
                ),
            2 =>
                array(
                    'name' => 'FantasticDeer',
                    'stack' => 1785,
                    'status' => 'active',
                    'bet' => 5,
                    'hole_cards' =>
                        array(
                            0 =>
                                array(
                                    'rank' => '3',
                                    'suit' => 'clubs',
                                ),
                            1 =>
                                array(
                                    'rank' => 'A',
                                    'suit' => 'clubs',
                                ),
                        ),
                    'time_used' => 3064291,
                    'version' => 'Default PHP folding player',
                    'id' => 2,
                ),
            3 =>
                array(
                    'name' => 'Bright Cat',
                    'stack' => 29,
                    'status' => 'active',
                    'bet' => 10,
                    'time_used' => 1130350,
                    'version' => 'Bright Cat V4',
                    'id' => 3,
                ),
        ),
    'small_blind' => 5,
    'big_blind' => 10,
    'orbits' => 6,
    'dealer' => 1,
    'community_cards' =>
        array(),
    'current_buy_in' => 10,
    'pot' => 15,
    'in_action' => 2,
    'minimum_raise' => 5,
    'bet_index' => 4,
);

$response = $client->request('POST', 'http://poker-player-fantasticdeer.szabolaszlo', [
    'form_params' => [
        'game_state' => $gameState,
        'action' => 'bet_request',
    ]
]);

echo PHP_EOL . 'Response: ' . $response->getBody() . '  SatusCode:' . $response->getStatusCode() . PHP_EOL;