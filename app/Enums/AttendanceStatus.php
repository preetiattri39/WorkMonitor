<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case LATE = 'late';
    case ABSENT = 'absent';
    case HALF_DAY = 'half_day';
    case REMOTE = 'remote';
    case ON_LEAVE = 'on_leave';
}
