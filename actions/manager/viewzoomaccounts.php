<?php

function viewzoomaccounts_ALL(Web $w)
{
    $accounts = SchoolService::getInstance($w)->GetZoomAccounts();

    $table = [];
    $tableHeaders = ['Account Number', 'Username', 'Password', 'Actions'];
    if (!empty($accounts)) {
        foreach($accounts as $account) {
            $row = [];
            $row[] = $account->account_number;
            $row[] = $account->user_name;
            $row[] = $account->password;
            $actions = [];
            $actions[] = Html::b("/school-manager/editzoomaccount/" . $account->id, "Edit");
            $row[] = implode('', $actions);
            $table[] = $row;
        }
    }

    $w->ctx("accountsTable", Html::table($table, null, "tablesorter", $tableHeaders));
}