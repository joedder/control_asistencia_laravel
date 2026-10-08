<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoryMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_student',
        'id_group',
        'id_new_group',
        'migrated',
        'id_user',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'migrated' => 'boolean',
    ];

    /**
     * Get the student associated with the movement.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_student');
    }

    /**
     * Get the old group associated with the movement.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    /**
     * Get the new group associated with the movement.
     */
    public function newGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_new_group');
    }

    /**
     * Get the user that registered the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Scope a query to apply filters from the request.
     */
    public function scopeFilter($query, $request)
    {
        $query
            ->when($request->filled('id_group'), fn($q) => $q->where('id_group', $request->id_group))
            ->when($request->filled('id_new_group'), fn($q) => $q->where('id_new_group', $request->id_new_group))
            ->when($request->filled('migrated'), fn($q) => $q->where('migrated', $request->migrated))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('student', function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%')
                        ->orWhere('identity_id', 'like', '%' . $request->search . '%');
                });
            });

        return $query;
    }
}
