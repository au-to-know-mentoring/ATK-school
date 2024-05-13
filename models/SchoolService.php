<?php

class SchoolService extends DbService {


    public function getAllInvoices() {
        return $this->GetObjects('SchoolInvoice',['is_deleted'=>0]);
    }

    public function getAllNewInvoices() {
        return $this->GetObjects('SchoolInvoice',['status' => 'new', 'is_deleted'=>0]);
    }

    public function getAllUnpaidInvoices() {
        return $this->GetObjects('SchoolInvoice',['status' => 'sent', 'is_deleted'=>0]);
    }

    public function getUninvoicedClasses() {
        //get 'active' class data
        $active_classes = $this->GetAllActiveClassData();
        // get class instances with 'completed' status
        $results = [];
        $completed_class_instances = [];
        $late_cancel_class_instances = [];
        foreach ($active_classes as $class_data) {
            $completed_instances = $this->GetObjects('SchoolClassInstance', ['class_data_id' => $class_data->id, 'status' => 'Completed', 'is_deleted' => 0]);
            if (!empty($completed_instances)) {
                foreach($completed_instances as $class_instance) {
                    if (!$class_instance->is_invoiced()) {
                        $results[] = $class_instance;
                    }
                }
            }
            $late_cancel_instances = $this->GetObjects('SchoolClassInstance', ['class_data_id' => $class_data->id, 'status' => 'Late Cancel', 'is_deleted' => 0]);
            if (!empty($late_cancel_instances)) {
                foreach($late_cancel_instances as $class_instance) {
                    if (!$class_instance->is_invoiced()) {
                        $results[] = $class_instance;
                    }
                }
            }
        }
        return $results;
    }

    public function getInvoiceLinkForClassInstanceId($class_instance_id) {
        return $this->getObject('SchoolInvoiceLine', ['class_instance_id' => $class_instance_id, 'is_deleted' => 0]);
    }

    public function getInvoiceForId($invoice_id) {
        return $this->getObject('SchoolInvoice', $invoice_id);
    }

    public function getInvoiceLineById($id) {
        return $this->getObject('SchoolInvoiceLine', $id);
    }

    public function getInvoiceLinesForInvoiceId($invoice_id) {
        return $this->getObjects('SchoolInvoiceLine', ['invoice_id' => $invoice_id, 'is_deleted' => 0]);
    }

    public function getInvoicesForFilter($student_id, $date_sent_range_start, $date_sent_range_end, $status = "Paid") {
        $where = [
            'is_deleted' => 0,
            'status' => $status,
            
        ];
        if (empty($student_id)) {
            return [];
        }

        if (!empty($student_id) && $student_id != 'all') {
            $where['student_id'] = $student_id;
        }
        if(!empty($date_sent_range_start)) {
            //$dt_from = new DateTime($date_sent_range_start,);
            $dt_from = new DateTime(str_replace('/', '-', $date_sent_range_start));
            //str_replace('/', '-', $_POST['start_date'])
            //echo $dt_from->format('Y-m-d H:i:s');
            $where['dt_paid >= ?'] = $dt_from->format('Y-m-d H:i:s'); // $dt_from->getTimestamp(); //
        }
        if(!empty($date_sent_range_end)) {
            $dt_to = new DateTime(str_replace('/', '-', $date_sent_range_end));
            $where['dt_paid <= ?'] = $dt_to->format('Y-m-d 23:59:59');
        }

        return $this->getObjects('SchoolInvoice', $where);
    }

    // returns all example item instances
    public function GetAllTeachers() {
        $teachers = $this->GetObjects('SchoolTeacher',['is_deleted'=>0]);
        $filtered_teachers = [];
        foreach ($teachers as $teacher) {
            $user = AuthService::getInstance($this->w)->getUser($teacher->user_id);
            if ($user->is_active) {
                $filtered_teachers[] = $teacher;
            }
        }
        return $filtered_teachers;
    }

    // returns a single example item matching the given id
    public function GetTeacherForId($id) {
        return $this->GetObject('SchoolTeacher',$id);
    }

    public function GetTeacherForUserId($user_id) {
        return $this->GetObject('SchoolTeacher',['is_deleted'=> 0, 'user_id' => $user_id]);
    }

    // returns all example item instances
    public function GetAllStudents() {
        $user = AuthService::getInstance($this->w)->user();
        if ($user->hasRole('school_manager')) {
            return $this->GetObjects('SchoolStudent',['is_deleted'=>0]);
        }
        if ($user->hasRole('school_teacher')) {
            $teacher = $this->GetTeacherForUserId($user->id);
            return $this->GetAllStudentsForTeacherId($teacher->id);   
        }
        return null;
    }

    // returns a single example item matching the given id
    public function GetStudentForId($id) {
        return $this->GetObject('SchoolStudent',$id);
    }

    public function getStudentSelectOptions() {
        $students = $this->GetAllStudents();
        $options = [];
        $options[] = ["All Participants", "all"];
        foreach ($students as $student) {
            $options[] = [$student->getFullName(), $student->id];
        }
        return $options;
    }

    public function GetAllStudentsForTeacherId($teacher_id) {
        $teachersclasses = $this->GetAllClassDataForTeacherId($teacher_id);
        if (empty($teachersclasses)) {
            return null;
        }

        $students = [];

        foreach ($teachersclasses as $classdata) {
            $students[] = $this->GetStudentForId($classdata->student_id);
        }

        return $students;
    }

    public function GetAllClassData() {
        $user = AuthService::getInstance($this->w)->user();
        if ($user->hasRole('school_manager')) {
            return $this->GetObjects('SchoolClassData',['is_deleted'=>0]);
        }
        if ($user->hasRole('school_teacher')) {
            $teacher = $this->GetTeacherForUserId($user->id);
            return $this->GetAllClassDataForTeacherId($teacher->id);   
        }
        return null;
        
    }

    public function GetAllActiveClassData() {
        return $this->GetObjects('SchoolClassData',['status' => 'active', 'is_deleted'=>0]);
    }

    // returns a single example item matching the given id
    public function GetClassDataForId($id) {
        return $this->GetObject('SchoolClassData',$id);
    }

    public function GetClassDataForStudentId($student_id) {
        return $this->getObjects('SchoolClassData', ['is_deleted'=>0, 'student_id'=> $student_id]);
    }

    public function GetAllClassDataForTeacherId($teacher_id) {
        return $this->getObjects('SchoolClassData', ['is_deleted'=>0, 'teacher_id'=> $teacher_id]);
    }

    public function GetAllClassDataForDateRange($dateArray) {
        return $this->getObjects('SchoolClassData',['is_deleted'=>0,
            //'status'=>'active',
            
            'dt_class_date <= ?'=>date('Y-m-d 00:00:00',strtotime($dateArray['end']))]);
    }

    public function GetAllClassDataForTeacherIdAndDateRange($teacher_id,$dateArray) {
        return $this->getObjects('SchoolClassData',['is_deleted'=>0,
            'status'=>'active',
            'teacher_id'=>$teacher_id,
            'dt_class_date <= ?'=>date('Y-m-d 00:00:00',strtotime($dateArray['end']))]);
    }

    // returns all example item instances
    public function GetAllClassInstances() {
        return $this->GetObjects('SchoolClassInstance',['is_deleted'=>0]);
    }

    // returns a single example item matching the given id
    public function GetClassInstancesForId($id) {
        return $this->GetObject('SchoolClassInstance',$id);
    }

    public function getPastClassInstancesByStatus($status) {
        //'dt_class_date <= ?'=>date('Y-m-d 00:00:00',strtotime($dateArray['end']))
        $date = date('Y-m-d 00:00:00', Time());

        return $this->getObjects('SchoolClassInstance', ['is_deleted'=>0, 'status'=>$status, 'dt_class_date <= ?'=>$date]);
    }

    public function GetAllStudentContactsForStudentId($student_id) {
        return $this->GetObjects("SchoolStudentContactMapping",['is_deleted'=>0,'student_id'=>$student_id]);
    }

    public function GetStudentContactMappingForId($mapping_id) {
        return $this->GetObject('SchoolStudentContactMapping', $mapping_id);
    }

    public function GetAllTeacherAvailability() {
        return $this->GetObjects('SchoolTeacherAvailability',['is_deleted'=>0]);
    }

    public function GetTeacherAvailabilityForId($id) {
        return $this->GetObject('SchoolTeacherAvailability',$id);
    }

    public function GetTeacherAvailabilityForTeacherId($teacher_id) {
        return $this->GetObjects('SchoolTeacherAvailability',['is_deleted'=>0, 'object_type' => 'teacher', 'object_id'=> $teacher_id]);
    }

    public function GetSettingsForUserId($user_id) {
        return $this->GetObject('SchoolManagerSettings', ['is_deleted' => 0, 'user_id'=> $user_id]);
    }

    public function GetZoomAccounts() {
        return $this->GetObjects('SchoolZoomAccount',['is_deleted'=>0]);
    }

    public function GetZoomAccountForId($id) {
        return $this->GetObject("SchoolZoomAccount", $id);
    }

    public function GetStateSelectOptions() {
        $states = ['NSW','ACT','QLD', 'VIC', 'TAS','SA','NT','WA'];
        return $states;
    }

    public function GetTimeZoneSelectOptions() {
        // $TZ = [
        //     ["(GMT -12:00) Eniwetok, Kwajalein","-12:00"],
        //     ["(GMT -11:00) Midway Island, Samoa","-11:00"],
        //     ["(GMT -10:00) Hawaii","-10:00"],
        //     ["(GMT -9:30) Taiohae","-09:50"],
        //     ["(GMT -9:00) Alaska","-09:00"],
        //     ["(GMT -8:00) Pacific Time (US &amp; Canada)","-08:00"],
        //     ["(GMT -7:00) Mountain Time (US &amp; Canada)","-07:00"],
        //     ["(GMT -6:00) Central Time (US &amp; Canada), Mexico City","-06:00"],
        //     ["(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima","-05:00"],
        //     ["(GMT -4:30) Caracas","-04:50"],
        //     ["(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz","-04:00"],
        //     ["(GMT -3:30) Newfoundland","-03:50"],
        //     ["(GMT -3:00) Brazil, Buenos Aires, Georgetown","-03:00"],
        //     ["(GMT -2:00) Mid-Atlantic","-02:00"],
        //     ["(GMT -1:00) Azores, Cape Verde Islands","-01:00"],
        //     ["(GMT) Western Europe Time, London, Lisbon, Casablanca","+00:00"],
        //     ["(GMT +1:00) Brussels, Copenhagen, Madrid, Paris","+01:00"],
        //     ["(GMT +2:00) Kaliningrad, South Africa","+02:00"],
        //     ["(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg","+03:00"],
        //     ["(GMT +3:30) Tehran","+03:50"],
        //     ["(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi","+04:00"],
        //     ["(GMT +4:30) Kabul","+04:50"],
        //     ["(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent","+05:00"],
        //     ["(GMT +5:30) Bombay, Calcutta, Madras, New Delhi","+05:50"],
        //     ["(GMT +5:45) Kathmandu, Pokhara","+05:75"],
        //     ["(GMT +6:00) Almaty, Dhaka, Colombo","+06:00"],
        //     ["(GMT +6:30) Yangon, Mandalay","+06:50"],
        //     ["(GMT +7:00) Bangkok, Hanoi, Jakarta","+07:00"],
        //     ["(GMT +8:00) Beijing, Perth, Singapore, Hong Kong","+08:00"],
        //     ["(GMT +8:45) Eucla","+08:75"],
        //     ["(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk","+09:00"],
        //     ["(GMT +9:30) Adelaide, Darwin","+09:50"],
        //     ["(GMT +10:00) Eastern Australia, Guam, Vladivostok","+10:00"],
        //     ["(GMT +10:30) Lord Howe Island","+10:50"],
        //     ["(GMT +11:00) Magadan, Solomon Islands, New Caledonia","+11:00"],
        //     ["(GMT +11:30) Norfolk Island","+11:50"],
        //     ["(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka","+12:00"],
        //     ["(GMT +12:45) Chatham Islands","+12:75"],
        //     ["(GMT +13:00) Apia, Nukualofa","+13:00"],
        //     ["(GMT +14:00) Line Islands, Tokelau","+14:00"]
        // ];
        
        $regions = array(
            'Australia' => DateTimeZone::AUSTRALIA,
            //'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            //'Antarctica' => DateTimeZone::ANTARCTICA,
            //'Aisa' => DateTimeZone::ASIA,
            //'Atlantic' => DateTimeZone::ATLANTIC,
            //'Europe' => DateTimeZone::EUROPE,
            //'Indian' => DateTimeZone::INDIAN,
            //'Pacific' => DateTimeZone::PACIFIC
        );
        
        $timezones = array();
        foreach ($regions as $name => $mask)
        {
            $zones = DateTimeZone::listIdentifiers($mask);
            foreach($zones as $timezone)
            {
                // Lets sample the time there right now
                $dtz = new DateTimeZone($timezone);
                
                $time = new DateTime("now", $dtz);
                //var_dump($time->format('T')); echo "<br>";
                // Us dumb Americans can't handle millitary time
                $ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';
        
                // Remove region name and add a sample time
                $timezones[] = [$timezone . ' ' . $time->format('T') . ' - ' . $time->format('H:i') . $ampm, $timezone];
            }
        }
        // echo "<pre>";
        // var_dump($timezones); die;
        return $timezones;
        //return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        // return $TZ;
    }

    public function navigation(Web $w, $title = null, $nav = null)
    {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : [];

        


        if (AuthService::getInstance($w)->loggedIn()) {
            $user = AuthService::getInstance($w)->user();
            // manager menu links
            if ($user->hasRole('school_manager')) {
                $w->menuLink("/school-manager/calendar", "Calendar", $nav);
                $w->menuLink("/school-manager/teacheredit", "New Mentor", $nav);
                $w->menuLink("/school-manager/studentedit", "New Participant", $nav);
                $w->menuLink("/school-manager/teacherlist", "View Mentors", $nav);
                //$w->menuLink("/school-manager/classdataedit", "Add New Class", $nav);
                $w->menuLink("/school-teacher/studentlist", "View Participants", $nav);
                $w->menuLink("/school-manager/settings", "Change Settings", $nav);
                $w->menuLink("/school-manager/invoices", "Invoices", $nav);
                $w->menuLink("/school-manager/viewzoomaccounts", "Zoom Accounts", $nav);
            } elseif ($user->hasRole('school_teacher')) {
                $w->menuLink("/school-teacher/teachercalendar", "Calendar", $nav);
                $w->menuLink("/school-teacher/studentlist", "My Participants", $nav);
                $w->menuLink("/school-teacher/editavailability", "Add New Availability", $nav);
            }
            
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }




}