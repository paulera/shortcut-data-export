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
            'started',
            'started_at',
            'deadline',
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
            'started',
            'started_at',
            'deadline',
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
                    foreach ($actions as $action) {

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

    public static function buildStoriesDependencyMap($stories) : array
    {
        $resultData = array();

        $storyDataMap = array(
            'id',
            'epic_id',
            'story_type',
            'estimate',
            'name',
            'started',
            'started_at',
            'deadline',
            'completed',
            'completed_at',
            'blocker',
            'blocked'
        );

        $resultData[0] = array_merge(
            $storyDataMap,
            ['current_state'],
            ['verb','subject_id','subject_state', 'this_iteration']
        );

        $stories_ids = array_map(fn($story) => $story['id'], $stories);

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
            $mainRowData[] = Config::workflowStateMap[$story['workflow_state_id']] ?? $story['workflow_state_id'];

            if (count($story['story_links'])) {
                foreach ($story['story_links'] as $link) {

                    $verb = $link['verb'];
                    if ($verb == "blocks" && $story['blocked']) {
                        $verb = "blocked by";
                    }

                    $resultData[] = array_merge($mainRowData, [
                        $verb,
                        $link['subject_id'],
                        Config::workflowStateMap[$link['subject_workflow_state_id']] ?? $link['subject_workflow_state_id'],
                        in_array($link['subject_id'], $stories_ids)?1:0
                    ]);
                }
            } else {
                $resultData[] = array_merge($mainRowData, ['','','','']);
            }

        }

        return $resultData;
    }

}