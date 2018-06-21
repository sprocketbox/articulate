<?php

if (! function_exists('entities')) {
    function entities(): \Ollieread\Articulate\EntityManager
    {
        return app(\Ollieread\Articulate\EntityManager::class);
    }
}