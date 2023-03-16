<?php

function invoices_ALL(Web $w) {

    $user = AuthService::getInstance($w)->user();
    if (!$user->hasRole('school_manager')) {
        $w->error('Cannot view page');
    }
    //var_dump(SchoolService::getInstance($w)->GetObjects('SchoolClassInstance', ['status' => 'Completed', 'is_deleted' => 0])); die;

    $w->ctx('title', 'Invoices');

    //$invoices = SchoolService::getInstance($w)->getAllInvoices();

    $unpaid_invoices = SchoolService::getInstance($w)->getAllUnpaidInvoices();
    
    $new_invoices = SchoolService::getInstance($w)->getAllNewInvoices();

    $uninvoices_class_instances = SchoolService::getInstance($w)->getUninvoicedClasses();

    //var_dump($uninvoices_class_instances); die;

    //make a table of uninvoiced class instances
    $uninvoicedtable = [];
    $uninvoicedheaders = ['Class Date', 'Session Status', 'Participant', 'Mentor'];
    foreach($uninvoices_class_instances as $class_instance) {
        $row = [];
        $row[] = Html::a("/school-manager/viewclassinstance/" . $class_instance->id, date('d/m/Y h:i', $class_instance->dt_class_date));
        $row[] = $class_instance->status;
        $row[] = $class_instance->getStudent()->getContact()->getFullName();
        $row[] = $class_instance->getTeacher()->getContact()->getFullName();
        //$row[] = Html::b('')
        $uninvoicedtable[] = $row;
    }

    $w->ctx('uninvoiced_table', Html::table($uninvoicedtable, null, 'tablesorter', $uninvoicedheaders));

    //make table for unpaid invoices
    $unpaidtable = [];
    $unpaidheaders = ['Invoice Number', 'Invoice Status', 'Participant', 'Amount', 'Date Sent', 'Actions'];
    foreach($unpaid_invoices as $invoice) {
        $row = [];
        $row[] = $invoice->id;
        $row[] = $invoice->status;
        $row[] = $invoice->getStudent()->getContact()->getFullName();
        $row[] = formatCurrency($invoice->total_charge);
        $row[] = date('d/m/Y', $invoice->dt_sent);
        $row[] = Html::b('/school-manager/viewInvoice/' . $invoice->id, 'View Invoice');
        $unpaidtable[] = $row;
    }

    $w->ctx('unpaid_table', Html::table($unpaidtable, null, 'tablesorter', $uninvoicedheaders));


    //make table for new invoices
    $newtable = [];
    $newheaders = ['Invoice Number', 'Invoice Status', 'Participant', 'Amount', 'Date Sent', 'Actions'];
    foreach($new_invoices as $invoice) {
        $row = [];
        $row[] = $invoice->id;
        $row[] = $invoice->status;
        $row[] = $invoice->getStudent()->getContact()->getFullName();
        $row[] = formatCurrency($invoice->total_charge);
        $row[] = date('d/m/Y', $invoice->dt_sent);
        $row[] = Html::b('/school-manager/viewInvoice/' . $invoice->id, 'View Invoice');
        $newtable[] = $row;
    }

    $w->ctx('new_table', Html::table($newtable, null, 'tablesorter', $newheaders));

}