<?php namespace app\lib;

use app\Config;

/**
 * This is a presentation class, it knows how to produce strings
 */
class Presenter
{

    /**
     *
     * Converts an array into a TSV table string
     *
     * @param array $tableData Matrix indexed as [rows][cols]
     * @return String `$tableData` organized as Tab Separated Values
     */
    static function generateTsvTable(array $tableData) : String {
        $imploded_rows = array_map(fn($row) => implode("\t", $row), $tableData);
        return implode("\n", $imploded_rows);
    }

}