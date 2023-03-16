<?php

function editInvoiceLine_GET(Web $w) {
    $p = $w->pathMatch('line_id');

    $invoice_line = SchoolService::getInstance($w)->getInvoiceLineById($p['line_id']);
    if (empty($invoice_line)) {
        $w->error('No invoice line item found for id', '/school-manager/invoices');
    }

    $invoice = SchoolService::getInstance($w)->getInvoiceForId($invoice_line->invoice_id);
    if (empty($invoice)) {
        $w->error('No invoice found for invoice line', '/school-manager/invoices');
    }

    if ($invoice->status != "New") {
        $w->error('Invoice is sent. Do not edit', '/school-manager/invoices');
    }

    $student = $invoice->getStudent();
    $session = SchoolService::getInstance($w)->GetClassInstancesForId($invoice_line->class_instance_id);
    $class_data = $session->getClassData();

    $form = [
        'Invoice Details' => [
            [
                (new \Html\Form\InputField\Text())->setLabel('Invoice Number')->setValue($invoice->id)->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Participant')->setValue($student->getContact()->getFullName())->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Date Created')->setValue(date('d/m/Y', $invoice->dt_created))->setDisabled(true)
            ]
            ],
        'Line Item Details' => [
            [
                (new \Html\Form\InputField\Text())->setLabel('Mentor')->setValue($session->getTeacher()->getContact()->getFullName())->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Session Date')->setValue(date('d/m/Y',$session->dt_class_date))->setDisabled(true)
            ],
            [
                (new \Html\Form\InputField\Text())->setLabel('Duration')->setValue($class_data->duration)->setDisabled(true),
                (new \Html\Form\InputField\Text())->setLabel('Rate')->setValue($class_data->rate)->setDisabled(true)
                
            ]
        ],
        'Amount' => [
            [
                (new \Html\Form\InputField())->setType('decimal')->setLabel('Amount')->setName('amount')->setValue($invoice_line->amount)
            ]
        ]
    ];
    
    $w->ctx('lineitemform', Html::multiColForm($form, '/school-manager/editInvoiceLine/' . $invoice_line->id));

}

function editInvoiceLine_POST(Web $w) {
    $p = $w->pathMatch('line_id');

    $invoice_line = SchoolService::getInstance($w)->getInvoiceLineById($p['line_id']);
    if (empty($invoice_line)) {
        $w->error('No invoice line item found for id', '/school-manager/invoices');
    }

    $invoice_line->amount = $_POST['amount'];
    $invoice_line->Update();
    //update invoice total
    $invoice = SchoolService::getInstance($w)->getInvoiceForId($invoice_line->invoice_id);
    $invoice->updateTotal();

    $w->msg('Invoice Line Updated', '/school-manager/viewInvoice/' . $invoice_line->invoice_id);
}