<?php namespace app\lib;

use app\Config;

/**
 * This class knows how to talk to Shortcut
 */
class ShortcutAdapter
{
    static function getStoriesByIteration($iterationId) {

        $data = array(
            'iteration_id' => $iterationId
        );
        $url = Config::host . Config::endpoint . '/stories/search';

        return (new Request($url))->post($data);
    }
}