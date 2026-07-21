<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackendBaseController extends Controller
{

    protected $panel;
    protected $base_route;
    protected $view_path;
    protected $img_path;



    protected function __loadDataToView($viewPath)
    {
        view()->composer($viewPath, function ($view) {
            $view->with('panel', $this->panel);
            $view->with('view_path', $this->view_path);
            $view->with('base_route', $this->base_route);
            if (isset($this->img_path)) {
                $view->with('img_path', $this->img_path);
            }
        });
        return $viewPath;
    }


    protected function uploadImage($image)
    {
        $image_name = time() . '_' . $image->getClientOriginalName();
        $image->move($this->img_path, $image_name);
        return $image_name;
    }

    // protected function deleteImage($image_name)
    // {
    //     $image = $this->img_path . $image_name;
    //     if (is_file($image)) {
    //         unlink($image);
    //     }
    // }

    protected function deleteImage($image_name)
    {
        $image = public_path($this->img_path . $image_name);

        if (file_exists($image)) {
            unlink($image);
        }
    }
}
