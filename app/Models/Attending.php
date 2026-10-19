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
        'substitute_teacher_id',
        'session_id',
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
        'class_date' => 'date:Y-m-d',
    ];

    /**
     * Get the teacher associated with the attending.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }

    public function substituteTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'substitute_teacher_id');
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
     * Normalize a date input to Y-m-d format.
     */
    public static function normalizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $trimmed = trim($date);

        // DD/MM/YYYY or DD-MM-YYYY
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $trimmed, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
        }

        // YYYY-MM-DD or YYYY/MM/DD
        if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $trimmed, $m)) {
            return sprintf('%04d-%02d-%02d', (int) $m[1], (int) $m[2], (int) $m[3]);
        }

        try {
            return \Carbon\Carbon::parse($trimmed)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
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
        $startDate = static::normalizeDate($request->input('start_date'));
        $endDate   = static::normalizeDate($request->input('end_date'));
        $classDate = static::normalizeDate($request->input('class_date'));

        // Filtro por fecha o rango de fechas
        if (!empty($startDate) && !empty($endDate)) {
            $from = $startDate;
            $to = $endDate;
            if (strtotime($from) > strtotime($to)) {
                [$from, $to] = [$to, $from];
            }

            if ($from === $to) {
                $query->whereDate('class_date', $from);
            } else {
                $query->whereDate('class_date', '>=', $from)
                      ->whereDate('class_date', '<=', $to);
            }
        } elseif (!empty($startDate)) {
            $query->whereDate('class_date', $startDate);
        } elseif (!empty($endDate)) {
            $query->whereDate('class_date', $endDate);
        } elseif (!empty($classDate)) {
            $query->whereDate('class_date', $classDate);
        }

        $query
            // Filtros de columna
            ->when($request->filled('id_teacher'), fn($q) => $q->where('id_teacher', $request->id_teacher))
            ->when($request->filled('id_group'), fn($q) => $q->where('id_group', $request->id_group))
            
            // Búsqueda global
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function($qW) use ($search) {
                    $qW->whereHas('teacher', function($qT) use ($search) {
                        $qT->where('name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                    })->orWhereHas('group', function($qG) use ($search) {
                        $qG->where('name', 'like', "%{$search}%");
                    })->orWhere('class_date', 'like', "%{$search}%");
                });
            });

        return $query;
    }
}
