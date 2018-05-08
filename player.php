<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
    	file_put_contents("php://stderr", var_export($game_state, true)."\n");

    	$player = $game_state[$game_state['in_action']];

		$bet = $game_state['current_buy_in'] - $player['bet'];

		if (count($game_state['community_cards']) === 0) {
			return max($bet, $game_state['small_blind']);
		}

		foreach ($player['hole_cards'] as $card) {
			foreach ($game_state['community_cards'] as $communityCard) {
				if ($communityCard['rank'] === $card['rank']) {
					return $game_state['pot'];
				}
			}
		}

		return $bet;
    }

    public function showdown($game_state)
    {
    }
}
