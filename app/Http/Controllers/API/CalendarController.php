<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CalendarController extends BackendBaseController
{
    private $model;
    protected $panel = "Holiday & Events Calendar";
    // protected $img_path = 'uploads/staff/';

    public function __construct()
    {
        $this->model = new Calendar();
    }

    public function index(Request $request)
    {
        $query = $this->model->query();

        if ($request->filled('year') && $request->filled('month')) {
            $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
            $query->where('date', 'like', "{$request->year}-{$month}-%");
        } elseif ($request->filled('year')) {
            $query->where('date', 'like', "{$request->year}-%");
        }

        $calendars = $query->get()->map(fn($calendar) => [
            'id' => $calendar->id,
            'date' => $calendar->date,
            'title' => $calendar->title,
            'holiday' => (bool) $calendar->is_holiday,
        ]);

        return response()->json([
            'message' => 'Calendars fetched successfully',
            'data' => $calendars,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'date' => 'required|string',
            'title' => 'required',
            'holiday' => 'nullable',
        ]);

        try {
            $calendar = $this->model->create([
                'title' => $validated['title'],
                'date' => $validated['date'],
                'slug' => Str::slug($validated['title']),
                'is_holiday' => $validated['holiday'] ?? false,
            ]);

            return response()->json([
                'message' => 'Calendar created successfully',
                'data' => $validated + ['id' => $calendar->id],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, Calendar $calendar)
    {
        $validated = $request->validate([
            'date' => 'required|string',
            'title' => 'required',
            'holiday' => 'nullable',
        ]);

        $calendar->update([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'is_holiday' => $validated['holiday'] ?? false,
        ]);

        return response()->json([
            'message' => 'Calendar updated successfully',
            'data' => $validated + ['id' => $calendar->id],
        ]);
    }

    public function destroy(Calendar $calendar)
    {
        $calendar->delete();

        return response()->json(['message' => 'Calendar deleted successfully']);
    }
}
