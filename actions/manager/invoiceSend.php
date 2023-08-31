<?php

use Laminas\Validator\File\Count;

function invoiceSend_ALL(Web $w) {

    //check if user is a manager
    $user = AuthService::getInstance($w)->user();
    if (!$user->hasRole("school_manaer")) {
        $w->error("you do not have permission to complate this action", "/school");
    }
    
    $p = $w->pathMatch("invoice_id");
    if (empty($p['invoice_id'])) {
        $w->error("no invoice id provided", "school-manager/invoices");
    }

    $invoice = SchoolService::getInstance($w)->getInvoiceForId($p['invoice_id']);
    if (empty($invoice)) {
        $w->error("no invoice found for id", "school-manager/invoices");
    }

    //get attachments
    $attachments = FileService::getInstance($w)->getAttachments($invoice);
    //var_dump($attachments); die;
    if (empty($attachments)) {
        $w->error("Please generate and check invoice PDF", "school-manager/viewInvoice/" . $invoice->id);
    }

    if (Count($attachments) > 1) {
        $invoice_pdf_count = 0;
        $invoice_filename = "Invoice_";
        //$invoice->invoice_number     00000     00001   00100
        $formattedInvoiceNumber = (string) $invoice->id;
        for ($i = 0; $i < 5 - strlen($formattedInvoiceNumber); $i++) {
            $invoice_filename .= "0";
        }

        $invoice_filename .= $invoice->id . ".pdf";
        foreach ($attachments as $attachment) {
            if ($attachment->filename == $invoice_filename) {
                $invoice_pdf_count +=1;
            }
        }
        if ($invoice_pdf_count != 1) {
            $w->error("Incorrect number of Invoice PDFs found. Please delete unwanted PDFs or generate a PDF", "school-manager/viewInvoice/" . $invoice->id);
        }
        
    }

    $attachment_paths = [];
    foreach ($attachments as $attachment) {
        $attachment_paths[] = FILE_ROOT . $attachment->fullpath;
    }
    
    $invoice_student = $invoice->getStudent();

    if (empty($invoice_student)) {
        $w->error("No student found for invoice ", '/school-manager/viewInvoice/' . $invoice->id);
    }

    $billingContactMapping = $invoice_student->getBillingContactMapping();

    if (empty($billingContactMapping)) {
        $w->error("No billing contact found for " . $invoice_student->getFullName(), '/school-manager/viewInvoice/' . $invoice->id);
    }

    //get email template and render

    $email_template = TemplateService::getInstance($w)->findTemplate('school', 'invoice_email');

    $subject = $email_template->renderTitle(); //add data
    //"test subject: invoice " . $invoice->id;
    $output = "dear test, test test";

    // var_dump($billingContactMapping->getContact()->email);
    // die;

    try{
        MailService::getInstance($w)->sendMail(
            $billingContactMapping->getContact()->email,
            Config::get('main.company_support_email'),
            $subject,
            $output,
            null,
            null,
            $attachment_paths
        );
    } catch (Exception $e) {
        $w->error("ERROR", '/school-manager/viewInvoice/' . $invoice->id);
    }
    
    $invoice->status = "Sent";
    $dt_object = new DateTime();
    $invoice->dt_sent = $dt_object->format('Y-m-d H:i:s');
    $invoice->Update();

    $w->msg("Invoice sent", "school-manager/viewInvoice/" . $invoice->id);

}