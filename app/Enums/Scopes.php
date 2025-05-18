<?php

namespace App\Enums;
enum Scopes: string
{
    case Individual = 'individual';
    case Group = 'group';
    case Both = 'both';
}
