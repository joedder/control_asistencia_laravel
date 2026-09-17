<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attending extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_teacher',
        'id_student',
        'id_group',
        'id_user',
        'status',
        'social_reason',
        'class_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'class_date' => 'datetime',
    ];

    /**
     * Get the teacher associated with the attending.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    /**
     * Get the student associated with the attending.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_student');
    }

    /**
     * Get the group associated with the attending.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    /**
     * Get the user that registered the attending.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Scope a query to apply filters from the request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $request)
    {
        $query
            // Filtros de columna
            ->when($request->filled('id_teacher'), fn($q) => $q->where('id_teacher', $request->id_teacher))
            ->when($request->filled('id_student'), fn($q) => $q->where('id_student', $request->id_student))
            ->when($request->filled('id_group'), fn($q) => $q->where('id_group', $request->id_group))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            
            // Búsqueda global (podría buscar por nombre del estudiante o profesor a través de relaciones, 
            // pero para mantenerlo simple lo hacemos sobre status o lo ignoramos si no aplica)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(fn($sub) =>
                    $sub->where('status', 'like', '%' . $request->search . '%')
                        ->orWhere('social_reason', 'like', '%' . $request->search . '%')
                );
            });

        return $query;
    }
}
