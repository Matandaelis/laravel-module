<?php

namespace Modules\Rosca\Policies;

use App\Models\User;
use Modules\Rosca\Models\Rosca;

class RoscaPolicy
{
    public function manage(User $user, Rosca $rosca)
    {
        // Basic: only creator (if creator_id added) or admin — this is a placeholder: extend as needed
        return true; // allow for now; replace with proper checks
    }
}
