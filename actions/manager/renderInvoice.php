<?php

function renderInvoice_ALL(Web $w) {
    $p = $w->pathMatch('invoice_id');

    if (empty($p['invoice_id'])) {
        $w->error('No id provided', '/school-manager/invoices');
    }

    //get the invoice
    $invoice = SchoolService::getInstance($w)->getInvoiceForId($p['inoive_id']);
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
    $templateData['invoice_number'] = $invoice->invoice_number;
    $templateData['billing_contact_name'] = $billingContact->getFullName();
    $templateData['billing_contact_email'] = $billingContact->email;
    $templateData['student_name'] = $student->getFullName();
    $templateData['invoice_lines'] = [];




}