<?php

function invoicePaid_ALL(Web $w) {
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

    $invoice->status = "Paid";
    $dt_object = new DateTime();
    $invoice->dt_paid = $dt_object->format('Y-m-d H:i:s');
    $invoice->Update();

    $w->msg("Invoice status set to Paid", "school-manager/invoices#unpaid");

}