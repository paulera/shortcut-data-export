<?php

require_once "boot.php";

use app\Lib;
use app\lib\Presenter;
use app\lib\MatrixBuilder;
use app\lib\ShortcutAdapter;

// @todo: refactor me - break into parts and place in Presenter and MatrixBuilder - build Matrix first, then compile table

switch ($_GET['action']){
    case 'detailedhistory':
        $iterationId = $_GET['iterationid'];
        $stories = ShortcutAdapter::getStoriesByIteration($iterationId);

        $storiesHistory = array();
        foreach ($stories as $story) {
            $storyId = $story['id'];
            if (array_key_exists($storyId, $storiesHistory)) {
                throw new \Exception("Duplicated story history entry");
            }
            $storiesHistory[$storyId] = ShortcutAdapter::getStoryHistory($storyId);
        }

        $matrix = MatrixBuilder::buildStoriesDetailedHistory($stories, $storiesHistory);
        $tsvTable = Presenter::generateTsvTable($matrix);
        print ($tsvTable);
        break;

    case 'basicinfo':
        $iterationId = $_GET['iterationid'];
        $stories = ShortcutAdapter::getStoriesByIteration($iterationId);
        $matrix = MatrixBuilder::buildStoriesBasicInfo($stories);
        $tsvTable = Presenter::generateTsvTable($matrix);
        print ($tsvTable);
        break;
}



