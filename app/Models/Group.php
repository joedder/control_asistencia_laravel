<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'id_teacher',
        'id_category_group',
        'id_level',
    ];

    /**
     * Get the teacher associated with the group.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    /**
     * Get the category group associated with the group.
     */
    public function categoryGroup(): BelongsTo
    {
        return $this->belongsTo(CategoryGroup::class, 'id_category_group');
    }

    /**
     * Get the level associated with the group.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'id_level');
    }

    /**
     * Get the students associated with the group.
     */
    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class, 'id_group');
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
            // Filtros de columna exacta o parcial
            ->when($request->filled('name'), fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('id_teacher'), fn($q) => $q->where('id_teacher', $request->id_teacher))
            ->when($request->filled('id_category_group'), fn($q) => $q->where('id_category_group', $request->id_category_group))
            ->when($request->filled('id_level'), fn($q) => $q->where('id_level', $request->id_level))

            // Búsqueda global (cross-column)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(fn($sub) =>
                    $sub->where('name', 'like', '%' . $request->search . '%')
                );
            });

        return $query;
    }
}
