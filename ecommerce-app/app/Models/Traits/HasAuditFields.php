<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasAuditFields
{
    /**
     * Get the user who created the model.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    /**
     * Get the user who last updated the model.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }

    /**
     * Get the user who soft deleted the model.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'uuid');
    }
}
