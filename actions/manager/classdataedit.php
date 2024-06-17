<?php

function classdataedit_GET(Web $w) {
    $p = $w->pathMatch('student_id','class_data_id');

    
   

    if (empty($p['student_id'])) {
        $w->error('No Student Id found TEST', '/school-teacher/studentlist');
    }

    $student = SchoolService::getInstance($w)->GetStudentForId($p['student_id']);

    if (empty($student)) {
        $w->error('No Student found for id', '/school-teacher/studentlist');
    }

    $w->ctx('title', 'Class edit for : ' . $student->getContact()->getFullName());

    if (empty($p['class_data_id'])) {
        $class_data = new SchoolClassData($w);
    } else {
        $class_data = SchoolService::getInstance($w)->GetClassDataForId($p['class_data_id']);
    }

    $frequencySelectArray = ['one off', 'weekly','fortnightly','monthly'];
    $statusSelectArray = ['pending', 'active', 'on hold', 'completed'];

    $form = [
        'Class Details'=> [
            [
                
                ['Teacher', 'select', 'teacher_id', $class_data->teacher_id, SchoolService::getInstance($w)->GetAllTeachers()],
                ['Topic', 'text', 'topic', $class_data->topic]
            ],
            [
                ['Timezone', 'select', 'timezone', 'Australia/Sydney', SchoolService::getInstance($w)->GetTimeZoneSelectOptions()]
            ],
            [
                ['Start Date', 'date', 'start_date', $class_data->getStartDate()],
                //['Class Start Time', 'text', 'start_time', $class_data->getStartTime()],
                (new \Html\Form\InputField([
                    "id|name"        => "start_time",
                    "value"            => $class_data->getStartTime(),
                    "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                    "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                    "required"        => "true"
                ]))->setLabel('Class Start Time')
            ],
            [
                //['Is Recurring', 'checkbox', 'is_recurring', $class_data->is_recurring],
                ['Frequency', 'select', 'frequency', $class_data->frequency, $frequencySelectArray],
                (new \Html\Form\InputField\Number())->setLabel("Duration (Hours)")->setName("duration")->setValue($class_data->duration)
            ],
            [
                ['End Date (Optional)', 'date', 'end_date', $class_data->getEndDate()],
                ['Status', 'select', 'status', $class_data->status, $statusSelectArray],
                (new \Html\Form\InputField())->setType('decimal')->setLabel('Rate')->setName('rate')->setValue($class_data->rate)
            ],
            [
                ['Link', 'text', 'link', $class_data->link]
            ],
            [
                ['Notes', 'textarea', 'notes', $class_data->notes]
            ]
        ]
    ];


    
    if (empty($p['class_data_id'])) {
        $form_url = '/school-manager/classdataedit/' . $student->id;
    } else {
        $form_url = '/school-manager/classdataedit/' . $student->id . '/' . $class_data->id;
    }


    $w->out(Html::multiColForm($form, $form_url));

}

function classdataedit_POST(Web $w) {
    $p = $w->pathMatch('student_id','class_data_id');
   
    if (empty($p['student_id'])) {
        $w->error('No Student Id found', '/school-teacher/studentlist');
    }

    $student = SchoolService::getInstance($w)->GetStudentForId($p['student_id']);

    if (empty($student)) {
        $w->error('No Student found for id', '/school-teacher/studentlist');
    }

    if (empty($p['class_data_id'])) {
        $class_data = new SchoolClassData($w);
    } else {
        $class_data = SchoolService::getInstance($w)->GetClassDataForId($p['class_data_id']);
    }

    $class_data->fill($_POST);
    $class_data->student_id = $student->id;

    if ($_POST['frequency'] != 'one off'){
        $class_data->is_recurring = true;
    }

    //check which timezone the start time value is for
    //$time = new DateTime(NULL, new DateTimeZone($timezone));
    $redirect = '/school-teacher/studentview/' . $student->id;

    
   
        $time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST['start_time'], new DateTimeZone($_SESSION['usertimezone']));
    
    //echo "<pre>";
    // var_dump($time_object); die;
   
    $class_data->dt_class_date = $time_object;//->format('Y/m/d H:i:s');
    

    
    //end date
    
       
           
        $end_time_object = new DateTime(str_replace('/', '-', $_POST['end_date']) . ' ' . $_POST['start_time'], new DateTimeZone($_SESSION['usertimezone']));
             
        //echo "<pre>";
        //var_dump(new DateTime($time_object->getTimestamp())->format('Y-m-d H:i:s')); die;

     
        $class_data->dt_end_date = $end_time_object;//->format('Y/m/d H:i:s');


    $Dt_daylight_savings_check = new DateTime("now", new DateTimeZone($_POST['timezone']));
    
    $class_data->daylight_savings_offset = $Dt_daylight_savings_check->format("O");

  
 
        
    $class_data->insertOrUpdate();
    
    $msg = "class data saved";

    $w->msg($msg, '/school-teacher/studentview/' . $student->id);

}