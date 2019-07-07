<?php
namespace MtHash\Controller\Mining;
class Request
{
    public $rules    = [
        'amount'                => ['required', 'numeric'],
    ];
}