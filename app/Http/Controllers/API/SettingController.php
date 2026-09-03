<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

use Illuminate\Routing\Controllers\Middleware;

class SettingController extends BackendBaseController implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.show', only: ['show']),
            new Middleware('permission:settings.store', only: ['store']),
        ];
    }


    private $model;
    protected $panel = 'Setting';
    // protected $base_route = 'api.settings';
    // protected $view_path = 'backend.setting';
    protected $img_path = 'uploads/settings/';


    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        return response()->json([
            'setting' => Setting::first()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'logo' => 'nullable',
            'fav_icon' => 'nullable',
            'slogan' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'viber' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'google_map' => 'nullable|string|max:555',
            'recaptcha_key' => 'nullable|string|max:255',
            'recaptcha_secret' => 'nullable|string|max:255',
        ]);

        $data = $request->except(['logo', 'fav_icon', 'updated_by', 'created_by']);




        $setting = $this->model->first();
        if ($setting) {

            if ($request->hasFile('logo')) {
                $this->deleteImage($setting->logo);
                $data['logo'] = $this->uploadImage($request->file('logo'), 'setting');
            }
            if ($request->hasFile('fav_icon')) {
                $this->deleteImage($setting->fav_icon);
                $data['fav_icon'] = $this->uploadImage($request->file('fav_icon'), 'setting');
            }
            $setting->update($data + [
                'updated_by' => auth('sanctum')->user()->id,
            ]);
        } else {
            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadImage($request->file('logo'), 'setting');
            }
            if ($request->hasFile('fav_icon')) {
                $data['fav_icon'] = $this->uploadImage($request->file('fav_icon'), 'setting');
            }
            $setting = $this->model->create($data + [
                'created_by' => auth('sanctum')->user()->id,
            ]);
        }


        $message = $setting->wasRecentlyCreated
            ? $this->panel . ' created successfully.'
            : $this->panel . ' updated successfully.';


        return response()->json([
            'status' => 200,
            'success' => true,
            'message' => $message,
            'setting' => $setting
        ]);
    }

    public function show()
    {
        return response()->json([
            'setting' => Setting::first()
        ]);
    }
}
