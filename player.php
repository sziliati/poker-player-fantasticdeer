<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
    	file_put_contents("php://stderr", var_export($game_state, true)."\n");

		if ($game_state['current_buy_in'] > 50) {
			return 0;
		}

		$bet = $game_state['current_buy_in'] - $game_state[$game_state['in_action']]['bet'];

        return max($bet, $game_state['small_blind']);
    }

    public function showdown($game_state)
    {
    }
}
