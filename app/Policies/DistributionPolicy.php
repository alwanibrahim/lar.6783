<?php

namespace App\Policies;

use App\Models\Distribution;
use App\Models\User;

class DistributionPolicy
{
    public function view(User $user, Distribution $distribution)
    {
        return $user->id === $distribution->user_id;
    }
}
