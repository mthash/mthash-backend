<?php
function getEntityByController (string $controllerName)
{
    $components = explode ('\\', $controllerName);
    return strtolower (rtrim ($components[2], 'Controller'));
}