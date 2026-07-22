<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenderController extends BackendBaseController
{

    protected $model;
    protected $panel = "Gender";

    public function __construct()
    {
        $this->model = new Gender();
    }
    public function index()
    {
        $genders = $this->model->get();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'genders' => $genders
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

        $gender = $this->model->create([
            'name' => $request->name,
            'key'  => Str::slug($request->name, '_') . '_key',
            'created_by' => auth('sanctum')->user()->id,
        ]);



        return response()->json([
            'status' => 200, 
             'message' => $this->panel . ' "' .  $request->name. '" stored successfully.',
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
        $gender = $this->model->findOrFail($id);

        $name = $gender->name;

        $gender->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }
}
