<?php

function viewInvoice_ALL(Web $w) {

    $w->ctx('title', 'View Invoice');

    $p = $w->pathMatch('id');

    if (empty($p['id'])) {
        $w->error('No id provided', '/school-manager/invoices');
    }

    $invoice = SchoolService::getInstance($w)->getInvoiceForId($p['id']);

    if (empty($invoice)) {
        $w->error('No invoice found for id', '/school-manager/invoices');
    }

    $w->ctx('invoice_id', $invoice->id);

    $student = $invoice->getStudent();
    $billing_contact = $student->getBillingContact();
    $billing_contact_mapping = $student->getBillingContactMapping();

    //invoice data table
    
    $invoice_data = [
        'Invoice Details' => [
            [
                ['Invoice Number', 'text', 'invoice_number', $invoice->id],
                ['Participant Name', 'text', 'participant_name', Html::a('/school-manager/studentview/' . $student->id, $student->getContact()->getFullName())],
                ['Status', 'text', 'invoice_status', $invoice->status],
                ['Total Amount', 'text', 'totale_charge', formatCurrency($invoice->total_charge)],
                ['Date Sent', 'text', 'dt_sent', ($invoice->status != 'New') ? date('d/m/Y', $invoice->dt_sent) : 'Not Sent'],
                ['Date Paid', 'text', 'dt_paid', ($invoice->status == 'Paid') ? date('d/m/Y', $invoice->dt_paid) : 'Not Paid']
            ]
        ],
        'Billing Contact' => [
            [
                ["Name", "text", "billing_contact_name", $billing_contact->getFullName()],
                ["Home Phone", "text", "billing_contact_homephone", $billing_contact->homephone],
                ["Mobile", "text", "billling_contact_mobile", $billing_contact->mobile],
                ["Email", "text", "billing_contact_email", $billing_contact->email],
                ['Notes', 'text', 'notes', $billing_contact_mapping->notes]
            ]
        ]
    ];

    $w->ctx('invoice_data', Html::multiColTable($invoice_data));





    $invoice_lines = $invoice->getLineItems();

    $lines_table = [];
    $lines_table_headers = ['Class Date', 'Mentor', 'Duration', 'Amount', 'Rate', 'Actions'];
    if (!empty($invoice_lines)) {
        foreach($invoice_lines as $line) {
            $session = SchoolService::getInstance($w)->GetClassInstancesForId($line->class_instance_id);
            $class_data = $session->getClassData();
            $row = [];
            $row[] = date('d/m/Y h:i', $session->dt_class_date);
            $row[] = $session->getTeacher()->getContact()->getFullName();
            $row[] = $class_data->duration;
            
            $row[] = $line->amount;
            $row[] = $class_data->rate;
            $actions = [];
            $actions[] = Html::b('/school-manager/editInvoiceLine/' . $line->id, 'Edit');

            $row[] = implode(" ", $actions);
            $lines_table[] = $row;
        }
    }

    $w->ctx('lines_table', Html::table($lines_table, null, 'tablesorter', $lines_table_headers));

}