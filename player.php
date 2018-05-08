<?php

use GuzzleHttp\Client;

class Player
{
	const VERSION = "Default PHP folding player";

	const RANKING_API = 'http://rainman.leanpoker.org/rank';

	/**
	 * @var Client
	 */
	private $httpClient;

	public function __construct(Client $httpClient)
	{
		$this->httpClient = $httpClient;
	}

	public function betRequest($game_state)
	{
		file_put_contents("php://stderr", var_export($game_state, true) . "\n");

		$player = $game_state[$game_state['in_action']];


		$bet = $game_state['current_buy_in'] - $player['bet'];

		if ($bet < 0 ) {
			return 0;
		}

		if (count($game_state['community_cards']) === 0) {
			return max($bet, $game_state['small_blind']);
		}

		$cards = [
			'cards' => array_merge($player['hole_cards'], $game_state['community_cards']),
		];

		try {
			$resp = $this->httpClient->get(self::RANKING_API, [
				'form_params' => $cards,
			]);
		} catch (\Throwable $e) {
			file_put_contents("php://stderr", $e->getMessage() . "\n");

			return 0;
		}

		$ranking = json_decode($resp->getBody(), true);

		file_put_contents("php://stderr", var_export($ranking, true) . "\n");

		if ($ranking['rank'] > 2) {
			return $player['stack'];
		}

		return $bet;
	}

	public function showdown($game_state)
	{
	}
}
