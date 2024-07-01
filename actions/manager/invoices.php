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
        $row[] = Html::a("/school-manager/viewclassinstance/" . $class_instance->id, formatDate($class_instance->dt_class_date, 'd/m/Y h:i'));
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
        $actions = [];
        $actions[] = Html::b('/school-manager/viewInvoice/' . $invoice->id, 'View Invoice');
        $actions[] = Html::b('/school-manager/invoicePaid/' . $invoice->id, 'Paid');
        $row[] = implode('', $actions);
        $unpaidtable[] = $row;
    }

    $w->ctx('unpaid_table', Html::table($unpaidtable, null, 'tablesorter', $unpaidheaders));


    //make table for new invoices
    $newtable = [];
    $newheaders = ['Invoice Number', 'Invoice Status', 'Participant', 'Amount', 'Actions'];
    foreach($new_invoices as $invoice) {
        $row = [];
        $row[] = $invoice->id;
        $row[] = $invoice->status;
        $row[] = $invoice->getStudent()->getContact()->getFullName();
        $row[] = formatCurrency($invoice->total_charge);
        //$row[] = date('d/m/Y', $invoice->dt_sent);
        $actions = [];
        $actions[] = Html::b('/school-manager/viewInvoice/' . $invoice->id, 'View Invoice');
        
        $row[] = implode('', $actions);
        $newtable[] = $row;
    }

    $w->ctx('new_table', Html::table($newtable, null, 'tablesorter', $newheaders));

    //paid invoices

    // Look for reset
    $reset = $w->sessionOrRequest("reset");
    if (empty($reset)) {
        // Get filter values
        $student_id = $w->sessionOrRequest("school__student-id", null);
        $date_sent_range_start = $w->sessionOrRequest("school__date-range-start");
        $date_sent_range_end = $w->sessionOrRequest("school__date-range-end");

    }

    
    

    $student_select_Options = SchoolService::getInstance($w)->getStudentSelectOptions();

    $filter_data = [
        ["Participant", "select", "school__student-id", !empty($student_id) ? $student_id : null, $student_select_Options],
        ['Date Paid From', 'date', 'school__date-range-start', !empty($date_sent_range_start) ? $date_sent_range_start : null],
        ['Date Paid To', 'date', 'school__date-range-end', !empty($date_sent_range_end) ? $date_sent_range_end : null],
    ];

    $w->ctx("filter_data", $filter_data);

    //get invoices for filter
    if (empty($reset)) {
        $filtered_paid_invoices = SchoolService::getInstance($w)->getInvoicesForFilter("Paid", $student_id, $date_sent_range_start, $date_sent_range_end);
    }

    if (!empty($filtered_paid_invoices) && empty($reset)) {
        $paidtable = [];
        $paidheaders = ['Invoice Number', 'Date Paid', 'Participant', 'Amount', 'Actions'];
        foreach($filtered_paid_invoices as $invoice) {
            $row = [];
            $row[] = $invoice->id;
            $row[] = date('d/m/Y', $invoice->dt_paid); //$invoice->dt_paid;
            $row[] = $invoice->getStudent()->getFullName();
            $row[] = formatCurrency($invoice->total_charge);
            //$row[] = date('d/m/Y', $invoice->dt_sent);
            $actions = [];
            $actions[] = Html::b('/school-manager/viewInvoice/' . $invoice->id, 'View Invoice');
            
            $row[] = implode('', $actions);
            $paidtable[] = $row;
        }

        $w->ctx('paid_table', Html::table($paidtable, null, 'tablesorter', $paidheaders));
    }

}