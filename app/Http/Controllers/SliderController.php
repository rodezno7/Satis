<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('alert.view')) {
            abort(403, 'Unauthorized action.');
        }
        return view('slider.index');
    }

    public function getSliderIndex()
    {
        if (!auth()->user()->can('alert.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = Auth::user()->business_id;
        $uploadedImages = Image::where('business_id', $business_id)->get();
        return response()->json(['data'=> $uploadedImages]);
    }

    public function create()
    {
        if (!auth()->user()->can('alert.create')) {
            abort(403, 'Unauthorized action.');
        }
        return view('slider.create');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('alert.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $image = Image::find($id);
        return view('slider.edit')->with(compact('image'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('alert.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $validator = Validator::make($request->all(), [
                'image_slide' => 'required|file|max:5000|mimes:png,jpg,jpeg|dimensions:max_width=1500,max_height=500',
            ]);
            $validator->setCustomMessages([
                'image_slide.required' => 'El campo es requerido',
                'image_slide.file' => 'El campo seleccionado no es valido.',
                'image_slide.max' => 'El archivo cargado no debe exceder los 5MB.',
                'image_slide.mimes' => 'Los formatos permitidos son: PNG, JPG, JPEG',
                'image_slide.dimensions' => 'Las dimensiones deben ser: 1500 x 300 px max.',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }
            $business_id = Auth::user()->business_id;
            $folderName = 'bs00'.$business_id.'_slides';
            if ($request->hasFile('image_slide')) {
                if (!Storage::disk('slide')->exists($folderName)) {
                    \File::makeDirectory(public_path().'/uploads/slides/'.$folderName, $mode = 0755, true, true);
                }
                $file = $request->file('image_slide');
                $name = $file->getClientOriginalName();
                Storage::disk('slide')->put($folderName.'/'.$name,  \File::get($file));
                $newImage = new Image();
                $newImage->name = $name;
                $newImage->description = $request->description;
                $newImage->is_active = true;
                $newImage->business_id = $business_id;
                $newImage->path = $folderName.'/'.$name;
                $newImage->start_date = $request->start_date;
                $newImage->end_date = $request->end_date;
                $newImage->link = $request->slide_link;
                $newImage->save();
            }
            return response()->json(['msg'=>'Alerta guardada satisfactoriamente', 'success'=> true]);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            return response()->json(['msg'=>'Error, contacte al administrador', 'success'=> false]);
        }
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('alert.edit')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $image = Image::find($id);
            $image->description = $request->description;
            $image->start_date = $request->start_date;
            $image->end_date = $request->end_date;
            $image->link = $request->slide_link;
            $image->save();
            return response()->json(['msg'=>'Alerta guardada satisfactoriamente', 'success'=> true]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['msg'=>'Error, contacte al administrador', 'success'=> false]);
        }

    }

    public function destroy($id)
    {
        if (!auth()->user()->can('alert.delete')) {
            abort(403, 'Unauthorized action.');
        }
        $image = Image::find($id);
        if(Storage::disk('slide')->exists($image->path)){
            Storage::disk('slide')->delete($image->path);
            $image->delete();
            return response()->json(['msg'=>'Alerta eliminada satisfactoriamente', 'success'=> true]);
        }
        return response()->json(['msg'=>'Error, contacte al administrador', 'success'=> false]);
    }

    public function show($id)
    {
        if (!auth()->user()->can('alert.view')) {
            abort(403, 'Unauthorized action.');
        }
        $image = Image::find($id);
        if(Storage::disk('slide')->exists($image->path)){
            $path = $image->path;
            return view('slider.show', compact('path'));
        }
    }

    public function downloadSlide($id)
    {
        if (!auth()->user()->can('alert.view')) {
            abort(403, 'Unauthorized action.');
        }
        $image = Image::find($id);
        if(Storage::disk('slide')->exists($image->path)){
            return Storage::disk('slide')->download($image->path);
        }
        return;
    }

    public function setImageStatus($id)
    {
        if (!auth()->user()->can('alert.edit')) {
            abort(403, 'Unauthorized action.');
        }
        $image = Image::find($id);
        $image->is_active = !$image->is_active;
        $image->save();
        if($image->is_active) {
            return response()->json(['msg'=>'Alerta activada satisfactoriamente', 'success'=> true]);
        } else {
            return response()->json(['msg'=>'Alerta desactivada satisfactoriamente', 'success'=> true]);
        }
    }
}
