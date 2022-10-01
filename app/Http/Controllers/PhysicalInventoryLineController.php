<?php

namespace App\Http\Controllers;

use App\PhysicalInventory;
use App\PhysicalInventoryLine;
use App\Variation;
use Illuminate\Http\Request;

class PhysicalInventoryLineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request()->ajax()) {
            $physical_inventory_id = $request->input('physical_inventory_id');
            $variation_id = $request->input('variation_id');

            try {
                $pil = PhysicalInventoryLine::where('physical_inventory_id', $physical_inventory_id)
                    ->where('variation_id', $variation_id)
                    ->first();

                if (empty($pil)) {
                    $user_id = $request->session()->get('user.id');

                    $variation = Variation::find($variation_id);

                    PhysicalInventoryLine::create([
                        'physical_inventory_id' => $physical_inventory_id,
                        'product_id' => $variation->product_id,
                        'variation_id' => $variation_id,
                        'quantity' => 0,
                        'created_by' => $user_id,
                        'updated_by' => $user_id
                    ]);

                    $output = [
                        'success' => true
                    ];

                } else {
                    $output = [
                        'success' => false,
                        'msg' => __('physical_inventory.product_already_added')
                    ];
                }

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PhysicalInventoryLine  $physicalInventoryLine
     * @return \Illuminate\Http\Response
     */
    public function show(PhysicalInventoryLine $physicalInventoryLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PhysicalInventoryLine  $physicalInventoryLine
     * @return \Illuminate\Http\Response
     */
    public function edit(PhysicalInventoryLine $physicalInventoryLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PhysicalInventoryLine  $physicalInventoryLine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PhysicalInventoryLine $physicalInventoryLine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('physical_inventory.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $pil = PhysicalInventoryLine::findOrFail($id);
                $pil->delete();

                $physical_inventory = PhysicalInventory::find($pil->physical_inventory_id);
                $physical_inventory->updated_by = request()->session()->get('user.id');
                $physical_inventory->save();

                $output = [
                    'success' => true,
                    'msg' => __('physical_inventory.product_removed_successfully')
                ];

            } catch (\Exception $e) {
                \Log::emergency('File: ' . $e->getFile(). ' Line: ' . $e->getLine(). ' Message: ' . $e->getMessage());
            
                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Update physical inventory line.
     * 
     * @return array
     */
    public function updateLine()
    {
        if (request()->ajax()) {
            $id = request()->input('id');
            $quantity = request()->input('quantity');
            $user_id = request()->session()->get('user.id');

            try {
                $line = PhysicalInventoryLine::find($id);
                $line->quantity = $quantity;
                $line->updated_by = $user_id;
                $line->save();

                $output = ['success' => true];

            } catch (\Exception $e) {
                $output = ['success' => false];
            }

            return $output;
        }
    }
}
