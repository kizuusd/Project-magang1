<?php

namespace Modules\Keuangan\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'target_amount', 'start_date', 'end_date'])]
class SavingGoal extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'start_date'    => 'date',
            'end_date'      => 'date',
        ];
    }

    /**
     * Get the user that owns the saving goal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
