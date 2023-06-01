<?php 


function editCalendarSettings_GET(Web $w){
    
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $mentorsCalendarSettings = SchoolService::getInstance($w)->getCalenderSettingsForUserId($user->id);
    var_dump($mentorsCalendarSettings); die;

    $calendar_option_array = [];

    foreach(SchoolService::getInstance($w)->GetAllTeachers() as $teacher){
        $calendar_option_array[] = [
            (new \Html\Form\InputField\Text())->setLabel('Mentor Name')->setValue($teacher->getFullName())->setName('mentorid_' . $teacher->id)->setDisabled(true),
            (new \Html\Form\InputField\Checkbox())->setLabel('View Classes')->setName('visibility_' . $teacher->id)->setChecked(false),
            (new \Html\Form\InputField\Checkbox())->setLabel('View Availability')->setName('availability_' . $teacher->id)->setChecked(false),
            (new \Html\Form\InputField\Color())->setLabel('Select colour'),
        ];
    }

    $form = [
        'Calendar Settings' => $calendar_option_array
    ];

    $w->out(Html::multiColForm($form, '/school-manager/calendaroptions', "POST", "Apply"));


}

function calendarsettings_POST(Web $w){

}