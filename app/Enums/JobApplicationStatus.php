<?php

namespace App\Enums;

enum JobApplicationStatus: string
{
    case APPLIED = 'applied';
    case REJECTED = 'rejected';
    case HIRED = 'hired';
}
