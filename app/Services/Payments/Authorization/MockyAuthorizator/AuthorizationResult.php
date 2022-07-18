<?php

namespace App\Services\Payments\Authorization\MockyAuthorizator;

enum AuthorizationResult: string
{
    case Authorized = 'Autorizado';
    case Denied = 'Negado';
}
