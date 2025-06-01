<?php
namespace App\Enums;

enum RoleEnum: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Student = 'student';
    case Teacher = 'teacher';
}
