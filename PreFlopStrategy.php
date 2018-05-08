<?php
/**
 * Created by PhpStorm.
 * User: szabolaszlo
 * Date: 2018.05.08.
 * 10ime: 13:25
 */

class PreFlopStrategy
{
    protected $allin = array(
        array('A', 'A'),
        array('A', 'K'),
        array('K', 'K'),
        array('Q', 'Q'),
        array('J', 'J'),
        array('10', '10'),
    );

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

    protected $limp = array(
        array('6', '6'),
        array('5', '5'),
        array('A', '9'),
        array('A', '8'),
        array('A', '7'),
        array('A', '6'),
        array('K', '9'),
        array('Q', '9'),
        array('Q', '8'),
        array('J', '8'),
        array('10', '8'),
        array('10', '9'),
        array('9', '8'),
    );

    public function __construct()
    {
        foreach ($this->raise as $key => $raise) {
            sort($raise);
            $this->raise[$key] = $raise;
        }
        foreach ($this->limp as $key => $limp) {
            sort($limp);
            $this->limp[$key] = $limp;
        }
        foreach ($this->allin as $key => $allin) {
            sort($allin);
            $this->allin[$key] = $allin;
        }
    }

    /**
     * return raise, limp, fold, allin
     */
    public function getAction($hand)
    {
        $cards = array();
        foreach ($hand as $card) {
            $cards[] = $card['rank'];
        }

        sort($cards);

        if (in_array($cards, $this->allin)) {
            return 'allin';
        }

        if (in_array($cards, $this->raise)) {
            return 'raise';
        }

        if (in_array($cards, $this->limp)) {
            return 'limp';
        }

        return 'fold';
    }

}