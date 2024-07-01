<?php

function generateinvoices_GET(Web $w) {

    $user = AuthService::getInstance($w)->user();
    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $uninvoices_class_instances = SchoolService::getInstance($w)->getUninvoicedClasses();

    $session_form_array = [];
    if (!empty($uninvoices_class_instances)) {
        foreach ($uninvoices_class_instances as $session) {
            $session_form_array[] = [
                (new \Html\Form\InputField\Text())->setLabel('Session Date')->setValue(formatDate($session->dt_class_date, 'd/m/Y h:i'))->setName('sessiondate' . $session->id)->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Session Status')->setValue($session->status)->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Participant Name')->setValue($session->getStudent()->getContact()->getFullName())->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Mentor Name')->setValue($session->getTeacher()->getContact()->getFullName())->setDisabled(true),
                (new \Html\Form\InputField\Checkbox())->setLabel('Make Invoice')->setName('generateinvoice_' . $session->id)->setChecked(true)
            ];
        }
    }

    $form = [
        'Select Sessions to Invoice' => $session_form_array
    ];

    $w->out(Html::multiColForm($form, '/school-manager/generateinvoices', "POST", "Generate"));

}

function generateinvoices_POST(Web $w) {

    $user = AuthService::getInstance($w)->user();
    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }

    $uninvoices_class_instances = SchoolService::getInstance($w)->getUninvoicedClasses();
    $sessions_to_invoice_by_client = [];
    if (!empty($uninvoices_class_instances)) {
        foreach ($uninvoices_class_instances as $session) {
            if (array_key_exists('generateinvoice_' . $session->id, $_POST)) {
                //echo $session->id . '</br>';
                // check for client key in array
                $array_key = $session->getStudentArrayKey();
                if (array_key_exists($array_key, $sessions_to_invoice_by_client)) {
                    $sessions_to_invoice_by_client[$array_key][] = $session;
                } else {
                    $sessions_to_invoice_by_client[$array_key] = [];
                    $sessions_to_invoice_by_client[$array_key][] = $session;
                }

            }
        }
    }

    if (!empty($sessions_to_invoice_by_client)) {
        foreach($sessions_to_invoice_by_client as $clientKey => $client_sessions) {
            
            // create new invoice
            $invoice = new SchoolInvoice($w);

            $class_data = SchoolService::getInstance($w)->GetClassDataForId($client_sessions[0]->class_data_id);
            $student = SchoolService::getInstance($w)->GetClassDataForId($client_sessions[0]->class_data_id)->getStudent();

            
            //echo 'student rate set for ' . $student->getContact()->getFullName() . ' </br>';

            $invoice->student_id = $student->id;
            $invoice->status = 'New';
            $invoice->insertOrUpdate();
            $total_charge = 0;
            foreach ($client_sessions as $session) {
                // create new invoice line
                $class_data = SchoolService::getInstance($w)->GetClassDataForId($session->class_data_id);
                if (empty($class_data->rate) || $class_data->rate == 0) {
                    continue;
                }
                $invoice_line = new SchoolInvoiceLine($w);
                $invoice_line->invoice_id = $invoice->id;
                $invoice_line->class_instance_id = $session->id;
                //calculate amount = rate * duration
                $invoice_line->amount = $class_data->rate * $class_data->duration;
                $invoice_line->insertOrUpdate();
                $total_charge += $invoice_line->amount;
            }
            $invoice->total_charge = $total_charge;
            $invoice->Update();

        }
    }

    $w->msg('New Invoices Generated', '/school-manager/invoices');

    // echo "<pre>";
    // var_dump($sessions_to_invoice_by_client);

    
    
}