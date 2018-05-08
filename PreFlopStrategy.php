<?php
/**
 * Created by PhpStorm.
 * User: szabolaszlo
 * Date: 2018.05.08.
 * 10ime: 13:25
 */

class PreFlopStrategy
{
    protected $raise = array(
        array('A', 'A'),
        array('K', 'K'),
        array('Q', 'Q'),
        array('J', 'J'),
        array('10', '10'),
        array('9', '9'),
        array('8', '8'),
        array('7', '7'),
        array('A', 'K'),
        array('A', 'Q'),
        array('A', 'J'),
        array('A', '10'),
        array('K', 'Q'),
        array('K', 'J'),
        array('K', '10'),
        array('K', 'Q'),
        array('K', 'J'),
        array('K', '10'),
        array('Q', 'J'),
        array('Q', '10'),
        array('J', '10'),
        array('J', '9'),

    );

    protected $limp = array();

    public function __construct()
    {
        sort($this->raise);
        sort($this->limp);
    }

    /**
     * return raise, limp, fold
     */
    public function getAction($hand)
    {
        $cards = array();
        foreach ($hand as $card) {
            $cards[] = $card['rank'];
        }

        sort($cards);

        if (in_array($cards, $this->raise)) {
            return 'raise';
        }

        if (in_array($cards, $this->limp)) {
            return 'limp';
        }

        return 'fold';
    }

}