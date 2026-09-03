<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Routing\Controllers\HasMiddleware;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class LeaveTypeController extends BackendBaseController implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            new Middleware('permission:leave-type.index', only: ['index']),
            new Middleware('permission:leave-type.show', only: ['show']),
            new Middleware('permission:leave-type.store', only: ['store']),
            new Middleware('permission:leave-type.edit', only: ['edit']),
            new Middleware('permission:leave-type.update', only: ['update']),
            new Middleware('permission:leave-type.destroy', only: ['destroy']),
        ];
    }

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
