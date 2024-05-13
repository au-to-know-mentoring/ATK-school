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
    $w->ctx("invoice", $invoice);

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
    $lines_table_headers = ['Class Date', 'Mentor', 'Duration',  'Rate', 'Amount', 'Actions'];
    if (!empty($invoice_lines)) {
        foreach($invoice_lines as $line) {
            $session = SchoolService::getInstance($w)->GetClassInstancesForId($line->class_instance_id);
            $class_data = $session->getClassData();
            $row = [];
            $row[] = date('d/m/Y h:i', $line->dt_class_date);
            $row[] = $session->getTeacher()->getContact()->getFullName();
            $row[] = $line->duration;
            $row[] = $line->rate;
            $row[] = $line->amount;
            
            $actions = [];
            $actions[] = Html::b('/school-manager/editInvoiceLine/' . $line->id, 'Edit');

            $row[] = implode(" ", $actions);
            $lines_table[] = $row;
        }
    }

    $w->ctx('lines_table', Html::table($lines_table, null, 'tablesorter', $lines_table_headers));

    //get attachment id
    $attachment_id = '';
    //get attachments
    $attachments = FileService::getInstance($w)->getAttachments($invoice);
    //var_dump($attachments); die;
    if (empty($attachments)) {
        //$w->error("Please generate and check invoice PDF", "school-manager/invoices/" . $invoice->id);
    }

    $templateInvoiceNumber = '';

    if (Count($attachments) > 1) {
        $invoice_pdf_count = 0;
        $invoice_filename = "Invoice_";
        //$invoice->invoice_number     00000     00001   00100
        $formattedInvoiceNumber = (string) $invoice->id;
        
        for ($i = 0; $i < 5 - strlen($formattedInvoiceNumber); $i++) {
            $invoice_filename .= "0";
            $templateInvoiceNumber .= '0';
        }
        $templateInvoiceNumber .= $invoice->id;

        $invoice_filename .= $invoice->id . ".pdf";
        foreach ($attachments as $attachment) {
            if ($attachment->filename == $invoice_filename) {
                $attachment_id = $attachment->id;
                break;
                //$invoice_pdf_count +=1;
            }
        }
        if ($invoice_pdf_count != 1) {
            $w->error("Incorrect number of Invoice PDFs found. Please delete unwanted PDFs or generate a PDF", "school-manager/viewInvoice/" . $invoice->id);
        }
        
    } else { 
        if (Count($attachments) == 1) {
            $attachment_id = $attachments[0]->id;
        }
        
    }
    $w->ctx('attachment_id', $attachment_id);


}