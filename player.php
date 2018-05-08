<?php

class Player
{
    const VERSION = "Default PHP folding player";

    public function betRequest($game_state)
    {
        return $game_state['small_blind'];
    }

    public function showdown($game_state)
    {
    }
}
