<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

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
            ->when($request->filled('description'), fn($q) => $q->where('description', 'like', '%' . $request->description . '%'))

            // Búsqueda global (cross-column)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(fn($sub) =>
                    $sub->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%')
                );
            });

        return $query;
    }
}
