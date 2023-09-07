<?php

function addcustomcalendar_GET(Web $w)
{
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $form = [
        "Add custom calendar" => [
            [
                (new \Html\Form\InputField([
                    "id|name"        => "calendar_name",
                    "placeholder"    => "Add name for custom calendar",
                    "required"        => "true"
                ]))->setLabel('Calendar Name')

            ],
        ]
    ];

    $w->out(Html::multiColForm($form, '/school-manager/addcustomcalendar', "POST", "Apply"));
}


function addcustomcalendar_POST(Web $w) 
{
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }
    
    // var_dump($_POST["calendar_name"]); die;
    // var_dump($_POST["calendar_name"]); die; 
    
    $calendarName = $_POST["calendar_name"];

    $custom_calendar = new SchoolCustomCalendar($w);
    // var_dump($custom_calendar); die;

    $custom_calendar->calendar_name = $calendarName;
    $custom_calendar->user_id = $user->id;
    // var_dump($custom_calendar); die;

    $custom_calendar->insertOrUpdate();
    $w->msg("Custom Calendar made successfully", "school-manager/calendar");
    

    // Should I list all calendar events to remove or add them ???  Guessing it goes in the other button :P
    
    
}

