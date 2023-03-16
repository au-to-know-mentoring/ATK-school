<?php

function settings_GET(Web $w) {

    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error("Access restricted",'/school');
    }

    $w->ctx('title', 'Settings for ' . $user->getFullName());

    $settings = SchoolService::getInstance($w)->GetSettingsForUserId($user->id);
    if (empty($settings)) {
        $settings = new SchoolManagerSettings($w);
    }

    $form = [
        'settings' => [
            [
                ['State', 'select', 'state', $settings->state, SchoolService::getInstance($w)->GetStateSelectOptions()],
                ['TimeZone', 'select', 'timezone', $settings->timezone, SchoolService::getInstance($w)->GetTimeZoneSelectOptions()]
            ]
        ]
    ];

    $w->ctx('form', Html::multiColForm($form,'/school-manager/settings'));

}

function settings_POST(Web $w) {
    $user = AuthService::getInstance($w)->user();

    if (!$user->hasRole('school_manager')) {
        $w->error("Access restricted",'/school');
    }

    $settings = SchoolService::getInstance($w)->GetSettingsForUserId($user->id);
    if (empty($settings)) {
        $settings = new SchoolManagerSettings($w);
    }

    $settings->fill($_POST);
    $settings->user_id = $user->id;
    $settings->insertOrUpdate();
    $w->msg('settings updated','/school-manager/settings');

}