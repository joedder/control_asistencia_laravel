<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'identity_id',
        'id_group',
    ];

    /**
     * Get the group associated with the student.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group');
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
            ->when($request->filled('last_name'), fn($q) => $q->where('last_name', 'like', '%' . $request->last_name . '%'))
            ->when($request->filled('identity_id'), fn($q) => $q->where('identity_id', 'like', '%' . $request->identity_id . '%'))
            ->when($request->filled('id_group'), fn($q) => $q->where('id_group', $request->id_group))

            // Búsqueda global (cross-column)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(fn($sub) =>
                    $sub->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%')
                        ->orWhere('identity_id', 'like', '%' . $request->search . '%')
                );
            });

        return $query;
    }
}
