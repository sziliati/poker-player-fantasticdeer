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
		$this->log(var_export($game_state, true));

		$player = $game_state['players'][$game_state['in_action']];

		$bet = $game_state['current_buy_in'] - $player['bet'];

		if ($bet < 0) {
			return 0;
		}

		// TODO: jó ötlet minden tétet tartani?
		if (count($game_state['community_cards']) === 0) {
			return max($bet, $game_state['small_blind']);
		}

		$cards = [
			'cards' => json_encode(array_merge($player['hole_cards'], $game_state['community_cards'])),
		];

		try {
			$resp = $this->httpClient->get(self::RANKING_API, [
				'form_params' => $cards,
				//'debug' => fopen("php://stderr", 'w+'),
			]);
		} catch (\Throwable $e) {
			$this->log('Getting ranking failed: '.$e->getMessage());

			return 0;
		}

		$ranking = json_decode($resp->getBody(), true);

		$this->log(var_export($ranking, true));

		if ($ranking['rank'] > 2) {
			return $player['stack'];
		}

		return $bet;
	}

	public function showdown($game_state)
	{
	}

	private function log($line)
	{
		file_put_contents('php://stderr', $line . "\n");
	}
}
