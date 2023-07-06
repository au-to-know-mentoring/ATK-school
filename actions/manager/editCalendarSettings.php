<?php

use Aws\DynamoDb\SetValue;

function editCalendarSettings_GET(Web $w)
{

    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    // $mentorsCalendarSettings = SchoolService::getInstance($w)->getCalenderSettingsForUserId($user->id);
    // var_dump($mentorsCalendarSettings); die;

    $calendar_option_array = [];

    foreach (SchoolService::getInstance($w)->GetAllTeachers() as $teacher) {
        $calendar_settings = SchoolService::getInstance($w)->GetCalendarSettingsForUserIdAndTeacherId($user->id, $teacher->id);

        // print_r($calendar_settings->is_view_class);
        // print_r($calendar_settings->is_view_availability);
        // Set form field values if retrieved from or assign false
        if(empty($calendar_settings)){
            $calendar_settings = new SchoolCalendarSettings($w);
        }

        $calendar_option_array[] = [
            (new \Html\Form\InputField\Text())->setLabel('Mentor Name')->setValue($teacher->getFullName())->setName('mentorname_' . $teacher->id)->setDisabled(true),


            (new \Html\Form\InputField\Text())->setValue($teacher->id)->setName('teacherid_' . $teacher->id)->setType('hidden'),

            //Visibility
            (new \Html\Form\InputField\Checkbox())->setLabel('View Classes')->setName('visibility_' . $teacher->id)->setChecked($calendar_settings->is_view_class),


            //Availability
            (new \Html\Form\InputField\Checkbox())->setLabel('View Availability')->setName('availability_' . $teacher->id)->setChecked($calendar_settings->is_view_availability),

            //Colour
            (new \Html\Form\InputField\Color())->setLabel('Select colour')->setName('colour_' . $teacher->id)->setValue($calendar_settings->colour),
        ];
    }

    $form = [
        'Calendar Settings' => $calendar_option_array
    ];

    //var_dump($form); die;

    // What is calendaroptions
    $w->out(Html::multiColForm($form, '/school-manager/editCalendarSettings', "POST", "Apply"));
}

function editCalendarSettings_POST(Web $w)
{
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }


    // var_dump($_POST); 

    $formData = [];


    foreach ($_POST as $key => $value) {
        $fieldArray = explode('_', $key);
        // echo '<br>';
        // print_r($fieldArray);
        if (count($fieldArray) == 2) {
            $mentor_id = $fieldArray[1];

            $formData[$mentor_id][$fieldArray[0]] = $value;
        }
    }
    // echo '<br><br>';
    // // var_dump($formData);
    // echo "<br><br>";
    // print_r($formData); die;



    echo "<br><br>";
    // seperate into each mentor
    foreach ($formData as $teacherId => $subArray) {
        // print_r($subArray); die;
        // echo "<br><br>";

        // Get settings from datase for manager and mentor where ids match
        $calendar_settings = SchoolService::getInstance($w)->GetCalendarSettingsForUserIdAndTeacherId($user->id, $teacherId);
        
        

        // if there is no saved settings create new calendar settings object
        if (empty($calendar_settings)) {
            $calendar_settings = new SchoolCalendarSettings($w);
        }


        // fill settings from calendar object array
        $calendar_settings->user_id = $user->id;
        $calendar_settings->teacher_id = $teacherId;


        if (array_key_exists('visibility', $subArray)) {
            $calendar_settings->is_view_class = true;
        } else {
            $calendar_settings->is_view_class = false;
        }


        if (array_key_exists('availability', $subArray)) {
            $calendar_settings->is_view_availability = true;
        } else {
            $calendar_settings->is_view_availability = false;
        }

        $calendar_settings->colour = $subArray['colour'];
        $calendar_settings->insertOrUpdate();
        $w->msg("Calender settings set successfully", 'school-manager/calendar');
    }
}