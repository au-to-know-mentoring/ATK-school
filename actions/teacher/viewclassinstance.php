<?php

function viewclassinstance_ALL(Web $w) {

    $p = $w->pathMatch('id');

    $loggedInUser = AuthService::getInstance($w)->user();
    if ($loggedInUser->hasRole('school_manager')) {
        $w->redirect("/school-manager/viewclassinstance/" . $p['id']);
    }

    if (empty($p['id'])) {
        $w->error('no class instance id provided', '/school');
    }

    $classInstance = SchoolService::getInstance($w)->GetClassInstancesForId($p['id']);

    if (empty($classInstance)) {
        $w->error('no class instance found for id', '/school');
    }

    $classData = $classInstance->getClassData();

    $table = [
        'Session Details' => [
            [
                ['Date', 'text', 'date', date('l d/m/Y', $classInstance->dt_class_date)],
                ['Time', 'text', 'time', date('H:i', $classInstance->dt_class_date)]
            ],
            [
                ['Link', 'text', 'link', $classData->link]
            ]
        ]
    ];

    $w->ctx('detailsTable', Html::multiColTable($table));
}