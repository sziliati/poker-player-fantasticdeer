<?php

use GuzzleHttp\Client;

class Player
{
	const VERSION = "Default PHP folding player";

	const RANKING_API = 'http://rainman.leanpoker.org/rank';

	private $highCards = ["10", "J", "Q", "K", "A"];

	/**
	 * @var Client
	 */
	private $httpClient;

	/**
	 * @var PreFlopStrategy
	 */
	private $preFlopStrategy;

	public function __construct(Client $httpClient)
	{
		$this->httpClient = $httpClient;
		$this->preFlopStrategy = new PreFlopStrategy();
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
		// SOLUTION: pre-flop strategy?
		if (count($game_state['community_cards']) === 0) {
			$decision = $this->preFlopStrategy->getAction($player['hole_cards']);
			$bet = max($bet, $game_state['small_blind']);

			switch ($decision) {
				//case 'raise':
				//	return $game_state['pot'];
                case 'allin':
				case 'raise':
				case 'limp':
					if ($bet > $player['stack'] * 0.1) {
						$this->log(sprintf('Folding pre-flop because the bet (%s) is larger than the allowed 20 percent threshold of our stack (%s)', $bet, $player['stack']));

						return 0;
					}

					return $bet;

				case 'fold':
					return 0;

				default:
					$this->log('Invalid pre-flop strategy: '.$decision);
					return 0;
			}
		}

		$ranking = $this->rankCards($player['hole_cards'], $game_state['community_cards']);

		$this->log(var_export($ranking, true));

		if ($ranking['rank'] > 2 && $ranking['strength'] == 2) {
			return (int)($player['stack'] / 2);
		} else if ($ranking['rank'] > 3 && $ranking['strength'] == 2) {
			return $player['stack'];
		}

		if ($bet > $player['stack'] * 0.2) {
			$this->log(sprintf('Folding because the bet (%s) is larger than the allowed 20 percent threshold of our stack (%s)', $bet, $player['stack']));

			return 0;
		}

		return $bet;
	}

	public function showdown($game_state)
	{
	}

	private function rankCards(array $playerCards, array $communityCards)
	{
		$cards = [
			'cards' => json_encode(array_merge($playerCards, $communityCards)),
		];

		$resp = $this->httpClient->get(self::RANKING_API, [
			'form_params' => $cards,
		]);

		$ranking = json_decode($resp->getBody(), true);

		$ranking['player_cards'] = $playerCards;
		$ranking['community_cards'] = $communityCards;

		$i = 0;

		foreach ($playerCards as $playerCard) {
			foreach ($communityCards as $communityCard) {
				if ($playerCard['rank'] == $communityCard['rank']) {
					$i++;

					if ($i === 2) {
						break 2;
					}
				}
			}
		}

		$ranking['strength'] = $i;

		return $ranking;
	}

	private function log($line)
	{
		file_put_contents('php://stderr', $line . "\n");
	}
}
