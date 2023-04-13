<?php

require_once('composer/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

function renderInvoice_ALL(Web $w) {
    $p = $w->pathMatch('invoice_id');

    if (empty($p['invoice_id'])) {
        $w->error('No id provided', '/school-manager/invoices');
    }

    //get the invoice
    $invoice = SchoolService::getInstance($w)->getInvoiceForId($p['invoice_id']);
    if (empty($invoice)) {
        $w->error('No invoice found for id', '/school-manager/invoices');
    }

    //get the student and billing contact
    $student = $invoice->getStudent();
    $billingContact = $student->getBillingContact();

    //get invoice line items
    $invoiceLineItems = $invoice->getLineItems();

    //get the template
    $template = TemplateService::getInstance($w)->findTemplate('school','invoice');

    //prepare the data
    $templateData = [];
    $invoiceTotal = 0;
    $templateData['invoice_number'] = $invoice->invoice_number;
    $templateData['billing_contact_name'] = $billingContact->getFullName();
    $templateData['billing_contact_email'] = $billingContact->email;
    $templateData['student_name'] = $student->getContact()->getFullName();
    $templateData['invoice_lines'] = [];
    foreach ($invoiceLineItems as $lineItem) {
        $class_instance = $lineItem->GetClassInstance();
        $class_data = $class_instance->getClassData();
        $line = [];
        $line['session_date'] = $class_instance->GetFormattedDate();
        $line['duration'] = $class_data->duration;
        $line['rate'] = $class_data->rate;
        $line['total'] = $lineItem->amount;
        $templateData['invoice_lines'][] = $line;
        $invoiceTotal += $lineItem->amount;
    }
    $templateData['invoice_total'] = $invoiceTotal;



    // $w->header('Content-Description: File Transfer');
    // $w->header(
    //     'Content-Type: '
    //         . "application/octet-stream" 
    //         // . (empty($att->mimetype) ? "application/octet-stream" : $att->mimetype)
    // );
    // $w->header(
    //     'Content-Disposition: attachment; filename="'
    //         . 'test.pdf' . '"'
    //         // . ($saveAs ?? $att->filename) . '"'
    // );
    // $w->header('Expires: 0');
    // $w->header('Cache-Control: must-revalidate');
    // $w->header('Pragma: public');

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML($template->renderBody($templateData), true, false, true, false, '');

    $pdf->Output('example_006.pdf', 'D');









    //$w->out($template->renderBody($templateData));




}