<?php

function studentedit_GET(Web $w) {
    
    $p = $w->pathMatch("id");
    if (!empty($p['id'])) {
        $student = SchoolService::getInstance($w)->GetStudentForId($p['id']);
        $student_contact = $student->getContact();
        $main_contact = $student->getMainContact();
        $w->ctx("title","Edit Student");
    } else {
        $student = new SchoolStudent($w);
        $student_contact = new Contact($w);
        $main_contact = new Contact($w);
        $w->ctx("title","Add Student");
    }

    $loggedInUser = AuthService::getInstance($w)->User();
    if ($loggedInUser->is_admin == 1) {
    $form = [
        "Auto Complete" => [
            [
                ["Auto Complete", "checkbox", "autocheck"],
            ]
        ],
        "Student Details" => [
            [
                ["First Name", "text", "student_firstname", $student_contact->firstname],
                ["Last Name", "text", "student_lastname", $student_contact->lastname]
            ],
            [
                ["Home Phone", "text", "student_homephone", $student_contact->homephone],
                ["Mobile", "text", "student_mobile", $student_contact->mobile],
                ["Email", "text", "student_email", $student_contact->email]
            ]
        ],
        "Main Contact Details" => [
            [
                ["First Name", "text", "main_contact_firstname", $main_contact->firstname],
                ["Last Name", "text", "main_contact_lastname", $main_contact->lastname]
            ],
            [
                ["Home Phone", "text", "main_contact_homephone", $main_contact->homephone],
                ["Mobile", "text", "main_contact_mobile", $main_contact->mobile],
                ["Email", "text", "main_contact_email", $main_contact->email]
            ]
        ],
        "Other details" => [
            [
                ['State', 'select', 'state', $student->state, SchoolService::getInstance($w)->GetStateSelectOptions()],
                ['TimeZone', 'select', 'timezone', $student->timezone, SchoolService::getInstance($w)->GetTimeZoneSelectOptions()],
                
            ]
        ]
    ];} else {
        $form = [
            "Student Details" => [
                [
                    ["First Name", "text", "student_firstname", $student_contact->firstname],
                    ["Last Name", "text", "student_lastname", $student_contact->lastname]
                ],
                [
                    ["Home Phone", "text", "student_homephone", $student_contact->homephone],
                    ["Mobile", "text", "student_mobile", $student_contact->mobile],
                    ["Email", "text", "student_email", $student_contact->email]
                ]
            ],
            "Main Contact Details" => [
                [
                    ["First Name", "text", "main_contact_firstname", $main_contact->firstname],
                    ["Last Name", "text", "main_contact_lastname", $main_contact->lastname]
                ],
                [
                    ["Home Phone", "text", "main_contact_homephone", $main_contact->homephone],
                    ["Mobile", "text", "main_contact_mobile", $main_contact->mobile],
                    ["Email", "text", "main_contact_email", $main_contact->email]
                ]
            ],
            "Other details" => [
                [
                    ['State', 'select', 'state', $student->state, SchoolService::getInstance($w)->GetStateSelectOptions()],
                    ['TimeZone', 'select', 'timezone', $student->timezone, SchoolService::getInstance($w)->GetTimeZoneSelectOptions()],
                    
                ]
            ]
        ]; 
    }

    $post_url = '/school-manager/studentedit';
    if (!empty($p['id'])) {
        $post_url .= "/" . $student->id;
    }

    $w->out(Html::multiColForm($form, $post_url));

}

function studentedit_POST(Web $w) {

//var_dump($_POST); die;

    $p = $w->pathMatch("id");
    if (!empty($p['id'])) {
        $student = SchoolService::getInstance($w)->GetStudentForId($p['id']);
        $student_contact = $student->getContact();
        $main_contact_mapping = $student->getMainCOntactMapping();
        $main_contact = $main_contact_mapping->getContact();
        $w->ctx("title","Edit Student");
    } else {
        $student = new SchoolStudent($w);
        $student_contact = new Contact($w);
        $main_contact = new Contact($w);
        $main_contact_mapping = new SchoolStudentContactMapping($w);
        $w->ctx("title","Add Student");
    }


    // AUTOMATION VARIABLES
        // Student contact data variables
    $randomNUM = rand(1,99);
    $studentfirstName = "StudentFirstName" . $randomNUM;
    $studentlastName = "StudentLastName" . $randomNUM;
    $studenthomePhoneNUM = "04" . rand(1000,9999);
    $studentmobileNum = "04" . rand(1000,9999);
    $studentemail = $student_contact->firstname . "@email.com";
        // Student Data Variables
    $state = "NSW";
    $timezone = "Australia/Sydney";
        // Main Contact Mapping
    $randomNUM = rand(1,99);
    $firstName = "First Name" . $randomNUM;
    $lastName = "Last Name" . $randomNUM;
    $homePhoneNUM = "04" . rand(1000,9999);
    $mobileNum = "04" . rand(1000,9999);
    $email = $main_contact->firstname . "@email.com";
    
    // Automatically fill in all form data
    $loggedInUser = AuthService::getInstance($w)->User();
    if ($loggedInUser ->is_admin == 1 && !empty($_REQUEST['autocheck'])) 
    {
        $student_contact->firstname = $studentfirstName;
        $student_contact->lastname = $studentlastName;
        $student_contact->homephone = $studenthomePhoneNUM;
        $student_contact->mobile = $studentmobileNum;
        $student_contact->email = $studentemail;

        $student_contact->insertOrUpdate();
        
        //add the contact id to the student object
        $student->contact_id = $student_contact->id;
        $student->state = $state;
        $student->timezone = $timezone;
        //$student->rate = $_POST['rate'];
        $student->insertOrUpdate();

        //fill the main contact details
        $main_contact->firstname = $firstName;
        $main_contact->lastname = $lastName;
        $main_contact->homephone = $homePhoneNUM;
        $main_contact->mobile = $mobileNum;
        $main_contact->email = $email;
        $main_contact->insertOrUpdate();
    } else {
        //fill the student's contact details
        $student_contact->firstname = $_POST['student_firstname'];
        $student_contact->lastname = $_POST['student_lastname'];
        $student_contact->homephone = $_POST['student_homephone'];
        $student_contact->mobile = $_POST['student_mobile'];
        $student_contact->email = $_POST['student_email'];

        $student_contact->insertOrUpdate();
        
        //add the contact id to the student object
        $student->contact_id = $student_contact->id;
        $student->state = $_POST['state'];
        $student->timezone = $_POST['timezone'];
        //$student->rate = $_POST['rate'];
        $student->insertOrUpdate();

        //fill the main contact details
        $main_contact->firstname = $_POST['main_contact_firstname'];
        $main_contact->lastname = $_POST['main_contact_lastname'];
        $main_contact->homephone = $_POST['main_contact_homephone'];
        $main_contact->mobile = $_POST['main_contact_mobile'];
        $main_contact->email = $_POST['main_contact_email'];
        $main_contact->insertOrUpdate();
    }
   

    //store the student contact mapping 
    if (empty($p['id'])) {
        $main_contact_mapping->student_id = $student->id;
        $main_contact_mapping->contact_id = $main_contact->id;
        $main_contact_mapping->is_main_contact = true;
        $main_contact_mapping->insertOrUpdate();
    }

    if (!empty($p['id'])) {
        $msg = "Student Details Updated";
    } else {
        $msg = "New Student Saved";
    }

    $w->msg($msg, "/school-teacher/studentlist");

}