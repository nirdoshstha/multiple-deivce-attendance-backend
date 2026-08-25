<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveApplicationController extends Controller
{
    private $model;
    protected $panel = "Leave Application";

    public function __construct()
    {
        $this->model = new LeaveApplication();
    }

    public function index()
    {
        $leave_applications = LeaveApplication::with('leaveType', 'user', 'role')->get();
        $leave_type = LeaveType::get();

        return response()->json([
            'leave_applications' => $leave_applications,
            'leave_type' => $leave_type
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'user_id' => 'required',
            'role_id' => 'required',
            'leave_type_id' => 'required'
        ], [
            'user_id.required' => 'Please select User.',
            'role_id.required' => 'Please select User Role.',
            'leave_type_id.required' => 'Please select Leave Type.',
        ]);

        $leaveType = LeaveType::find($validated['leave_type_id']);

        $leave_application = $this->model->create([
            'user_id' => $validated['user_id'],
            'role_id' => $validated['role_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'day_type' => $request['day_type'],
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'total_days' => $leaveType?->days_per_year ?? 0,
            'reason' => $request->reason,
            'created_by' => auth('sanctum')->user()->id
        ]);

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' stored successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
