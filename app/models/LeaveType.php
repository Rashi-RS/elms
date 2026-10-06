<?php
declare(strict_types=1);

namespace App\models;

class LeaveType extends Model
{
    protected string $table = 'leave_types';

    public function getActive(): array
    {
        return $this->where('status', 'active', 'name ASC');
    }
}
