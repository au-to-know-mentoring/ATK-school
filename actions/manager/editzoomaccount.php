<?php 

function editzoomaccount_GET(Web $w) {
    $p = $w->pathMatch("id");

    if (!empty($p['id'])) {
        $account = SchoolService::getInstance($w)->GetZoomAccountForId($p['id']);
    } else {
        $account = new SchoolZoomAccount($w);
    }

    $form = [
        "account details" => [
            [
                ["Account Number", "text", "account_number", $account->account_number]
            ],
            [
                ["User Name", "text", "user_name", $account->user_name]
            ],
            [
                ["Passowrd", "text", "password", $account->password]
            ]
        ]
    ];

    $w->ctx('zoomeditform', Html::multiColForm($form, '/school-manager/editzoomaccount/' . $account->id));

}

function editzoomaccount_POST(Web $w) {
    $p = $w->pathMatch("id");

    if (!empty($p['id'])) {
        $account = SchoolService::getInstance($w)->GetZoomAccountForId($p['id']);
    } else {
        $account = new SchoolZoomAccount($w);
    }

    $account->fill($_POST);
    $account->insertOrUpdate();
    $w->msg("Zoom Account Updated", "/school-manager/viewzoomaccounts");
}
