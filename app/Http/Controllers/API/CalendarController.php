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
    protected $img_path = 'uploads/calendar/';

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
            'image' => $calendar->image ? asset($this->img_path . $calendar->image) : null,
            'description' => $calendar->description
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
            'image' => 'nullable',
            'description' => 'nullable'
        ]);


        if ($request->hasFile('image')) {
            $image = $this->uploadImage($request->file('image'), 'calendar');
        }

        if ($request)

            try {
                $calendar = $this->model->create([
                    'title' => $validated['title'],
                    'date' => $validated['date'],
                    'is_holiday' => (bool) $validated['holiday'] ?? false,
                    'image' => $image ?? null,
                    'description' => $validated['description'] ?? null,
                    'created_by' => auth('sanctum')->user()->id,
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
            'image' => 'nullable',
            'description' => 'nullable'
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($calendar->image);
            $image = $this->uploadImage($request->file('image'), 'calendar');
        }

        $calendar->update([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'is_holiday' => (bool) $validated['holiday'] ?? false,
            'image' => $image ?? $calendar->image,
            'description' => $validated['description'] ?? null,
            'updated_by' => auth('sanctum')->user()->id,
        ]);

        return response()->json([
            'message' => 'Calendar updated successfully',
            'data' => $validated + ['id' => $calendar->id],
        ]);
    }

    public function destroy(Calendar $calendar)
    {
        if ($calendar->image) {
            $this->deleteImage($calendar->image);
        }

        $calendar->delete();

        return response()->json(['message' => 'Calendar deleted successfully']);
    }
}
