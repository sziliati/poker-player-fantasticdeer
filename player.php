<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
    	file_put_contents("php://stderr", var_export($game_state, true)."\n");

		if ($game_state['current_buy_in'] > 20) {
			return 0;
		}

        return $game_state['small_blind'];
    }

    public function showdown($game_state)
    {
    }
}
