<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
    	file_put_contents(STDOUT, var_export($game_state, true));

        return $game_state['small_blind'];
    }

    public function showdown($game_state)
    {
    }
}
