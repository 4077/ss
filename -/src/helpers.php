<?php

function ss()
{
    return \ss\Svc::getInstance();
}

function ssc()
{
    $args = func_get_args();

    if ($args) {
        return call_user_func_array([ss()->moduleRootController, 'c'], $args);
    } else {
        return ss()->moduleRootController;
    }
}
