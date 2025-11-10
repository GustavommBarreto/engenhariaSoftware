<?php

namespace App\Enums;

enum ProjectRole: string
{
    case OWNER = 'OWNER';
    case PRODUCT_OWNER = 'PRODUCT_OWNER';
    case SCRUM_MASTER = 'SCRUM_MASTER';
    case DEVELOPER = 'DEVELOPER';
}
