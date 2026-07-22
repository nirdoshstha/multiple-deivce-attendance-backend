<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DesignationController extends BackendBaseController
{

    protected $model;
    protected $panel = "Designation";

    public function __construct()
    {
        $this->model = new Designation();
    }
    public function index()
    {
        $designations = $this->model->get();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'designations' => $designations
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
            'name' => 'required',
        ]);

        $designation = $this->model->create([
            'name' => $request->name,
            'key'  => Str::slug($request->name, '_') . '_key',
            'created_by' => auth('sanctum')->user()->id,
        ]);



        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' .  $request->name . '" stored successfully.',
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
        $designation = $this->model->findOrFail($id);

        $name = $designation->name;

        $designation->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }
}
