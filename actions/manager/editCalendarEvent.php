<?php

use Aws\DynamoDb\SetValue;

function editCalendarEvent_GET(Web $w)
{
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $calendar_event_options = [];
}