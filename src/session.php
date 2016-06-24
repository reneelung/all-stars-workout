<?php

function is_logged_in($session) {
    return $session->get('is_logged_in') && $session->get('is_user') == true;
}