<?php

function editcustomcalendarevent_GET(Web $w)
{
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $custom_calendar_names = [];

    foreach (SchoolService::getInstance($w)->GetAllCustomCalendars() as $calendar) {
        //
        array_push($custom_calendar_names, $calendar->calendar_name);

        // var_dump($calendar->calendar_name); die;

    }

    // foreach (SchoolService::getInstance($w)->getAllCustomCalendarEvents() as $calendarEvents) {
    $form = [
        'Custom Calendar Event Settings' => [
            [
                ["Calendar Name", "select", "calendar_name", $calendar->calendar_name, $custom_calendar_names],

                (new \Html\Form\InputField\Text())->setLabel('Event Name')->setName("event_name")->setPlaceholder("Enter event name")->setRequired('true'),
            ],
            [
                (new \Html\Form\InputField\Text())->setValue($calendar->id)->setName('custom_calendar_id')->setType('hidden'),
            ],
            [
                (new \Html\Form\Textarea())->setLabel('Event Description')->setName("event_description")->setPlaceholder("Enter event description")->setRequired('true'),

                ['Start Date', 'date', 'start_date', ""],
            ],
            [
                ['TimeZone', 'select', 'timezone', "", SchoolService::getInstance($w)->GetTimeZoneSelectOptions()],
            ],
            [
                (new \Html\Form\InputField([
                    "id|name"        => "dt_start_time",
                    "value"            => "",
                    "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                    "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                    "required"        => "true"
                ]))->setLabel('Event Start Time'),

                (new \Html\Form\InputField([
                    "id|name"        => "dt_end_time",
                    "value"            => "",
                    "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                    "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                    "required"        => "true"
                ]))->setLabel('Event End Time')
            ],
            [
                ["Event Status", "select", "status", "", ["Pending", "Active", "On hold", "Completed"]],

                (new \Html\Form\InputField\Checkbox())->setLabel("Is recurring?")->setName("is_repeat"),

                ["Repeat interval", "select", "repeat_interval", "", ["Once off", "Daily", "Weekly", "Fornightly", "monthly", "yearly"]],
            ],
        ]
    ];

    // print_r($custom_calendar_names); die;

    //var_dump($form); die;

    // What is calendaroptions
    $w->out(Html::multiColForm($form, '/school-manager/editcustomcalendarevent', "POST", "Apply"));
}

function editcustomcalendarevent_POST(Web $w)
{

    // var_dump($_POST); die;
    // $p = TO BE CONTINUED
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $calendar_event = new SchoolCustomCalendarEvent($w);
    $calendar_event->fill($_POST);

    // var_dump($calendar_event->custom_calendar_id); die;
    $redirect = '/school-manager/calendar/';

    try {
        $start_time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST["dt_start_time"]);
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error("Invalid start date or time", $redirect ?: "/school-manager/editcustomcalendarevent");
    }

    // var_dump($time_object);
    // var_dump($time_object->getTimestamp()); die;

    $start_dt_object = new DateTime();
    $start_dt_object->setTimestamp($start_time_object->getTimestamp());
    $calendar_event->dt_start_time = $start_dt_object->format('Y-m-d H:i:s');
    // print_r($calendar_event->dt_start_time);


    try {
        $end_time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST["dt_end_time"]);
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error("Invalid end time", $redirect ?: "/school-manager/editcustomcalendarevent");
    }

    $end_dt_object = new DateTime();
    $end_dt_object->setTimestamp($end_time_object->getTimestamp());
    // var_dump($time_object);
    // var_dump($time_object->getTimestamp()); die;
    
    $end_dt_object->setTimestamp($end_time_object->getTimestamp());
    $calendar_event->dt_end_time = $end_dt_object->format('Y-m-d H:i:s');
    // print_r($calendar_event->dt_end_time);
    // print_r($calendar_event);

    $calendar_event->insertOrUpdate();

    $msg = "Event settings saved";

    $w->msg($msg, '/school-manager/calendar/');

    

    
    
}
