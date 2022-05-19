<?php namespace app\lib;

use app\Config;

class MatrixBuilder
{
    static function buildStoriesBasicInfo($stories): array {

        $resultData = array();

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

        $resultData[0] = array_merge($storyDataMap, ['current_state']);

        foreach ($stories as $data) {

            $rowData = array();
            foreach ($storyDataMap as $key) {
                $value = $data[$key];
                if (gettype($value) == 'boolean') {
                    $value = $value?1:0;
                } elseif (substr($key, -3) == '_at' || $key == 'deadline') {
                    $value = substr($value, 0, 10);
                }
                $rowData[] = $value;
            }
            $rowData[] = Config::workflowStateMap[$data['workflow_state_id']] ?? $data['workflow_state_id'];

            $resultData[] = $rowData;

        }

        return $resultData;
    }

    public static function buildStoriesDetailedHistory($stories, $storiesHistory) : array
    {

        $resultData = array();

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

        $resultData[0] = array_merge($storyDataMap, $stateChangeHeaders);

        foreach ($stories as $story) {

            $mainRowData = array();
            foreach ($storyDataMap as $key) {
                $value = $story[$key];
                if (gettype($value) == 'boolean') {
                    $value = $value ? 1 : 0;
                } elseif (substr($key, -3) == '_at' || $key == 'deadline') {
                    $value = substr($value, 0, 10);
                }
                $mainRowData[] = $value;
            }


            if (array_key_exists($story['id'], $storiesHistory)) {

                $storyHistoryEvents = $storiesHistory[$story['id']];

                // Search for workflow state changes
                foreach ($storyHistoryEvents as $event) {

                    $actions = $event['actions'];
                    $filteredActions = array_filter($actions, function($action) {
                        return
                            $action['action'] == 'update' &&
                            array_key_exists('changes', $action) &&
                            array_key_exists('workflow_state_id', $action['changes']);
                    });

                    foreach ($filteredActions as $action) {

                        $oldState = $action['changes']['workflow_state_id']['old'];
                        $newState = $action['changes']['workflow_state_id']['new'];
                        $dateChange = $event['changed_at'];

                        $stateChangeData = array(
                            'state_previous' => Config::workflowStateMap[$oldState] ?? $oldState,
                            'state_new' => Config::workflowStateMap[$newState] ?? $newState,
                            'state_change' => substr($dateChange, 0, 10)
                        );

                        $resultData[] = array_merge($mainRowData, $stateChangeData);
                    }
                }

            }

        }

        return $resultData;

    }

}