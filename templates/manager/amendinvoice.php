<?php

function amendinvoice_ALL(Web $w) {
    $p = $w->pathMatch('id');

    if (empty($p['id'])) {
        $w->error('No id provided', '/school-manager/invoices');
    }

    $invoice = SchoolService::getInstance($w)->getInvoiceForId($p['id']);

    if (empty($invoice)) {
        $w->error('No invoice found for id', '/school-manager/invoices');
    }

    //mark invoice as ammended 
    if ($invoice->is_ammended) {
        //invoice already ammended
    } else {
        $invoice->is_ammended = true;
    }

    //create edit form
    $invoice_data_form = [
        'Invoice details'=> [
            [
                (new \Html\Form\InputField\Text())->setLabel('Invoice Number')->setValue($invoice->id)->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Participant')->setValue($student->getContact()->getFullName())->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Date Created')->setValue(date('d/m/Y', $invoice->dt_created))->setDisabled(true)
            ]
        ]
    ];

    $invoice_lines = $invoice->getLineItems();
    $invoice_lines_form = [];
    foreach ($invoice_lines as $line) {
        $line_form = [
            (new \Html\Form\InputField\Text())->setLabel('Mentor')->setValue($session->getTeacher()->getContact()->getFullName()),
                (new \Html\Form\InputField\Text())->setLabel('Session Date')->setValue(date('d/m/Y',$session->dt_class_date)),
                (new \Html\Form\InputField\Text())->setLabel('Duration')->setValue($class_data->duration),
                (new \Html\Form\InputField\Text())->setLabel('Rate')->setValue($class_data->rate)
        ];
    }


}