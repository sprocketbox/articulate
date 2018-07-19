<?php

if (! function_exists('entities')) {
    function entities(): \Sprocketbox\Articulate\EntityManager
    {
        return app(\Sprocketbox\Articulate\EntityManager::class);
    }
}