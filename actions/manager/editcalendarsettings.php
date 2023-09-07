<?php

use Aws\DynamoDb\SetValue;

function editcalendarsettings_GET(Web $w)
{

    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    // $mentorsCalendarSettings = SchoolService::getInstance($w)->getCalenderSettingsForUserId($user->id);
    // var_dump($mentorsCalendarSettings); die;

    $calendar_option_array = [];
    $custom_calendar_array = [];

    foreach (SchoolService::getInstance($w)->GetAllTeachers() as $teacher) {
        $calendar_settings = SchoolService::getInstance($w)->GetCalendarSettingsForUserIdAndTeacherId($user->id, $teacher->id);

        // print_r($calendar_settings->is_view_class);
        // print_r($calendar_settings->is_view_availability);
        // Set form field values if retrieved from or assign false
        if (empty($calendar_settings)) {
            $calendar_settings = new SchoolCalendarSettings($w);
        }

        $calendar_option_array[] = [
            (new \Html\Form\InputField\Text())->setLabel('Mentor Name')->setValue($teacher->getFullName())->setName('mentorname~' . $teacher->id)->setDisabled(true),


            (new \Html\Form\InputField\Text())->setValue($teacher->id)->setName('teacherid~' . $teacher->id)->setType('hidden'),

            //Visibility
            (new \Html\Form\InputField\Checkbox())->setLabel('View Classes')->setName('visibility~' . $teacher->id)->setChecked($calendar_settings->is_view_class),


            //Availability
            (new \Html\Form\InputField\Checkbox())->setLabel('View Availability')->setName('availability~' . $teacher->id)->setChecked($calendar_settings->is_view_availability),

            //Colour
            (new \Html\Form\InputField\Color())->setLabel('Select colour')->setName('colour~' . $teacher->id)->setValue($calendar_settings->colour),
        ];
    }




    foreach (SchoolService::getInstance($w)->getAllCustomCalendars() as $customCalendar) {

        $custom_calendar_settings = SchoolService::getInstance($w)->GetCustomCalendarSettingsForUserIdAndCalendarName($user->id, str_replace(" ", "_", $customCalendar->calendar_name));
        // print_r($customCalendar->calendar_name); 
        // print_r($custom_calendar_settings);

        if (empty($custom_calendar_settings)) {
            $custom_calendar_settings = new SchoolCustomCalendarSettings($w);
        }
        // print_r($customCalendar->calendar_name); die;
        $custom_calendar_array[] = [
            (new \Html\Form\InputField\Text())->setLabel('Custom Calendar')->setValue($customCalendar->calendar_name)->setDisabled(true)->setName('custCalName-' . str_replace(" ", "_", $customCalendar->calendar_name)),

            // (new \Html\Form\InputField\Text())->setValue($customCalendar->calendar_name)->setName('custCalName-' . str_replace()->setType('hidden'),


            (new \Html\Form\InputField\Checkbox())->setLabel('View Calendars')->setName('isView-' . $customCalendar->calendar_name)->setChecked($custom_calendar_settings->is_view_calendar),
            
            (new \Html\Form\InputField\Color())->setLabel('Select colour')->setName('colour-' . $customCalendar->calendar_name)->setValue($custom_calendar_settings->colour),
        ];
    }
    // die;

    $form = [
        'Calendar Settings' => $calendar_option_array,
        'Custom Calendars' => $custom_calendar_array
    ];

    // var_dump($form); die;

    $w->out(Html::multiColForm($form, '/school-manager/editcalendarsettings', "POST", "Apply"));
}

function editcalendarsettings_POST(Web $w)
{
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }


    // var_dump($_POST); die;

    $CalSettingsFormData = [];
    $CustCalSettingsFormData = [];


    foreach ($_POST as $key => $value) {
        $fieldArray1 = explode('~', $key);
        // var_dump($fieldArray);
        // echo '<br>';
        if (count($fieldArray1) == 2) {
            // var_dump($value . "</br>");
            // print_r($fieldArray[1]); die;
            $mentor_id = $fieldArray1[1];
            $CalSettingsFormData[$mentor_id][$fieldArray1[0]] = $value;
            

        }
    }

    // var_dump($CalSettingsFormData); die;
    // echo "</br>";
    // echo "<br><br>";

    // var_dump($_POST); die;
    foreach ($_POST as $key => $value) {
        $fieldArray2 = explode('-', $key);

        if (count($fieldArray2) == 2) {
            // var_dump($value . "</br>");
            $custCalName = $fieldArray2[1];
            $CustCalSettingsFormData[$custCalName][$fieldArray2[0]] = $value;
        }
    }

    // var_dump($CustCalSettingsFormData); die;
    // die;
    // echo '<br><br>';
    // // var_dump(CalSettingsFormData);
    // echo "<br><br>";
    // print_r($CalSettingsFormData); die;



    // echo "<br><br>";
    // seperate into each mentor
    foreach ($CalSettingsFormData as $teacherId => $subArray) {
        // print_r($subArray);
        // echo "<br><br>";
        // echo $teacherId;
        // print_r($CalSettingsFormData); echo "<br><br>";

        // Get settings from datase for manager and mentor where ids match
        $calendar_settings = SchoolService::getInstance($w)->GetCalendarSettingsForUserIdAndTeacherId($user->id, $teacherId);
        // print_r($calendar_settings); die;


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
        // print_r($calendar_settings); die;
        $calendar_settings->insertOrUpdate();
        // print_r($calendar_settings); die;
    }

    // echo "</br></br>";
    // print_r($subArray); die;
    foreach($CustCalSettingsFormData as $calendarName => $subArray) {
        $cust_cal_settings = SchoolService::getInstance($w)->GetCustomCalendarSettingsForUserIdAndCalendarName($user->id, $calendarName);

        // print_r($CustCalSettingsFormData); die; 
        if(empty($cust_cal_settings)) {
            // echo "test"; die;
            $cust_cal_settings = new SchoolCustomCalendarSettings($w);
        }

        $cust_cal_settings->user_id = $user->id;

        if (array_key_exists('isView', $subArray)) {
            $cust_cal_settings->is_view_calendar = true;
        } else {
            $cust_cal_settings->is_view_calendar = false;
        }
        $cust_cal_settings->colour = $subArray['colour'];

        $cust_cal_settings->custom_calendar_name = $calendarName;

        $cust_cal_settings->insertOrUpdate();
    }
    // die;
    $w->msg("Calender settings set successfully", 'school-manager/calendar');
}
