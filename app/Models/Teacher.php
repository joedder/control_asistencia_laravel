<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
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
        'english_level',
        'id_user',
    ];

    /**
     * Get the user associated with the teacher.
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
            // Filtros de columna exacta o parcial
            ->when($request->filled('name'), fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->filled('last_name'), fn($q) => $q->where('last_name', 'like', '%' . $request->last_name . '%'))
            ->when($request->filled('identity_id'), fn($q) => $q->where('identity_id', 'like', '%' . $request->identity_id . '%'))
            ->when($request->filled('english_level'), fn($q) => $q->where('english_level', $request->english_level))

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
