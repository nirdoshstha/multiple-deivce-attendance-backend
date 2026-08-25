<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

class LeaveTypeController extends BackendBaseController
{

    private $model;
    protected $panel = "Leave Type";

    public function __construct()
    {
        $this->model = new LeaveType();
    }


    public function index()
    {
        $leaves = $this->model->get();

        return response()->json([
            'leaves' => $leaves
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

        $request->validate([
            'name' => 'required|string',
            'days_per_year' => 'required'
        ]);

        $leaves = $this->model->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'days_per_year' => $request->days_per_year,
            'is_paid' => $request->is_paid,
            'requires_approval' => $request->requires_approval,
            'allow_half_day' => $request->allow_half_day,
            'status' => $request->status,
            'created_by' => auth('sanctum')->user()->id
        ]);

        return response()->json([
            'status' => 200,
            'message' => $this->panel . 'created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $leave = $this->model->find($id);


        return response()->json([
            'status' => 200,
            'leave' => $leave
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $leave = $this->model->find($id);


        return response()->json([
            'status' => 200,
            'leave' => $leave
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $leave = $this->model->find($id);

        $leave->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'days_per_year' => $request->days_per_year,
            'is_paid' => $request->is_paid,
            'requires_approval' => $request->requires_approval,
            'allow_half_day' => $request->allow_half_day,
            'status' => $request->status,
            'updated_by' => auth('sanctum')->user()->id
        ]);

        $name = $leave->name;

        return response()->json([
            'status' => 200,
            'message' => $name . ' updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $leave = $this->model->find($id);
        $leave->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' deleted successfully'
        ]);
    }
}
