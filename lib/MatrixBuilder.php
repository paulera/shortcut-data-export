<?php namespace app\lib;

use app\Config;

class MatrixBuilder
{
    static function buildStoriesBasicInfo($stories): array {

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

        $resultData = array();
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

}