<?php

use GuzzleHttp\Client;

class Player
{
	const VERSION = "Default PHP folding player";

	const RANKING_API = 'http://rainman.leanpoker.org/rank';

	const LIMP_THRESHOLD = 0.3;

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
			$bet = max($bet, $game_state['minimum_raise']);

			if ($bet > $player['stack'] * 0.7) {
				$bet = $player['stack'];
			}

			if (
				$decision === 'fold' &&
				(
					($game_state['dealer'] + 1 % 4) == $game_state['in_action'] ||
					($game_state['dealer'] + 2 % 4) == $game_state['in_action']
				)
			){
				$decision = 'blind';
			}

			switch ($decision) {
				//case 'raise':
				//	return $game_state['pot'];
                case 'allin':
                    return (int) ($player['stack'] / 2);

				case 'blind':
					return $bet;

				case 'raise':
				case 'limp':
					if ($bet > $player['stack'] * self::LIMP_THRESHOLD) {
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

		if ($ranking['rank'] > 3 && $ranking['strength'] == 1) {
			return $player['stack'];
		} else if ($ranking['rank'] > 2 && $ranking['strength'] == 2) {
			return (int)($player['stack'] / 2);
		}

		if ($bet > $player['stack'] * self::LIMP_THRESHOLD) {
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
