<?php

use App\Kardex;
use App\Product;
use App\Business;
use App\Warehouse;
use App\Variation;
use App\PurchaseLine;
use App\KitHasProduct;
use App\BusinessLocation;
use App\TransactionSellLine;
use App\VariationLocationDetails;

use App\Http\Controllers\KardexController;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDuplicatedProducts extends Seeder
{
    private $kardex;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct(KardexController $kardex) {
        $this->kardex = $kardex;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $business = Business::pluck('id');

        $p = 1;
        $limit = 100;
        foreach ($business as $b) {
            break; // Remove this line for continue
            $duplicated_products = Product::join('variations as v', 'products.id', 'v.product_id')
                ->where('products.business_id', $b)
                ->whereRaw('v.deleted_at IS NULL')
                ->select('products.id', 'products.sku')
                ->groupBy('products.id')
                ->havingRaw('COUNT(v.id) > 1')
                ->get();

            foreach ($duplicated_products as $dp) {
                $variations = Variation::where('product_id', $dp->id)
                    ->select('id', 'product_id', 'sub_sku as sku')
                    ->get();

                $count = count($variations);

                \Log::info("/********** Product ". $p ." => id: ". $dp->id ." sku: ". $dp->sku ." */");
                
                if ($count == 2) {;
                    $first = $variations->first();
                    $last = $variations->last();

                    /** Update variation records */
                    $this->update_records($first->id, $last->id, $b);

                } else { // Products with more than two variations
                    $first = $variations->first();

                    foreach ($variations as $v) {
                        if ($v->id != $first->id) {
                            /** Update variation records */
                            $this->update_records($first->id, $v->id, $b);
                        }
                    }
                }

                $p ++;

                /** Verify the product limit */
                if ($p >= $limit) {
                    break;
                }
            }

            /** Verify the product limit */
            if ($p >= $limit) {
                break;
            }
        }
    }

    /**
     * Update variation records
     * 
     * @param int $first_id
     * @param int $last_id
     * 
     * @return void
     */
    private function update_records($first_id, $last_id, $business_id) {
        try {
            \Log::info('Replacing '. $last_id .' for '. $first_id);
            DB::beginTransaction();

            /** Update kardex */
            $kardex = Kardex::where('variation_id', $last_id)->get();
            foreach ($kardex as $k) {
                $k->variation_id = $first_id;
                $k->save();

                \Log::info('Kardex updated '. $k->id);
            }

            if (!count($kardex)) {
                \Log::info('No kardex records');
            }

            /** Update purchase lines */
            $purchase_lines = PurchaseLine::where('variation_id', $last_id)->get();
            foreach ($purchase_lines as $pl) {
                $pl->variation_id = $first_id;
                $pl->save();

                \Log::info('Purchase lines updated => transaction_id: '. $pl->transaction_id .' id: '. $pl->id);
            }

            if (!count($purchase_lines)) {
                \Log::info('No purchase lines records');
            }

            /** Update transaction sell lines */
            $transaction_sell_lines = TransactionSellLine::where('variation_id', $last_id)->get();
            foreach ($transaction_sell_lines as $tsl) {
                $tsl->variation_id = $first_id;
                $tsl->save();

                \Log::info('Transaction sell lines updated => transaction_id: '. $tsl->transaction_id .' id: '. $tsl->id);
            }

            if (!count($transaction_sell_lines)) {
                \Log::info('No transaction sell lines records');
            }

            /** Update kit has products */
            $kit_has_products = KitHasProduct::where('children_id', $last_id)->get();
            foreach ($kit_has_products as $khp) {
                $khp->children_id = $first_id;
                $khp->save();

                \Log::info('Kit has product updated '. $khp->id);
            }

            if (!count($kit_has_products)) {
                \Log::info('No kit has product records');
            }

            /** Update variation location details */
            $locations = BusinessLocation::where('business_id', $business_id)->pluck('id');
            
            foreach ($locations as $location_id) {
                $warehouses = Warehouse::where('business_location_id', $location_id)->pluck('id');
                
                foreach ($warehouses as $warehouse_id) {
                    $last_vld = VariationLocationDetails::where('variation_id', $last_id)
                        ->where('location_id', $location_id)
                        ->where('warehouse_id', $warehouse_id)
                        ->first();

                    if (empty($last_vld)) {
                        continue;
                    }

                    $last_vld->forceDelete();
                    \Log::info('Vld deleted => id: '. $last_vld->id .' product: '. $last_vld->product_id . ' variation_id: '. $last_vld->variation_id .' location_id: '. $last_vld->location_id .' warehouse_id '. $last_vld->warehouse_id);

                    /** Generate product kardex */
                    if (count($purchase_lines) > 0) {
                        $this->kardex->__generateProductKardex($first_id, $warehouse_id, true, true, $business_id);
                    }
                }
            }

            /** Delete variation */
            $variation = Variation::find($last_id);

            if (!empty($variation)) {
                $variation->forceDelete();

                \Log::info('Variation deleted '. $variation->id);
            }

            DB::commit();

        } catch(\Exception $e) {
            DB::rollBack();
            \Log::emergency("File: " . $e->getFile());
            \Log::emergency("Line: " . $e->getLine(). " Message:" . $e->getMessage());
        }
    }
}
