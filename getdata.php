<?php

require_once "config.php";

use app\Config;


function getPaginatedRequest($url)
{

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array (
            'Content-Type: application/json',
            'Shortcut-Token: ' . Config::apikey
        )
    );

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $output = array(
            'message' => curl_error($ch)
        );
    } else {
        $output = json_decode($result, true);

        if ($output['next']) {
            $nextUrl = Config::host . $output['next'];
            $nextOutput = getPaginatedRequest($nextUrl);

            // incorporates the results of the next page into the current one
            if ($nextOutput['next']) {
                $output['next'] = $nextOutput['next'];
            } else {
                $output['next'] = null;
            }

            $output['data'] = array_merge($output['data'], $nextOutput['data']);

        }
    }

    curl_close($ch);

    return $output;

}

function getRequest($url)
{

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array (
            'Content-Type: application/json',
            'Shortcut-Token: ' . Config::apikey
        )
    );
    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $output = array(
            'message' => curl_error($ch)
        );
    } else {
        $output = json_decode($result, true);
    }

    curl_close($ch);

    return $output;

}

/**
 * @param $url
 * @param $data
 * @return array|mixed
 */
function postRequest($url, $data)
{

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
        array (
            'Content-Type: application/json',
            'Shortcut-Token: ' . Config::apikey
        )
    );

    $result = curl_exec($ch);

    if (curl_error($ch)) {
        $output = array(
            'message' => curl_error($ch)
        );
    } else {
        $output = json_decode($result, true);
    }

    curl_close($ch);

    return $output;

}

function getStoryHistory($storyId) {
    $url = Config::host . Config::endpoint . '/stories/'.(int)$storyId.'/history';
    $output = getRequest ($url);
    return $output;
}

function printStoriesDetailedHistory($iterationId) {

    $data = array(
        'iteration_id' => $iterationId
    );
    $url = Config::host . Config::endpoint . '/stories/search';
    $output = postRequest($url, $data);

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

function printStoriesBasicInfo($iterationId) {

    $data = array(
        'iteration_id' => $iterationId
    );
    $url = Config::host . Config::endpoint . '/stories/search';
    $output = postRequest($url, $data);

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

    print(implode("\t", $storyDataMap));
    print("\t"."current_state");

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
        $mainRowData[] = Config::workflowStateMap[$data['workflow_state_id']] ?? $data['workflow_state_id'];

        print("\n".implode("\t", array_values($mainRowData)));

    }
}

function searchStoriesByQuery($query)
{
    $data = array(
        'page_size' => 25,
        'query' => $query
    );
    $url = Config::host . Config::endpoint . '/search/stories'.'?'.http_build_query($data);

    $output = getPaginatedRequest ($url);

    $storyDataMap = array(
        'id',
        'story_type',
        'estimate',
        'deadline',
        'name',
        'started',
        'started_at',
        'completed',
        'completed_at',
        'blocker',
        'blocked'
    );

    print(implode("\t", $storyDataMap));

    foreach ($output['data'] as $index => $data) {
        $row = array();
        foreach ($storyDataMap as $key) {
            $value = $data[$key];
            if (gettype($value) == 'boolean') {
                $value = $value?1:0;
            } elseif (substr($key, -3) == "_at") {
                $value = substr($value, 0, 10);
            }
            $row[] = $value;
        }
        print("\n".implode("\t", array_values($row)));
    }

}

//searchStories($_GET['query']);
switch ($_GET['action']){
    case 'detailedhistory':
        printStoriesDetailedHistory($_GET['iterationid']);
        break;

    case 'basicinfo':
        printStoriesBasicInfo($_GET['iterationid']);
        break;
}



