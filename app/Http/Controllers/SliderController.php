<?php

namespace App\Http\Controllers;

use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        return view('slider.index');
    }

    public function getSliderIndex()
    {
        $business_id = Auth::user()->business_id;
        $uploadedImages = Image::where('business_id', $business_id)->get();
        return response()->json(['data'=> $uploadedImages]);
    }

    public function create()
    {
        return view('slider.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'image_slide' =>'file|mimes:jpg,png,jpeg'
                ]
            );
            $business_id = Auth::user()->business_id;
            $folderPath = 'bs00'.$business_id.'_slides';
            if ($request->hasFile('image_slide')) {
                if (!Storage::disk('slide')->exists($folderPath)) {
                    \File::makeDirectory(public_path().'/slider_files/'.$folderPath, $mode = 0755, true, true);
                }
                $file = $request->file('image_slide');
                $name = $file->getClientOriginalName();
                Storage::disk('slide')->put($folderPath.'/'.$name,  \File::get($file));
                $newImage = new Image();
                $newImage->name = $name;
                $newImage->is_active = true;
                $newImage->business_id = $business_id;
                $newImage->path = $folderPath.'/'.$name;
                $newImage->save();
            }
            return redirect()->action('SliderController@index');
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
        }
        return redirect()->action('SliderController@index');
    }

    public function destroy($id)
    {
        $image = Image::find($id);
        if(Storage::disk('slide')->exists($image->path)){
            Storage::disk('slide')->delete($image->path);
            $image->delete();
            return response()->json(['msg'=>'Image deleted successfully', 'success'=> true]);
        }
        return response()->json(['msg'=>'Error, contact admin. please!', 'success'=> false]);
    }

    public function show($id)
    {
        $image = Image::find($id);
        if(Storage::disk('slide')->exists($image->path)){
            $hash = base64_decode(Storage::disk('slide')->get($image->path));
            return view('slider.show', compact('hash'));
        }
    }

    public function downloadSlide($id)
    {
        $image = Image::find($id);
        if(Storage::disk('slide')->exists($image->path)){
            return Storage::disk('slide')->download($image->path);
        }
        return;
    }

    public function setImageStatus($id)
    {
        $image = Image::find($id);
        $image->is_active = !$image->is_active;
        $image->save();
        return response()->json(['success'=> true]);
    }
}
