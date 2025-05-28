<?php
namespace App\Enums;

enum RoleEnum: string
{
    case Student = 'student';
    case Teacher = 'teacher';
    case Admin = 'admin';
}
