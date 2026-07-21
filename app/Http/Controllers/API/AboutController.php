<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends BackendBaseController
{

    protected $model;
    protected $panel = 'About Us';
    protected $img_path = 'uploads/about/';

    public function __construct()
    {
        $this->model = new About();
    }

    public function index()
    {
        $about = $this->model->where('type', 'page')->first();
        $abouts = $this->model->where('type', 'post')->get();

        return response()->json([
            'status' => 200,
            'about' => $about,
            'abouts' => $abouts
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:55',
            'sub_title' => 'nullable|string|max:55',
            'description' => 'nullable|string',
            'image' => 'nullable',
            'banner' => 'nullable',
            'status' => 'nullable'
        ]);

        $data = $request->except([
            'image',
            'banner',
            'created_by',
            'updated_by'
        ]);
        // $data['status'] = $request->boolean('status');

        $about = $this->model->where('type', 'page')->first();

        if ($about) {

            if ($request->hasFile('image')) {
                $this->deleteImage($about->image);
                $data['image'] = $this->uploadImage($request->file('image'), 'about');
            }
            if ($request->hasFile('banner')) {
                $this->deleteImage($about->banner);
                $data['banner'] = $this->uploadImage($request->file('banner'), 'about');
            }

            $about->update($data + [
                'updated_by' =>  auth('sanctum')->user()->id,
                'type' => 'page',
            ]);

            return response()->json([
                'status' => 200,
                'message' => $this->panel . ' updated successfully',

            ]);
        } else {

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), 'about');
            }
            if ($request->hasFile('banner')) {
                $data['banner'] = $this->uploadImage($request->file('banner'), 'about');
            }
            $about = $this->model->create($data + [
                'created_by' => auth('sanctum')->user()->id,
                'type' => 'page',
            ]);

            return response()->json([
                'status' => 200,
                'message' => $this->panel . ' Stored Successfully'
            ]);
        }
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:55',
            'sub_title' => 'nullable|string|max:55',
            'description' => 'nullable|string',
            'image' => 'nullable',
        ]);


        $data = $request->except('image', 'created_by', 'updatedby');
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'about');
        }

        $about = $this->model->create($data + [
            'type' => 'post',
            'created_by' => auth('sanctum')->user()->id,
        ]);

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' post created successfully.'
        ]);
    }

    public function editPost(Request $request, $id)
    {
        $about = $this->model->find($id);
        return response()->json([
            'status' => 200,
            'about' => $about
        ]);
    }

    public function updatePost(Request $request, $id)
    {

        $request->validate([
            'title' => 'required|string',
            'sub_title' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        $about = $this->model->find($id);

        $data = $request->except('image', 'created_by', 'updated_by');

        if ($request->hasFile('image')) {
            $this->deleteImage($about->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'about');
        }

        $about->update($data + [
            'type' => 'post',
            'updated_by' => auth('sanctum')->user()->id,
        ]);

        return response()->json([
            'status' => 201,
            'message' => $this->panel . ' updated successfully'
        ]);
    }

    public function statusPost($id)
    {
        $about = $this->model->find($id);
        $about->status = $about->status ? '0' : '1';
        $about->save();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' post status changed successfully'
        ]);
    }

    public function destroy($id)
    {
        $about = $this->model->find($id);
        $this->deleteImage($about->image);
        // if ($about->image && file_exists(public_path('uploads/about/' . $about->image))) {
        //     unlink(public_path('uploads/about/' . $about->image));
        // }
        $about->delete();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' post deleted successfully',
        ]);
    }
}
