<?php namespace app;

// create a copy of this file renamed as config.php and set your TOKEN

// To get an API token, go to
// https://app.shortcut.com/videoslots/settings/account/api-tokens
// set any "Token Name" (that's just for you to get organised)
// and click "Generate Token". You can redo this process as many
// times as you want.

/**
 * Constants that setup the application.
 */
class Config
{
    const apikey = '---> YOUR API KEY HERE <---';
    const host = 'https://api.app.shortcut.com';
    const endpoint = '/api/v3';

    const workflowStateMap = array(
        500000003 => 'ToDo',
        500000004 => 'In Development',
        500000005 => 'Ready for Review',
        500000204 => 'Ready for QA',
        500007062 => 'In QA',
        500005143 => 'Ready for Merge',
        500000006 => 'Pending Approval',
        500000102 => 'Waiting for Deploy',
        500000025 => 'Deployed Live',
        500000174 => 'Completed'
    );

}