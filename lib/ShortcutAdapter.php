<?php namespace app\lib;

use app\Config;

/**
 * This class knows how to talk to Shortcut
 */
class ShortcutAdapter
{
    public static function getStoriesByIteration($iterationId) {
        error_log("Requesting stories of iteration ${iterationId}", 4);
        $url = Config::host . Config::endpoint . '/stories/search';
        $request = new Request($url);
        $result = $request->post([
            'iteration_id' => $iterationId
        ]);
        error_log(count($result) . " stories found", 4);
        return $result;
    }

    public static function getStoryHistory($storyId)
    {
        error_log("Requesting story ${storyId} history", 4);
        $url = Config::host . Config::endpoint . '/stories/' . (int)$storyId . '/history';
        $request = new Request($url);
        return $request->get();
    }

    public static function filterStoryHistoryStateChangesOnly($storyHistory) : array
    {
        $storyHistorySelectedEvents = array();

        foreach ($storyHistory as $event) {

            $filteredActions = array_filter($event['actions'], function($action) {
                return
                    $action['action'] == 'update' &&
                    array_key_exists('changes', $action) &&
                    array_key_exists('workflow_state_id', $action['changes']);
            });

            if (count($filteredActions)) {
                $event['actions'] = $filteredActions;
                $storyHistorySelectedEvents[] = $event;
            }

        }

        return $storyHistorySelectedEvents;

    }
}