<?php

namespace Exceptions;

class RouteNotFoundException extends \Exception
{
    protected $message = 'cette route n\'existe pas';
}