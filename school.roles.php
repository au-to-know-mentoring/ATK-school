<?php

function role_school_manager_allowed(Web $w, $path) {
    return $w->checkUrl($path, "school", "*", "*");
}

function role_school_teacher_allowed(Web $w, $path) {
    return $w->checkUrl($path, "school", "teacher", "*") || $w->checkUrl($path, "school", "", "index");
}
