<?php

require_once "boot.php";

use app\Config;
use app\Lib;
use app\lib\Request;
use app\lib\ShortcutAdapter;

// @todo: refactor me - move to Shortcut Adapter
function getStoryHistory($storyId) {
    $url = Config::host . Config::endpoint . '/stories/'.(int)$storyId.'/history';
    $output = (new Request($url))->get();
    return $output;
}

// @todo: refactor me - break into parts and place in Presenter and MatrixBuilder - build Matrix first, then compile table
function printStoriesDetailedHistory($iterationId) {

    $output = ShortcutAdapter::getStoriesByIteration($iterationId);

    $storyDataMap = array(
        'id',
        'epic_id',
        'story_type',
        'estimate',
        'name',
        'deadline',
        'started',
        'started_at',
        'completed',
        'completed_at',
        'blocker',
        'blocked'
    );

    $stateChangeHeaders = array(
        'state_previous',
        'state_new',
        'state_change'
    );

    print(implode("\t", $storyDataMap));
    print("\t");
    print(implode("\t", $stateChangeHeaders));

    foreach ($output as $data) {

        $mainRowData = array();
        foreach ($storyDataMap as $key) {
            $value = $data[$key];
            if (gettype($value) == 'boolean') {
                $value = $value?1:0;
            } elseif (substr($key, -3) == '_at' || $key == 'deadline') {
                $value = substr($value, 0, 10);
            }
            $mainRowData[] = $value;
        }

        $storyHistory = getStoryHistory($data['id']);

        // Search for workflow state changes
        foreach ($storyHistory as $historyEntry) {
            foreach ($historyEntry['actions'] as $action) {
                foreach ($action['changes'] as $changeType => $change) {
                    if ($changeType == 'workflow_state_id') {
                        $stateChangeData = array();
                        $stateChangeData['state_previous'] = Config::workflowStateMap[$change['old']] ?? $change['old'];
                        $stateChangeData['state_new'] = Config::workflowStateMap[$change['new']] ?? $change['new'];
                        $stateChangeData['state_change'] = substr($historyEntry['changed_at'], 0, 10);
                        $rowData = array_merge($mainRowData, $stateChangeData);

                        print("\n".implode("\t", array_values($rowData)));
                    }
                }
            }
        }

    }
}

switch ($_GET['action']){
    case 'detailedhistory':
        printStoriesDetailedHistory($_GET['iterationid']);
        break;

    case 'basicinfo':
        $iterationId = $_GET['iterationid'];
        $stories = ShortcutAdapter::getStoriesByIteration($iterationId);
        $storiesBasicInfoMatrix = lib\MatrixBuilder::buildStoriesBasicInfo($stories);
        $tsvTable = lib\Presenter::generateTsvTable($storiesBasicInfoMatrix);
        print ($tsvTable);
        break;
}



