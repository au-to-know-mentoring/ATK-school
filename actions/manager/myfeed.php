<?php

function myfeed_ALL(Web $w)
{

    $w->setLayout(null);
    $p = $w->pathMatch('teacher_id');
    $user = AuthService::getInstance($w)->user();
    $calSettings = SchoolService::getInstance($w)->getCalendarSettingsForUserId($user->id);
    $custom_calendar_settings = SchoolService::getInstance($w)->getCustomCalendarSettingsForUserId($user->id);
    $applySettings = false;
    // var_dump($calSettings); die;

    // foreach($calSettings as $mentorSettings)
    // {
    //     echo "<br><br>";
    //     var_dump($mentorSettings->teacher_id);
    //     echo "<br><br>";
    // } die;


    $calendarSettingsByTeacherId = [];

    foreach ($calSettings as $setting) {
        $calendarSettingsByTeacherId[$setting->teacher_id] = $setting;
    }


    // var_dump($calendarSettingsByTeacherId); die;
    $teacher_availability = [];

    if (empty($p['teacher_id'])) {
        $classes = SchoolService::getInstance($w)->GetAllClassDataForDateRange($_REQUEST);
        $teacher_availability = SchoolService::getInstance($w)->GetAllTeacherAvailability();
        // var_dump($teacher_availability); die;

        $applySettings = true;
    } else {
        $classes = SchoolService::getInstance($w)->GetAllClassDataForTeacherIdAndDateRange($p['teacher_id'], $_REQUEST);
        $teacher_availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForTeacherId($p['teacher_id']);
    }

    // echo "<pre>";
    // var_dump($classes);

    $class_instances = [];

    foreach ($classes as $class) {
        $ci = $class->GetInstanceForRange($_REQUEST);
        if (!empty($ci)) {
            $class_instances[] = $ci;
        }
    }

    // var_dump($class_instances);

    $calendarEvents = [];


    if (!empty($class_instances)) {
        foreach ($class_instances as $class_instance) {

            $is_visible = true;
            // echo "<pre>";
            // var_dump($class_instance);
            // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
            $class_data = $class_instance->getClassData();

            if ($applySettings) {
                $instance_teacher = $class_instance->getTeacher();
                if (!empty($calendarSettingsByTeacherId[$instance_teacher->id])) {
                    $is_visible =  $calendarSettingsByTeacherId[$instance_teacher->id]->is_view_class;
                }
            }

            if ($is_visible) {

                //$startDate =    
                // var_dump($class_data); die;
                if (empty($p['teacher_id'])) {
                    $start = date('Y-m-d H:i', $class_instance->dt_class_date);
                    $end = date('Y-m-d H:i', $class_instance->dt_class_date + ($class_data->duration * 60 * 60));
                } else {
                    $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['teacher_id']);
                    $dtz = new DateTimeZone($teacher->timezone);
                    //var_dump($class_instance->dt_class_date); die;
                    $time = new DateTime(null, $dtz);
                    //var_dump($time);
                    $time->setTimestamp($class_instance->dt_class_date);
                    $start = $time->format('Y-m-d H:i');
                    $time->setTimestamp($class_instance->dt_class_date + ($class_data->duration * 60 * 60));
                    $end = $time->format('Y-m-d H:i');
                    //var_dump($start); die;
                }



                //var_dump($time->format('T')); echo "<br>";
                // Us dumb Americans can't handle millitary time
                //ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';
                $teacher_id = $class_instance->getTeacher()->id;

                $event = [
                    'title' => $class_instance->getCalendarTitle(), // a property!
                    // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
                    // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
                    'start' => $start, // date('Y-m-d H:i', $class_instance->dt_class_date), // a property!
                    'end' => $end, //date('Y-m-d H:i', $class_instance->dt_class_date + ($class_data->duration * 60 * 60)),
                    'url' => '/school-teacher/viewclassinstance/' . $class_instance->id,
                    'className' => $class_instance->status . " " . "teacher_" . $teacher_id,
                ];
                $calendarEvents[] = $event;
            }
        }
    }

    //get availabiliity for teacher
    if (!empty($teacher_availability)) {
        foreach ($teacher_availability as $availability) {

            $is_visible = true;

            if ($applySettings) {

                if (!empty($calendarSettingsByTeacherId[$availability->object_id])) {

                    $is_visible =  $calendarSettingsByTeacherId[$availability->object_id]->is_view_availability;
                }
            }
            if ($is_visible) {

                $event = [
                    'title' => $availability->type . ' ' . SchoolService::getInstance($w)->GetTeacherForId($availability->object_id)->getFullName(), // a property!
                    'start' => $availability->getStartForCurrentWeek($_REQUEST), // a property!
                    'end' => $availability->getEndForCurrentWeek($_REQUEST),
                    'url' => '/school-teacher/editavailability/' . "teacher/" . $p['teacher_id'] . '/' .  $availability->id,  ///////////////
                    'className' => $availability->type,

                ];
                $calendarEvents[] = $event;
            }
        }
    }




























    // Should custom calendars be visibile
    // Check we are NOT viewing teacher calendar by viewing if path is empty 
    // Use apply settings variable to check if calendar event should be viewed by setting
    if ($applySettings) {
        // Check if there are settings
        if (!empty($custom_calendar_settings)) {
            // var_dump($custom_calendar_settings); die;

            // Loop through settings
            foreach ($custom_calendar_settings as $custom_calendar_setting) {
                $is_visible = $custom_calendar_setting->is_view_calendar;

                // Check if calendar is visible in settings get event
                if ($is_visible) {
                    // var_dump($_REQUEST); die;
                    $event_setting = SchoolService::getInstance($w)->getAllCustomCalendarEventsByCalendarIdAndRange($custom_calendar_setting->custom_calendar_id, $_REQUEST);
                    // var_dump($custom_calendar_setting); die;
                    var_dump($event_setting); die;
                }
            }
        }
    }




    // build events into array and add to calendar events array


    $w->out(json_encode($calendarEvents));
}
