<?php

namespace App\Enums;

enum OrderStatusEnums: string
{
    case PENDING = 'pending';

    case PROCESSIGN = 'processign';

    case COMPLETED = 'completed';

    case DECLINED = 'declined';
}