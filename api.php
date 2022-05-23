<?php

require_once "boot.php";

use app\lib\Presenter;
use app\lib\MatrixBuilder;
use app\lib\ShortcutAdapter;

switch ($_GET['action']){

    case 'detailedhistory':
        $iterationId = $_GET['iterationid'];
        $stories = ShortcutAdapter::getStoriesByIteration($iterationId);
        $stateChangesPerStory = array();
        foreach ($stories as $story) {
            $storyId = $story['id'];
            $storyHistory = ShortcutAdapter::getStoryHistory($storyId);
            $stateChangesPerStory[$storyId] = ShortcutAdapter::filterStoryHistoryStateChangesOnly($storyHistory);
        }
        $matrix = MatrixBuilder::buildStoriesDetailedHistory($stories, $stateChangesPerStory);
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

    case 'dependencies':
        $iterationId = $_GET['iterationid'];
        $stories = ShortcutAdapter::getStoriesByIteration($iterationId);
        $matrix = MatrixBuilder::buildStoriesDependencyMap($stories);
        $tsvTable = Presenter::generateTsvTable($matrix);
        print ($tsvTable);
        break;

}

