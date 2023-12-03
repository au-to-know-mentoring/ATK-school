<?php

function teacheredit_GET(Web $w)
{
    

    $p = $w->pathMatch("id");
    if (!empty($p['id'])) {
        $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['id']);
        $user = $teacher->getUser();
        $contact = $user->getContact();
        $w->ctx("title","Edit Mentor");
    } else {
        $teacher = new SchoolTeacher($w);
        $user = new User($w);
        $contact = new Contact($w);
        $w->ctx("title","Add Mentor");
    }

    //
    $loggedInUser = AuthService::getInstance($w)->User();
    if ($loggedInUser->is_admin == 1) {
        $form = [
            "Auto Complete" => [
                [
                    ["Auto Complete", "checkbox", "autocheck"],
                ]
                ],
            "User Details" => [
                [
                    ["Login", "text", "login", $user->login],
                ],[
                    
                    ["Password (leave blank to keep existing password)", "password", "password"],
                    ["Repeat Password", "password", "password2"],
                ]
            ],
            "Contact Details" => [
                [
                    ["First Name", "text", "firstname", $contact->firstname],
                    ["Last Name", "text", "lastname", $contact->lastname]
                ],
                [
                    ["Home Phone", "text", "homephone", $contact->homephone],
                    ["Mobile", "text", "mobile", $contact->mobile],
                    ["Email", "text", "email", $contact->email]
                ]
            ],  
            "Mentor Working Checks" => [
               [
                    ["Certificate Number", "text", "input_certnumber", $teacher->input_certnumber],
                    ["Date Acquired", "date", "d_acquired_date", $teacher->getDateAcquired()],
             
                ]
                ],
            "Mentor Data" => [
                [
                    (new \Html\Form\InputField\Number())->setLabel("Max Student Capacity")->setName("max_students")->setValue($teacher->max_students)
                    
                ],
                [
                    ['State', 'select', 'state', $teacher->state, SchoolService::getInstance($w)->GetStateSelectOptions()],
                    ['TimeZone', 'select', 'timezone', $teacher->timezone, SchoolService::getInstance($w)->GetTimeZoneSelectOptions()]
                ]
    
                ],
          
        ]; 
    } 
    // if user is not admin then don't show the auto complete checkbox
    else { $form = [
        "User Details" => [
            [
                ["Login", "text", "login", $user->login],
            ],[
                
                ["Password (leave blank to keep existing password)", "password", "password"],
                ["Repeat Password", "password", "password2"],
            ]
        ],
        "Contact Details" => [
            [
                ["First Name", "text", "firstname", $contact->firstname],
                ["Last Name", "text", "lastname", $contact->lastname]
            ],
            [
                ["Home Phone", "text", "homephone", $contact->homephone],
                ["Mobile", "text", "mobile", $contact->mobile],
                ["Email", "text", "email", $contact->email]
            ]
        ],  
        "Mentor Working Checks" => [
           [
                ["Certificate Number", "text", "input_certnumber", $teacher->input_certnumber],
                ["Date Acquired", "date", "d_acquired_date", $teacher->getDateAcquired()],
         
            ]
            ],
        "Mentor Data" => [
            [
                (new \Html\Form\InputField\Number())->setLabel("Max Student Capacity")->setName("max_students")->setValue($teacher->max_students)
                
            ],
            [
                ['State', 'select', 'state', $teacher->state, SchoolService::getInstance($w)->GetStateSelectOptions()],
                ['TimeZone', 'select', 'timezone', $teacher->timezone, SchoolService::getInstance($w)->GetTimeZoneSelectOptions()]
            ]

            ],
      
    ];}
    // 
    
     

    $post_url = '/school-manager/teacheredit';
    if (!empty($p['id'])) {
        $post_url .= "/" . $teacher->id;
    }

    $w->out(Html::multiColForm($form, $post_url));

}

function teacheredit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    
    if (!empty($p['id'])) {
        $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['id']);
        $user = $teacher->getUser();
        $contact = $user->getContact();
    } else {
        $teacher = new SchoolTeacher($w);
        $user = new User($w);
        $contact = new Contact($w);
    }
    
    $errors = [];
   
    if ($_REQUEST['password2'] != $_REQUEST['password']) {
        $errors[] = "Passwords don't match";
    }
     if (sizeof($errors) != 0) {
         $w->error(implode("<br/>\n", $errors), "/school-manager/teacherlist");
     }
    
   // DO NOT PUSH
        // Variables for automatically filling data fields
        $randomINT = random_int(1,99);
     $teacherLogin = "Mentor" . random_int(1,99);
     $firstName = "First Name" . $randomINT;
     $lastName = "Last Name" . $randomINT;

     $homePhoneNUM = "04" . random_int(1000,9999);
     $mobilePhoneNUM = "04" . random_int(1000,9999);
     $email = $contact->firstname . "@email.com"; 
    
    
    $loggedInUser = AuthService::getInstance($w)->User();
    if ($loggedInUser ->is_admin == 1 && !empty($_REQUEST['autocheck'])) 
    {
        // CONTACT
        $contact->fill($_POST);
        // Contact Details
        $contact->firstname = $firstName;
        $contact->lastname = $lastName;
        $contact->homephone = $homePhoneNUM;
        $contact->mobile = $mobilePhoneNUM;
        $contact->email = $email;
        $contact->insertOrUpdate();
        // USER
        $hasPass = $user->password;
            if (empty($_REQUEST['password'])){
                $user->fill($_POST);
                $user->password = $hasPass; 
            }
        // User Details
        $user->login = $teacherLogin;
        $user->contact_id = $contact->id;
        $user->is_active = true;
        $user->insertOrUpdate();
        // TEACHER
        $user->addRole('school_teacher');
        $user->addRole('user');
       
        $teacher->user_id = $user->id;
        $teacher->fill($_POST);
        
        // Mentor Working Data input fields
        $teacher->input_certnumber = random_int(1000000,9999999);
        $teacher->d_acquired_date = date("y:d:m"); 
        // Mentor Data
        $teacher->max_students = random_int(1,10);
        $teacher->state = "NSW";
        $teacher->timezone = "Australia/Sydney";
    
        
    } else { 
        // CONTACT
        $contact->fill($_POST);
        $contact->insertOrUpdate();
        // USER
        $hasPass = $user->password;
        if (empty($_REQUEST['password'])){
            $user->fill($_POST);
            $user->password = $hasPass;// if password field is empty, user will maintain current password
        } else {$user->setPassword($_POST['password']);} 

        $user->contact_id = $contact->id;
        $user->is_active = true;
        $user->insertOrUpdate();

        $user->addRole('school_teacher');
        $user->addRole('user');
        // Teacher
        $teacher->user_id = $user->id;
        $teacher->fill($_POST);
    } 
           

    $teacher->insertOrUpdate();

    if (!empty($p['id'])) {
        $msg = "Teacher Details Updated";
    } else {
        $msg = "New Teacher Saved";
    }

    $w->msg($msg, "/school-manager/teacherlist");

}