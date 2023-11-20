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

    $form = [
        "User Details" => [
            [
                ["Login", "text", "login", $user->login],
            ],[
                ["Password", "password", "password"],
                ["Repeat Password", "password", "password2"]
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
        "Mentor Data" => [
            [
                (new \Html\Form\InputField\Number())->setLabel("Max Student Capacity")->setName("max_students")->setValue($teacher->max_students)
                
            ],
            [
                ['State', 'select', 'state', $teacher->state, SchoolService::getInstance($w)->GetStateSelectOptions()],
                ['TimeZone', 'select', 'timezone', $teacher->timezone, SchoolService::getInstance($w)->GetTimeZoneSelectOptions()]
            ]

        ]
    ];

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

    $contact->fill($_POST);

    $contact->insertOrUpdate();

    $user->fill($_POST);
    $user->contact_id = $contact->id;
    $user->setPassword($_POST['password'], false);
    $user->is_active = true;
    $user->insertOrUpdate();
    // need to add roles for teacher
    $user->addRole('school_teacher');
    $user->addRole('user');

    $teacher->user_id = $user->id;
    $teacher->fill($_POST);

    $teacher->insertOrUpdate();

    if (!empty($p['id'])) {
        $msg = "Teacher Details Updated";
    } else {
        $msg = "New Teacher Saved";
    }

    $w->msg($msg, "/school-manager/teacherlist");

}