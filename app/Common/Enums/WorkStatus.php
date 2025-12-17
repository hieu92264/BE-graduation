<?php

namespace App\Common\Enums;

enum WorkStatus: string
{
    case INTERN = 'INTERN'; // Thực tập
    case PROBATION = 'PROBATION'; // Thử việc
    case OFFICIAL = 'OFFICIAL'; // Chính thức
    case PART_TIME = 'PART_TIME'; // Bán thời gian
    case CONTRACT = 'CONTRACT'; // Hợp đồng
}
