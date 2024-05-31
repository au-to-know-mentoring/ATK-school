<?php

function classdataedit_GET(Web $w) {
    $p = $w->pathMatch('student_id', 'class_data_id');




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

    $frequencySelectArray = ['one off', 'weekly', 'fortnightly', 'four weekly'];
    $statusSelectArray = ['pending', 'active', 'on hold', 'completed'];

    $form = [
        'Class Details' => [
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
                ['Frequency', 'select', 'frequency', $class_data->frequency, $frequencySelectArray, "null"],
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
    $p = $w->pathMatch('student_id', 'class_data_id');

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

    // var_dump($_POST); die;
    if ($_POST['frequency'] != "one off") {
        $class_data->is_recurring = true;
    }
    $class_data->fill($_POST);
    $class_data->student_id = $student->id;

    //check which timezone the start time value is for
    //$time = new DateTime(NULL, new DateTimeZone($timezone));

    try {
        if ($_POST['timezone'] == 'Australia/Sydney') {
            $time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST['start_time']);
        } else {
            $time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST['start_time'], new DateTimeZone($_POST['timezone']));
        }
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error('Invalid start date or time', $redirect ?: '/school');
    }
    //echo "<pre>";
    //var_dump(new DateTime($time_object->getTimestamp())->format('Y-m-d H:i:s')); die;
    $dt_object = new DateTime();
    $dt_object->setTimestamp($time_object->getTimestamp());
    $class_data->dt_class_date = $dt_object->format('Y-m-d H:i:s');

    $redirect = '/school-teacher/studentview/' . $student->id;
    //end date
    if ($_POST['end_date']) {
        try {
            if ($_POST['timezone'] == 'Australia/Sydney') {
                $end_time_object = new DateTime(str_replace('/', '-', $_POST['end_date']) . ' ' . $_POST['start_time']);
            } else {
                $end_time_object = new DateTime(str_replace('/', '-', $_POST['end_date']) . ' ' . $_POST['start_time'], new DateTimeZone($_POST['timezone']));
            }
        } catch (Exception $e) {
            LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $w->error('Invalid end date or time', $redirect ?: '/school');
        }
        //echo "<pre>";
        //var_dump(new DateTime($time_object->getTimestamp())->format('Y-m-d H:i:s')); die;
        $end_dt_object = new DateTime();
        $end_dt_object->setTimestamp($end_time_object->getTimestamp());
        $class_data->dt_end_date = $end_dt_object->format('Y-m-d H:i:s');
    }
    // var_dump($class_data); die;
    $class_data->insertOrUpdate();





    // Get instances of this class data IF it is unedited / dont want to get it if edited as it wont need changing // GET ONLY FUTURE DATES WHY CHANGE OLD ONES???
    $instances = SchoolService::getInstance($w)->GetObjects('SchoolClassInstance', ['status' => 'Scheduled', 'is_deleted' => 0, "class_data_id" => $p['class_data_id'], 'is_edited' => 0, "dt_class_date >= ? " => date('Y-m-d 00:00:00')]); // get only future dates




    foreach ($instances as $instance) {

        // Check if class start time or weekday is edited.
        // Check class start time against class instance
        $classTime = $class_data->getStartTime();  // showing wrong time.  when converting from $class_data->dt_class_date its correct


        // $dt_test = DateTime::createFromFormat('H:i', $classTime, new DateTimeZone('UTC'));
        // var_dump($dt_test);


        // var_dump($classTime);
        // die;

        $instanceTime = $instance->getStartTime();
        // Check if class time and instance time is the same.
        if ($classTime != $instanceTime) {
            //Now we know we need to change instance time to match the class time
            $newTime = DateTime::createFromFormat('d/m/Y H:i', $instance->GetFormattedDate() . " " . $classTime);

            // var_dump($newTime->format('Y-m-d H:i:s'));

            $formattedDate = $newTime->format('Y-m-d H:i:s');
            // die;
            $instance->dt_class_date = strtotime($formattedDate);
        }



        $classDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $class_data->dt_class_date);

        $classDayNo = $classDateTime->format('N');
        // var_dump($classDayNo);


        $instanceDateTime = DateTime::createFromFormat('d/m/Y H:i', $instance->GetFormattedDate() . $instance->getStartTime());

        $instanceDayNo = $instanceDateTime->format('N');

        // var_dump($instanceDayNo);

        $daysDifference = (int)$classDayNo - (int)$instanceDayNo;
        // var_dump($daysDifference);
        // die;



        // Now we need to check the day of the week of the instance and see if it matches the class
        if ($instanceDayNo != $classDayNo) {
            //Now we know we need to change the day of the week of the instance to match class time
            $instanceDateTime->modify($daysDifference . " days");
            // var_dump($instanceDateTime->format('Y-m-d H:i:s'));
            echo "<br>";
            // var_dump($instanceDateTime->format('N'));

            $formattedDate = $instanceDateTime->format('Y-m-d H:i:s');
        }
        // die;
        $instance->dt_class_date = strtotime($formattedDate);
        $instance->insertOrUpdate();
    }

    // die;



    $msg = "class data saved";

    $w->msg($msg, '/school-teacher/studentview/' . $student->id);
}
