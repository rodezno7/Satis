<?php

namespace App\Http\Controllers;

use App\Apportionment;
use App\ApportionmentHasTransaction;
use DB;
use Excel;
use Charts;
use App\Unit;

use App\User;
use DateTime;

use App\Quote;
use App\Brands;
use App\Reason;
use Datatables;
use App\Cashier;
use App\Contact;
use App\Product;
use App\Business;
use App\Category;
use App\Employees;
use App\Variation;
use App\Warehouse;
use App\Transaction;
use App\CashRegister;
use App\DiscountCard;
use App\DocumentType;
use App\PurchaseLine;
use App\CustomerGroup;
use App\CashierClosure;
use App\ExpenseCategory;
use App\BusinessLocation;
use App\Customer;
use App\CustomerPortfolio;
use App\Exports\AccountStatementExport;
use App\SellingPriceGroup;
use App\Utils\ProductUtil;
use App\TransactionPayment;
use App\Restaurant\ResTable;
use App\TransactionSellLine;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;
use App\Exports\StockReportExport;
use App\Exports\LostSaleReportExport;
use App\Exports\CostSaleDetailExport;
use App\Exports\AllSalesReportExport;
use App\Exports\SalesTrackingReportExport;
use App\Exports\SalesAdjustmentsReportExport;
use App\Exports\AllSalesWithUtilityReportExport;
use App\Exports\CollectionReport;
use App\Exports\ConnectReport;
use App\Exports\InputOutput;
use App\Exports\DetailedCommissionsReportExport;
use App\Exports\DispatchedProducts;
use App\Exports\LabOrdersReportExport;
use App\Exports\ListPriceReport;
use App\Exports\PaymentNoteReportExport;
use App\Exports\PaymentReportExport;
use App\Exports\PriceListsReport;
use App\Exports\ProductsReportExport;
use App\Exports\SalesPerSellerReportExport;
use App\Exports\TransferSheetReportExport;
use App\Optics\ExternalLab;
use App\Optics\Patient;
use App\Optics\StatusLabOrder;
use App\Utils\BusinessUtil;
use App\Utils\TaxUtil;

class ReportController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $transactionUtil;
    protected $productUtil;
    protected $taxUtil;
    protected $businessUtil;
    protected $delivery_types;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, TaxUtil $taxUtil, BusinessUtil $businessUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->taxUtil = $taxUtil;
        $this->businessUtil = $businessUtil;

        $this->delivery_types = [
            "at_home" => __("order.at_home"),
            "eastern_route" => __("order.eastern_route"),
            "western_route" => __("order.western_route"),
            "caex" => __("order.caex"),
            "location" => __("order.location"),
            "other" => __("order.other")
        ];

        $this->crystal_warehouse = 1;
        
        if (config('app.disable_sql_req_pk')) {
            DB::statement('SET SESSION sql_require_primary_key=0');
        }
    }

    /**
     * Shows profit\loss of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getProfitLoss(Request $request)
    {
        if (!auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');

            //For Opening stock date should be 1 day before
            $day_before_start_date = \Carbon::createFromFormat('Y-m-d', $start_date)->subDay()->format('Y-m-d');
            //Get Opening stock
            $opening_stock = $this->transactionUtil->getOpeningClosingStock($business_id, $day_before_start_date, $location_id, true);

            //Get Closing stock
            $closing_stock = $this->transactionUtil->getOpeningClosingStock(
                $business_id,
                $end_date,
                $location_id
            );

            //Get Purchase details
            $purchase_details = $this->transactionUtil->getPurchaseTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            //Get Purchase Return details
            $purchase_return_details = $this->transactionUtil->getTotalPurchaseReturn(
                $business_id,
                $location_id,
                $start_date,
                $end_date
            );

            //Get Sell details
            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            //Get Sell Return details
            $sell_return_details = $this->transactionUtil->getTotalSellReturn(
                $business_id,
                $location_id,
                $start_date,
                $end_date
            );

            //Get total expense
            $total_expense = $this->transactionUtil->getTotalExpense(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            //Add total shipping charges to total expenses
            $total_expense += $sell_details['total_shipping_charges'];

            //Get total stock adjusted
            $total_stock_adjustment = $this->transactionUtil->getTotalStockAdjustment(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            $total_transfer_shipping_charges = $this->transactionUtil->getTotalTransferShippingCharges(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            //Discounts
            $total_purchase_discount = $this->transactionUtil->getTotalDiscounts($business_id, 'purchase', $start_date, $end_date, $location_id);
            $total_sell_discount = $this->transactionUtil->getTotalDiscounts($business_id, 'sell', $start_date, $end_date, $location_id);

            $data['opening_stock'] = !empty($opening_stock) ? $opening_stock : 0;
            $data['closing_stock'] = !empty($closing_stock) ? $closing_stock : 0;
            $data['total_purchase'] = !empty($purchase_details['total_purchase_exc_tax']) ? $purchase_details['total_purchase_exc_tax'] : 0;
            $data['total_sell'] = !empty($sell_details['total_sell_exc_tax']) ? $sell_details['total_sell_exc_tax'] : 0;
            $data['total_expense'] = !empty($total_expense) ? $total_expense : 0;

            $data['total_adjustment'] = !empty($total_stock_adjustment->total_adjustment) ? $total_stock_adjustment->total_adjustment : 0;

            $data['total_recovered'] = !empty($total_stock_adjustment->total_recovered) ? $total_stock_adjustment->total_recovered : 0;

            $data['total_transfer_shipping_charges'] = !empty($total_transfer_shipping_charges) ? $total_transfer_shipping_charges : 0;

            $data['total_purchase_discount'] = !empty($total_purchase_discount) ? $total_purchase_discount : 0;
            $data['total_sell_discount'] = !empty($total_sell_discount) ? $total_sell_discount : 0;

            $data['total_purchase_return'] = !empty($purchase_return_details['total_purchase_return_exc_tax']) ? $purchase_return_details['total_purchase_return_exc_tax'] : 0;

            $data['total_sell_return'] = !empty($sell_return_details['total_sell_return_exc_tax']) ? $sell_return_details['total_sell_return_exc_tax'] : 0;

            $data['net_profit'] = $data['total_sell'] + $data['closing_stock'] -
                                $data['total_purchase'] - $data['total_sell_discount']-
                                $data['opening_stock'] - $data['total_expense'] -
                                $data['total_adjustment'] + $data['total_recovered'] -
                                $data['total_transfer_shipping_charges'] + $data['total_purchase_discount']
                                + $data['total_purchase_return'] - $data['total_sell_return'];
            return $data;
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('report.profit_loss', compact('business_locations'));
    }

    /**
     * Shows product report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseSell(Request $request)
    {
        if (!auth()->user()->can('purchase_n_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');

            $purchase_details = $this->transactionUtil->getPurchaseTotals($business_id, $start_date, $end_date, $location_id);

            $purchase_return_details = $this->transactionUtil->getTotalPurchaseReturn($business_id, $location_id, $start_date, $end_date);

            $sell_details = $this->transactionUtil->getSellTotals(
                $business_id,
                $start_date,
                $end_date,
                $location_id
            );

            $sell_return_details = $this->transactionUtil->getTotalSellReturn($business_id, $location_id, $start_date, $end_date);

            $total_purchase_return_inc_tax = !empty($purchase_return_details['total_purchase_return_inc_tax']) ? $purchase_return_details['total_purchase_return_inc_tax'] : 0;

            $total_sell_return_inc_tax = !empty($sell_return_details['total_sell_return_inc_tax']) ? $sell_return_details['total_sell_return_inc_tax'] : 0;

            $difference = [
                'total' => $sell_details['total_sell_inc_tax'] + $total_sell_return_inc_tax - $purchase_details['total_purchase_inc_tax'] - $total_purchase_return_inc_tax,
                'due' => $sell_details['invoice_due'] - $purchase_details['purchase_due']
            ];

            return ['purchase' => $purchase_details,
                    'sell' => $sell_details,
                    'total_purchase_return' => $total_purchase_return_inc_tax,
                    'total_sell_return' => $total_sell_return_inc_tax,
                    'difference' => $difference
                ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.purchase_sell')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows report for Supplier
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerSuppliers(Request $request)
    {
        if (!auth()->user()->can('contacts_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $contacts = Contact::where('contacts.business_id', $business_id)
                ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
                ->groupBy('contacts.id')
                ->select(
                    DB::raw("SUM(IF(t.type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(t.type = 'purchase', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_paid"),
                    DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                    DB::raw("SUM(IF(t.type = 'purchase_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as purchase_return_received"),
                    DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                    'contacts.supplier_business_name',
                    'contacts.name',
                    'contacts.id',
                    'contacts.contact_id'
                );
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $contacts->whereIn('t.location_id', $permitted_locations);
            }
            return Datatables::of($contacts)
                ->editColumn('name', function ($row) {
                    $name = $row->name;
                    if (!empty($row->supplier_business_name)) {
                        $name .= ', ' . $row->supplier_business_name;
                    }
                    return '<a href="' . action('ContactController@show', [$row->id]) . '" target="_blank">' .
                            $name .
                        '</a>';
                })
                ->editColumn('total_purchase', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_purchase . '</span>';
                })
                ->editColumn('total_purchase_return', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_purchase_return . '</span>';
                })
                ->editColumn('total_sell_return', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_sell_return . '</span>';
                })
                ->editColumn('total_invoice', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_invoice . '</span>';
                })
                ->addColumn(
                    'due',
                    '<span class="display_currency" data-currency_symbol=true data-highlight=true>{{($total_invoice - $invoice_received - $total_sell_return + $sell_return_paid) - ($total_purchase - $total_purchase_return + $purchase_return_received - $purchase_paid) + ($opening_balance - $opening_balance_paid)}}</span>'
                )
                ->addColumn(
                    'opening_balance_due',
                    '<span class="display_currency" data-currency_symbol=true>{{$opening_balance - $opening_balance_paid}}</span>'
                )
                ->removeColumn('supplier_business_name')
                ->removeColumn('invoice_received')
                ->removeColumn('purchase_paid')
                ->removeColumn('id')
                ->rawColumns(['total_purchase', 'total_invoice', 'due', 'name', 'total_purchase_return', 'total_sell_return', 'opening_balance_due'])
                ->make(true);
        }

        return view('report.contact');
    }

    /**
     * Shows product stock report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockReport(Request $request)
    {

        if (!auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
                                                ->get();
        $allowed_selling_price_group = false;
        foreach ($selling_price_groups as $selling_price_group) {
            if (auth()->user()->can('selling_price_group.' . $selling_price_group->id)) {
                $allowed_selling_price_group = true;
                break;
            }
        }

         //Return the details in ajax call
        if ($request->ajax()) {
            $query = Variation::leftjoin('products as p', 'p.id', '=', 'variations.product_id')
                    ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                    ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                    ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                    ->where('p.business_id', $business_id)
                    ->whereIn('p.type', ['single', 'variable']);

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';

            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);

                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }

            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');

                $query->where('vld.location_id', $location_id);

                $location_filter .= "AND transactions.location_id=$location_id";
            }

            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                die('this is alert');
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }

            $products = $query->select(
                // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
                //     transaction_sell_lines.product_id=products.id) as total_sold"),

                DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity - TSL.quantity_returned , -1* TPL.quantity) ) FROM transactions
                        JOIN transaction_payments  as tp on transactions.id = tp.transaction_id
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                        LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                        AND (TSL.variation_id=variations.id OR TPL.variation_id=variations.id)) as total_sold"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                        AND (TSL.variation_id=variations.id)) as total_transfered"),
                DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                        LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                        AND (SAL.variation_id=variations.id)) as total_adjusted"),
                DB::raw("SUM(vld.qty_available) as stock"),
                'variations.sub_sku as sku',
                'p.name as product',
                'p.type',
                'p.id as product_id',
                'units.short_name as unit',
                'p.enable_stock as enable_stock',
                'variations.sell_price_inc_tax as unit_price',
                'pv.name as product_variation',
                'variations.name as variation_name'
            )->groupBy('variations.id');

            return Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    if ($row->enable_stock) {
                        $stock = $row->stock ? $row->stock : 0 ;
                        return  '<span class="current_stock display_currency" data-orig-value="' . (float)$stock . '" data-unit="' . $row->unit . '" data-currency_symbol=false > ' . (float)$stock . '</span>' . ' ' . $row->unit ;
                    } else {
                        return 'N/A';
                    }
                })
                ->editColumn('product', function ($row) {
                    $name = $row->product;
                    if ($row->type == 'variable') {
                        $name .= ' - ' . $row->product_variation . '-' . $row->variation_name;
                    }
                    return $name;
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold =  (float)$row->total_sold;
                    }

                    return '<span class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . $total_sold . '" data-unit="' . $row->unit . '" >' . $total_sold . '</span> ' . $row->unit;
                })
                ->editColumn('total_transfered', function ($row) {
                    $total_transfered = 0;
                    if ($row->total_transfered) {
                        $total_transfered =  (float)$row->total_transfered;
                    }

                    return '<span class="display_currency total_transfered" data-currency_symbol=false data-orig-value="' . $total_transfered . '" data-unit="' . $row->unit . '" >' . $total_transfered . '</span> ' . $row->unit;
                })
                ->editColumn('total_adjusted', function ($row) {
                    $total_adjusted = 0;
                    if ($row->total_adjusted) {
                        $total_adjusted =  (float)$row->total_adjusted;
                    }

                    return '<span class="display_currency total_adjusted" data-currency_symbol=false  data-orig-value="' . $total_adjusted . '" data-unit="' . $row->unit . '" >' . $total_adjusted . '</span> ' . $row->unit;
                })
                ->editColumn('unit_price', function ($row) use ($allowed_selling_price_group) {
                    $html = '';
                    if ($row->type == 'single' && auth()->user()->can('access_default_selling_price')) {
                        $html .= '<span class="display_currency" data-currency_symbol=true >'
                        . $row->unit_price . '</span>';
                    }

                    if ($allowed_selling_price_group) {
                        if (config('app.business') == 'optics') {
                            $html .= ' <button type="button" class="btn btn-primary btn-xs btn-modal no-print" data-container=".view_modal" data-href="' . action('Optics\ProductController@viewGroupPrice', [$row->product_id]) .'">' . __('lang_v1.view_group_prices') . '</button>';
                        } else {
                            $html .= ' <button type="button" class="btn btn-primary btn-xs btn-modal no-print" data-container=".view_modal" data-href="' . action('ProductController@viewGroupPrice', [$row->product_id]) .'">' . __('lang_v1.view_group_prices') . '</button>';
                        }
                    }

                    return $html;
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id')
                ->rawColumns(['unit_price', 'total_transfered', 'total_sold',
                    'total_adjusted', 'stock'])
                ->make(true);
        }

        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.stock_report_old')
                ->with(compact('categories', 'brands', 'units', 'business_locations'));
    }

    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellReport(Request $request)
    {
        if (!auth()->user()->can('product_sell_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = $request->session()->get('user.business_id');
       

        if ($request->ajax()) {
            $discount = DiscountCard::where('business_id','=',$business_id)->get();

            if(empty($discount))
            {
                 $discount_t =0;
                   
            }else{

                if($discount[0]->value_ == 0)
                {
                    $discount_t = 0;
                }else
                {
                    $discount_t  = $discount[0]->value_/100;
                } 

            }
           

            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('contacts as c', 't.contact_id', '=', 'c.id')
                ->join('transaction_payments as tp', 't.id','=','tp.transaction_id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->select(
                    'p.name as product_name',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    'tp.method as customer',
                    't.id as transaction_id',
                    't.invoice_no',
                    't.transaction_date as transaction_date',
                    DB::raw('IF(tp.method ="card",transaction_sell_lines.unit_price_inc_tax-(transaction_sell_lines.unit_price_inc_tax * '.($discount_t).'),transaction_sell_lines.unit_price_inc_tax ) as unit_price'), 
                     'transaction_sell_lines.unit_price_inc_tax as unit_sale_price',
                    DB::raw('IF(tp.method ="card",(transaction_sell_lines.unit_price_inc_tax * '.($discount_t).'),0 ) as sell_qty'),
                    'transaction_sell_lines.line_discount_type as discount_type',
                    'transaction_sell_lines.line_discount_amount as discount_amount',
                    // 'transaction_sell_lines.item_tax',
                    'tax_rates.name as tax',
                    'u.short_name as unit',
                    'p.brand_id',
                    DB::raw('transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax as subtotal')
                )
                ->groupBy('transaction_sell_lines.id');

            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);

            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }

            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('invoice_no', function ($row) {
                    return '<a data-href="' . action('SellController@show', [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                 })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_sale_price', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->unit_sale_price . '</span>';
                })
                ->editColumn('sell_qty', function ($row) {
                    return '<span class="display_currency sell_qty" data-currency_symbol=false data-orig-value="' . (float)$row->sell_qty . '" data-unit="' . $row->unit . '" >' . (float) $row->sell_qty . '</span> ';
                })
                 ->editColumn('subtotal', function ($row) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol = true data-orig-value="' . $row->subtotal . '">' . $row->subtotal . '</span>';
                 })
                ->editColumn('unit_price', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->unit_price . '</span>';
                })
                ->editColumn('discount_amount', '
                    @if($discount_type == "percentage")
                        {{@number_format($discount_amount)}} %
                    @elseif($discount_type == "fixed")
                        {{@number_format($discount_amount)}}
                    @endif
                    ')
                ->editColumn('tax', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>'.
                            $row->item_tax.
                       '</span>'.'<br>'.'<span class="tax" data-orig-value="'.(float)$row->item_tax.'" data-unit="'.$row->tax.'"><small>('.$row->tax.')</small></span>';
                })
                ->rawColumns(['invoice_no', 'unit_sale_price', 'subtotal', 'sell_qty', 'discount_amount', 'unit_price', 'tax'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id);
        $brands = Brands::brandsDropdown($business_id);
        return view('report.product_sell_report')
            ->with(compact('business_locations', 'customers','brands'));
    }


    /**
     * Shows product stock details
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockDetails(Request $request)
    {
         //Return the details in ajax call
        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $product_id = $request->input('product_id');
            $query = Product::leftjoin('units as u', 'products.unit_id', '=', 'u.id')
                ->join('variations as v', 'products.id', '=', 'v.product_id')
                ->join('product_variations as pv', 'pv.id', '=', 'v.product_variation_id')
                ->leftjoin('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
                ->where('products.business_id', $business_id)
                ->where('products.id', $product_id)
                ->whereNull('v.deleted_at');

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = '';
            if ($permitted_locations != 'all') {
                $query->whereIn('vld.location_id', $permitted_locations);
                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
            }

            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');

                $query->where('vld.location_id', $location_id);

                $location_filter .= "AND transactions.location_id=$location_id";
            }

            $product_details =  $query->select(
                'products.name as product',
                'u.short_name as unit',
                'pv.name as product_variation',
                'v.name as variation',
                'v.sub_sku as sub_sku',
                'v.sell_price_inc_tax',
                DB::raw("SUM(vld.qty_available) as stock"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity - TSL.quantity_returned, -1* TPL.quantity) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                        LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                        WHERE transactions.status='final' AND transactions.type='sell' $location_filter 
                        AND (TSL.variation_id=v.id OR TPL.variation_id=v.id)) as total_sold"),
                DB::raw("(SELECT SUM(IF(transactions.type='sell_transfer', TSL.quantity, 0) ) FROM transactions 
                        LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                        WHERE transactions.status='final' AND transactions.type='sell_transfer' $location_filter 
                        AND (TSL.variation_id=v.id)) as total_transfered"),
                DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                        LEFT JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                        WHERE transactions.status='received' AND transactions.type='stock_adjustment' $location_filter 
                        AND (SAL.variation_id=v.id)) as total_adjusted")
                // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
                //     transaction_sell_lines.variation_id=v.id) as total_sold")
            )
                        ->groupBy('v.id')
                        ->get();

            return view('report.stock_details')
                        ->with(compact('product_details'));
        }
    }

    /**
     * Shows tax report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getTaxReport(Request $request)
    {
        if (!auth()->user()->can('tax_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $location_id = $request->get('location_id');

            $input_tax_details = $this->transactionUtil->getInputTax($business_id, $start_date, $end_date, $location_id);

            $input_tax = view('report.partials.tax_details')->with(['tax_details' => $input_tax_details])->render();

            $output_tax_details = $this->transactionUtil->getOutputTax($business_id, $start_date, $end_date, $location_id);

            $output_tax = view('report.partials.tax_details')->with(['tax_details' => $output_tax_details])->render();

            return ['input_tax' => $input_tax,
                    'output_tax' => $output_tax,
                    'tax_diff' => $output_tax_details['total_tax'] - $input_tax_details['total_tax']
                ];
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.tax_report')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows trending products
     *
     * @return \Illuminate\Http\Response
     */
    public function getTrendingProducts(Request $request)
    {
        if (!auth()->user()->can('trending_product_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = $request->session()->get('user.business_id');
        $filters = $request->only(['category', 'sub_category', 'brand', 'unit', 'limit', 'location_id']);

        $date_range = $request->input('date_range');

        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        }

        $products = $this->productUtil->getTrendingProducts($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($products as $product) {
            $values[] = $product->total_unit_sold;
            $string =  $product->product . ' (' . $product->unit . ')';
            $labels[] = str_replace('"', '', $string);
        }
        
        $chart = Charts::create('bar', 'highcharts')
            ->title(" ")
            ->dimensions(0, 400)
            ->template("material")
            ->values($values)
            ->labels($labels)
            ->elementLabel(__('report.total_unit_sold'));

        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.trending_products')
                    ->with(compact('chart', 'categories', 'brands', 'units', 'business_locations'));
    }

    /**
     * Shows expense report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getExpenseReport(Request $request)
    {
        if (!auth()->user()->can('expense_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $filters = $request->only(['category', 'location_id']);

        $date_range = $request->input('date_range');
        
        if (!empty($date_range)) {
            $date_range_array = explode('~', $date_range);
            $filters['start_date'] = $this->transactionUtil->uf_date(trim($date_range_array[0]));
            $filters['end_date'] = $this->transactionUtil->uf_date(trim($date_range_array[1]));
        } else {
            $filters['start_date'] = \Carbon::now()->startOfMonth()->format('Y-m-d');
            $filters['end_date'] = \Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $expenses = $this->transactionUtil->getExpenseReport($business_id, $filters);

        $values = [];
        $labels = [];
        foreach ($expenses as $expense) {
            $values[] = $expense->total_expense;
            $labels[] = !empty($expense->category) ? $expense->category : __('report.others');
        }

        $chart = Charts::create('bar', 'highcharts')
            ->title(__('report.expense_report'))
            ->dimensions(0, 400)
            ->template("material")
            ->values($values)
            ->labels($labels)
            ->elementLabel(__('report.total_expense'));

        $categories = ExpenseCategory::where('business_id', $business_id)
                            ->pluck('name', 'id');
        
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.expense_report')
                    ->with(compact('chart', 'categories', 'business_locations'));
    }

    /**
     * Shows stock adjustment report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockAdjustmentReport(Request $request)
    {

        if (!auth()->user()->can('stock_adjustment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query =  Transaction::where('business_id', $business_id)
                            ->where('type', 'stock_adjustment');

            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('location_id', $permitted_locations);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            $location_id = $request->get('location_id');
            if (!empty($location_id)) {
                $query->where('location_id', $location_id);
            }

            $stock_adjustment_details = $query->select(
                DB::raw("SUM(final_total) as total_amount"),
                DB::raw("SUM(total_amount_recovered) as total_recovered"),
                DB::raw("SUM(IF(adjustment_type = 'normal', final_total, 0)) as total_normal"),
                DB::raw("SUM(IF(adjustment_type = 'abnormal', final_total, 0)) as total_abnormal")
            )->first();
            return $stock_adjustment_details;
        }
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.stock_adjustment_report')
                    ->with(compact('business_locations'));
    }

    /**
     * Shows register report of a business
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegisterReport(Request $request)
    {
        if (!auth()->user()->can('cash_register_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $registers = CashRegister::join('cashiers', 'cash_registers.cashier_id', 'cashiers.id')
                        ->where('cash_registers.business_id', $business_id)
                        ->select(
                            'cash_registers.id',
                            'cash_registers.date',
                            'cash_registers.status',
                            'cash_registers.total_amount_cash',
                            'cash_registers.total_amount_card',
                            'cash_registers.total_amount_check',
                            'cash_registers.total_amount_transfer',
                            'cash_registers.total_amount_credit',
                            'cashiers.name as cash_register_name'
                        );

            if (!empty($request->input('cashier_id'))) {
                $registers->where('cash_registers.cashier_id', $request->input('cashier_id'));
            }
            if (!empty($request->input('status'))) {
                $registers->where('cash_registers.status', $request->input('status'));
            }
            return Datatables::of($registers)
                ->editColumn('date', function ($row) {
                    if ($row->status == 'close') {
                        return $this->productUtil->format_date($row->date, false);
                    } else {
                        return '';
                    }
                })
                ->editColumn('status', '{{ __("cash_register." . $status) }}')
                ->editColumn('total_amount_cash', function ($row) {
                    if ($row->status == 'close') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount_cash . '</span>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('total_amount_card', function ($row) {
                    if ($row->status == 'close') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount_card . '</span>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('total_amount_check', function ($row) {
                    if ($row->status == 'close') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount_check . '</span>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('total_amount_transfer', function ($row) {
                    if ($row->status == 'close') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount_transfer . '</span>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('total_amount_credit', function ($row) {
                    if ($row->status == 'close') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->total_amount_credit . '</span>';
                    } else {
                        return '';
                    }
                })
                ->addColumn('action', function($row){
                    if ($row->status == 'close') {
                        return '<button type="button" data-href=" '.action('CashRegisterController@show', [$row->id]) .'" class="btn btn-xs btn-info btn-modal" 
                                data-container=".view_register"><i class="fa fa-external-link" aria-hidden="true"></i>' . __("messages.view") .'</button>';
                    }
                    else {
                        return '';
                    }
                })
                ->rawColumns(['action', 'total_amount_cash', 'total_amount_card',
                    'total_amount_check', 'total_amount_transfer', 'total_amount_credit'])
                ->make(true);
        }

        $cashiers = Cashier::forDropdown($business_id, false);

        return view('report.register_report')
                    ->with(compact('cashiers'));
    }

       /**
     * Get daily z cut reports
     * 
     */
    public function getDailyZCutReport(){
        if(!auth()->user()->can('daily_z_cut_report.view')){
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->user()->business_id;
        if(request()->ajax()){
            $cashier_id = request()->input('cashier_id', null);

            $cashier_closures =
                CashierClosure::join('cashiers AS c', 'cashier_closures.cashier_id', 'c.id')
                    ->where('c.business_id', $business_id)
                    ->whereNotNull('cashier_closures.close_date')
                    ->whereNotNull('cashier_closures.closed_by')
                    ->select(
                        'cashier_closures.id',
                        'c.id as cashier_id',
                        'c.business_location_id as location_id',
                        'c.name as cashier_name',
                        'cashier_closures.close_date',
                        'cashier_closures.close_correlative',
                        'cashier_closures.total_cash_amount',
                        'cashier_closures.total_card_amount',
                        'cashier_closures.total_check_amount',
                        'cashier_closures.total_bank_transfer_amount',
                        'cashier_closures.total_return_amount',
                        'cashier_closures.total_physical_amount',
                    );

            /** Filter cashiers permitted */
            $permitted_cashiers = Cashier::permittedCashiers();
            if($permitted_cashiers != 'all'){
                $cashier_closures->whereIn('c.id', $permitted_cashiers);
            }

            /** Filter cashiers */
            if(!is_null($cashier_id)){
                $cashier_closures->where('c.id', $cashier_id);
            }

            return DataTables::of($cashier_closures)
                ->addColumn('action', function($row) {
                    $actions = '<div class="btn-group">
                        <button type="button"
                            class="btn btn-primary dropdown-toggle btn-xs"
                            data-toggle="dropdown" aria-expanded="false">' .__("messages.actions") .
                            ' <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li><a class="view_daily_z_cut"
                                    href="' . action('CashierClosureController@showDailyZCut', [$row->id]) .'">
                                        <i class="fa fa-eye" aria-hidden="true"></i>'. __('messages.view') .'</a>
                                </li>
                                <li><a class="recalc_cc" title="'. __('cashier.recalc_cc')
                                    .'" href="'. url('/reports/recalc-cashier-closure', [$row->id, $row->location_id]).'">
                                    <i class="fa fa-refresh" aria-hidden="true"></i>'. __('messages.update') .'</a>
                                </li>';

                    if(auth()->user()->can('entries.create')){
                        $actions .= '
                            <li><a class="create_acc_entry"
                                title="' . __("accounting.generate_accounting_entry") . '"
                                href="' . action('CashierClosureController@createSaleAccountingEntry', [$row->id]) . '">
                                <i class="fa fa-check-circle"></i> '. __('accounting.accounting') .'</a>
                            </li>';
                    }

                    $actions .= '</ul></div>';

                    return $actions;
                })
                ->editColumn('close_date', '{{ @format_date($close_date) ." ". @format_time($close_date) }}')
                ->editColumn('total_cash_amount', '<span class="display_currency" data-currency_symbol="true">{{ $total_cash_amount }}</span>')
                ->editColumn('total_card_amount', '<span class="display_currency" data-currency_symbol="true">{{ $total_card_amount }}</span>')
                ->editColumn('total_check_amount', '<span class="display_currency" data-currency_symbol="true">{{ $total_check_amount }}</span>')
                ->editColumn('total_bank_transfer_amount', '<span class="display_currency" data-currency_symbol="true">{{ $total_bank_transfer_amount }}</span>')
                ->editColumn('total_return_amount', '<span class="display_currency" data-currency_symbol="true">{{ $total_return_amount }}</span>')
                ->editColumn('total_physical_amount', '<span class="display_currency" data-currency_symbol="true">{{ $total_physical_amount }}</span>')
                ->removeColumn('cashiers.id', 'c.id')
                ->rawColumns(['close_date', 'total_cash_amount', 'total_card_amount', 'total_check_amount', 'total_check_amount', 'total_bank_transfer_amount', 'total_return_amount', 'total_physical_amount', 'action'])
                ->toJson();
        }
        $cashiers = Cashier::forDropdown($business_id, false);

        return view('report.daily_z_cut_report')
            ->with(compact('cashiers'));
    }

    /**
     * Shows sales representative report
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesRepresentativeReport(Request $request)
    {

        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $users = User::allUsersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.sales_representative')
                ->with(compact('users', 'business_locations'));
    }

    /**
     * Shows sales representative total expense
     *
     * @return json
     */
    public function getSalesRepresentativeTotalExpense(Request $request)
    {

        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $filters = $request->only(['expense_for', 'location_id', 'start_date', 'end_date']);

            $total_expense = $this->transactionUtil->getExpenseReport($business_id, $filters, 'total');

            return $total_expense;
        }
    }

    /**
     * Shows sales representative total sales
     *
     * @return json
     */
    public function getSalesRepresentativeTotalSell(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');
            $created_by = $request->get('created_by');

            $sell_details = $this->transactionUtil->getSellTotals($business_id, $start_date, $end_date, $location_id, $created_by);

            return ['total_sell_exc_tax' => $sell_details['total_sell_exc_tax']];
        }
    }

    /**
     * Shows sales representative total commission
     *
     * @return json
     */
    public function getSalesRepresentativeTotalCommission(Request $request)
    {
        if (!auth()->user()->can('sales_representative.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $location_id = $request->get('location_id');
            $commission_agent = $request->get('commission_agent');

            $sell_details = $this->transactionUtil->getTotalSellCommission($business_id, $start_date, $end_date, $location_id, $commission_agent);

            //Get Commision
            $commission_percentage = User::find($commission_agent)->cmmsn_percent;
            $total_commission = $commission_percentage * $sell_details['total_sales_with_commission'] / 100;

            return ['total_sales_with_commission' =>
                        $sell_details['total_sales_with_commission'],
                    'total_commission' => $total_commission,
                    'commission_percentage' => $commission_percentage
                ];
        }
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReport(Request $request)
    {
        if (!auth()->user()->can('stock_expiry_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $query = PurchaseLine::leftjoin(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                            ->leftjoin(
                                'products as p',
                                'purchase_lines.product_id',
                                '=',
                                'p.id'
                            )
                            ->leftjoin(
                                'variations as v',
                                'purchase_lines.variation_id',
                                '=',
                                'v.id'
                            )
                            ->leftjoin(
                                'product_variations as pv',
                                'v.product_variation_id',
                                '=',
                                'pv.id'
                            )
                            ->leftjoin('business_locations as l', 't.location_id', '=', 'l.id')
                            ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                            ->where('t.business_id', $business_id)
                            //->whereNotNull('p.expiry_period')
                            //->whereNotNull('p.expiry_period_type')
                            ->whereNotNull('exp_date')
                            ->where('p.enable_stock', 1)
                            ->whereRaw('purchase_lines.quantity > purchase_lines.quantity_sold + quantity_adjusted + quantity_returned');
                            
            $permitted_locations = auth()->user()->permitted_locations();

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id);
            }

            if (!empty($request->input('category_id'))) {
                $query->where('p.category_id', $request->input('category_id'));
            }
            if (!empty($request->input('sub_category_id'))) {
                $query->where('p.sub_category_id', $request->input('sub_category_id'));
            }
            if (!empty($request->input('brand_id'))) {
                $query->where('p.brand_id', $request->input('brand_id'));
            }
            if (!empty($request->input('unit_id'))) {
                $query->where('p.unit_id', $request->input('unit_id'));
            }
            if (!empty($request->input('exp_date_filter'))) {
                $query->whereDate('exp_date', '<=', $request->input('exp_date_filter'));
            }

            $report = $query->select(
                'p.name as product',
                'p.sku',
                'p.type as product_type',
                'v.name as variation',
                'pv.name as product_variation',
                'l.name as location',
                'mfg_date',
                'exp_date',
                'u.short_name as unit',
                DB::raw("SUM(COALESCE(quantity, 0) - COALESCE(quantity_sold, 0) - COALESCE(quantity_adjusted, 0) - COALESCE(quantity_returned, 0)) as stock_left"),
                't.ref_no',
                't.id as transaction_id',
                'purchase_lines.id as purchase_line_id',
                'purchase_lines.lot_number'
            )
                                    ->groupBy('purchase_lines.id');

            return Datatables::of($report)
                ->editColumn('name', function ($row) {
                    if ($row->product_type == 'variable') {
                        return $row->product . ' - ' .
                        $row->product_variation . ' - ' . $row->variation;
                    } else {
                        return $row->product;
                    }
                })
                ->editColumn('mfg_date', function ($row) {
                    if (!empty($row->mfg_date)) {
                        return $this->productUtil->format_date($row->mfg_date);
                    } else {
                        return '--';
                    }
                })
                ->editColumn('exp_date', function ($row) {
                    if (!empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                            return $this->productUtil->format_date($row->exp_date) . '<br><small>( <span class="time-to-now">' . $row->exp_date . '</span> )</small>';
                        } else {
                            return $this->productUtil->format_date($row->exp_date) . ' &nbsp; <span class="label label-danger">' . __('report.expired') . '</span><br><small>( <span class="time-from-now">' . $row->exp_date . '</span> )</small>';
                        }
                    } else {
                        return '--';
                    }
                })
                ->editColumn('ref_no', function ($row) {
                    return '<button type="button" data-href="' . action('PurchaseController@show', [$row->transaction_id])
                            . '" class="btn btn-link btn-modal" data-container=".view_modal"  >' . $row->ref_no . '</button>';
                })
                ->editColumn('stock_left', function ($row) {
                    return '<span class="display_currency stock_left" data-currency_symbol=false data-orig-value="' . $row->stock_left . '" data-unit="' . $row->unit . '" >' . $row->stock_left . '</span> ' . $row->unit;
                })
                ->addColumn('edit', function ($row) {
                    $html =  '<button type="button" class="btn btn-primary btn-xs stock_expiry_edit_btn" data-transaction_id="' . $row->transaction_id . '" data-purchase_line_id="' . $row->purchase_line_id . '"> <i class="fa fa-edit"></i> ' . __("messages.edit") .
                    '</button>';

                    if (!empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) < 0) {
                             $html .=  ' <button type="button" class="btn btn-warning btn-xs remove_from_stock_btn" data-href="' . action('StockAdjustmentController@removeExpiredStock', [$row->purchase_line_id]) . '"> <i class="fa fa-trash"></i> ' . __("lang_v1.remove_from_stock") .
                            '</button>';
                        }
                    }

                    return $html;
                })
                ->rawColumns(['exp_date', 'ref_no', 'edit', 'stock_left'])
                ->make(true);
        }

        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $view_stock_filter = [
            \Carbon::now()->subDay()->format('Y-m-d') => __('report.expired'),
            \Carbon::now()->addWeek()->format('Y-m-d') => __('report.expiring_in_1_week'),
            \Carbon::now()->addDays(15)->format('Y-m-d') => __('report.expiring_in_15_days'),
            \Carbon::now()->addMonth()->format('Y-m-d') => __('report.expiring_in_1_month'),
            \Carbon::now()->addMonths(3)->format('Y-m-d') => __('report.expiring_in_3_months'),
            \Carbon::now()->addMonths(6)->format('Y-m-d') => __('report.expiring_in_6_months'),
            \Carbon::now()->addYear()->format('Y-m-d') => __('report.expiring_in_1_year')
        ];

        return view('report.stock_expiry_report')
                ->with(compact('categories', 'brands', 'units', 'business_locations', 'view_stock_filter'));
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getStockExpiryReportEditModal(Request $request, $purchase_line_id)
    {

        if (!auth()->user()->can('stock_expiry_report.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        //Return the details in ajax call
        if ($request->ajax()) {
            $purchase_line = PurchaseLine::join(
                'transactions as t',
                'purchase_lines.transaction_id',
                '=',
                't.id'
            )
                                ->join(
                                    'products as p',
                                    'purchase_lines.product_id',
                                    '=',
                                    'p.id'
                                )
                                ->where('purchase_lines.id', $purchase_line_id)
                                ->where('t.business_id', $business_id)
                                ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                                ->first();

            if (!empty($purchase_line)) {
                if (!empty($purchase_line->exp_date)) {
                    $purchase_line->exp_date = date('m/d/Y', strtotime($purchase_line->exp_date));
                }
            }

            return view('report.partials.stock_expiry_edit_modal')
                ->with(compact('purchase_line'));
        }
    }

    /**
     * Update product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStockExpiryReport(Request $request)
    {

        if (!auth()->user()->can('stock_expiry_report.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            //Return the details in ajax call
            if ($request->ajax()) {
                DB::beginTransaction();

                $input = $request->only(['purchase_line_id', 'exp_date']);

                $purchase_line = PurchaseLine::join(
                    'transactions as t',
                    'purchase_lines.transaction_id',
                    '=',
                    't.id'
                )
                                    ->join(
                                        'products as p',
                                        'purchase_lines.product_id',
                                        '=',
                                        'p.id'
                                    )
                                    ->where('purchase_lines.id', $input['purchase_line_id'])
                                    ->where('t.business_id', $business_id)
                                    ->select(['purchase_lines.*', 'p.name', 't.ref_no'])
                                    ->first();

                if (!empty($purchase_line) && !empty($input['exp_date'])) {
                    $purchase_line->exp_date = $this->productUtil->uf_date($input['exp_date']);
                    $purchase_line->save();
                }

                DB::commit();

                $output = ['success' => 1,
                            'msg' => __('lang_v1.updated_succesfully')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __('messages.something_went_wrong')
                        ];
        }

        return $output;
    }

    /**
     * Shows product stock expiry report
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerGroup(Request $request)
    {
        if (!auth()->user()->can('customer_group_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = Transaction::leftjoin('customer_groups AS CG', 'transactions.customer_group_id', '=', 'CG.id')
                        ->join('transaction_sell_lines AS tsl', 'transactions.id', '=', 'tsl.transaction_id')
                        ->join('products as p','tsl.product_id','=','p.id')
                        ->where('transactions.business_id', $business_id)
                        ->where('transactions.type', 'sell')
                        ->where('transactions.status', 'final')
                        ->groupBy('transactions.customer_group_id')
                        ->select(DB::raw("SUM(final_total) as total_sell"), 'CG.name');

            $group_id = $request->get('customer_group_id', null);
            if (!empty($group_id)) {
                $query->where('transactions.customer_group_id', $group_id);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }

            $brands_id = $request->get('brand_id', null);
            if (!empty($brands_id)) {
                $query->where('p.brand_id', $brands_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->total_sell . '</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $customer_group = CustomerGroup::forDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.customer_group')
            ->with(compact('customer_group', 'business_locations'));
    }

    /**
     * Shows product purchase report
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductPurchaseReport(Request $request)
    {

        if (!auth()->user()->can('product_purchase_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = PurchaseLine::join(
                        'transactions as t',
                        'purchase_lines.transaction_id',
                        '=',
                        't.id'
                    )
                    ->join(
                        'variations as v',
                        'purchase_lines.variation_id',
                        '=',
                        'v.id'
                    )
                    ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                    ->join('contacts as c', 't.contact_id', '=', 'c.id')
                    ->join('products as p', 'pv.product_id', '=', 'p.id')
                    ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'purchase')
                    ->select(
                        'p.name as product_name',
                        'p.type as product_type',
                        'pv.name as product_variation',
                        'v.name as variation_name',
                        'c.name as supplier',
                        't.id as transaction_id',
                        't.ref_no',
                        't.transaction_date as transaction_date',
                        'purchase_lines.purchase_price_inc_tax as unit_purchase_price',
                        'purchase_lines.quantity as purchase_qty',
                        'u.short_name as unit',
                        DB::raw('purchase_lines.quantity * purchase_lines.purchase_price_inc_tax as subtotal')
                    )
                    ->groupBy('purchase_lines.id');
            if (!empty($variation_id)) {
                $query->where('purchase_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $supplier_id = $request->get('supplier_id', null);
            if (!empty($supplier_id)) {
                $query->where('t.contact_id', $supplier_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }

                    return $product_name;
                })
                 ->editColumn('ref_no', function ($row) {
                    return '<a data-href="' . action('PurchaseController@show', [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->ref_no . '</a>';
                 })
                 ->editColumn('purchase_qty', function ($row) {
                    return '<span class="display_currency purchase_qty" data-currency_symbol=false data-orig-value="' . (float)$row->purchase_qty . '" data-unit="' . $row->unit . '" >' . (float) $row->purchase_qty . '</span> ' . $row->unit;
                 })
                 ->editColumn('subtotal', function ($row) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol=true data-orig-value="' . $row->subtotal . '">' . $row->subtotal . '</span>';
                 })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('unit_purchase_price', function ($row) {
                    return '<span class="display_currency" data-currency_symbol = true>' . $row->unit_purchase_price . '</span>';
                })
                ->rawColumns(['ref_no', 'unit_purchase_price', 'subtotal', 'purchase_qty'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id);

        return view('report.product_purchase_report')
            ->with(compact('business_locations', 'suppliers'));
    }


    /**
     * Shows product lot report
     *
     * @return \Illuminate\Http\Response
     */
    public function getLotReport(Request $request)
    {
        if (!auth()->user()->can('lot_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

         //Return the details in ajax call
        if ($request->ajax()) {
            $query = Product::where('products.business_id', $business_id)
                    ->leftjoin('units', 'products.unit_id', '=', 'units.id')
                    ->join('variations as v', 'products.id', '=', 'v.product_id')
                    ->join('purchase_lines as pl', 'v.id', '=', 'pl.variation_id')
                    ->leftjoin(
                        'transaction_sell_lines_purchase_lines as tspl',
                        'pl.id',
                        '=',
                        'tspl.purchase_line_id'
                    )
                    ->join('transactions as t', 'pl.transaction_id', '=', 't.id');

            $permitted_locations = auth()->user()->permitted_locations();
            $location_filter = 'WHERE ';

            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);

                $locations_imploded = implode(', ', $permitted_locations);
                $location_filter = " LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id IN ($locations_imploded) AND ";
            }

            if (!empty($request->input('location_id'))) {
                $location_id = $request->input('location_id');
                $query->where('t.location_id', $location_id);

                $location_filter = "LEFT JOIN transactions as t2 on pls.transaction_id=t2.id WHERE t2.location_id=$location_id AND ";
            }

            if (!empty($request->input('category_id'))) {
                $query->where('products.category_id', $request->input('category_id'));
            }

            if (!empty($request->input('sub_category_id'))) {
                $query->where('products.sub_category_id', $request->input('sub_category_id'));
            }

            if (!empty($request->input('brand_id'))) {
                $query->where('products.brand_id', $request->input('brand_id'));
            }

            if (!empty($request->input('unit_id'))) {
                $query->where('products.unit_id', $request->input('unit_id'));
            }

            $products = $query->select(
                'products.name as product',
                'v.name as variation_name',
                'sub_sku',
                'pl.lot_number',
                'pl.exp_date as exp_date',
                DB::raw("( COALESCE((SELECT SUM(quantity - quantity_returned) from purchase_lines as pls $location_filter variation_id = v.id AND lot_number = pl.lot_number), 0) - 
                    SUM(COALESCE((tspl.quantity - tspl.qty_returned), 0))) as stock"),
                // DB::raw("(SELECT SUM(IF(transactions.type='sell', TSL.quantity, -1* TPL.quantity) ) FROM transactions
                //         LEFT JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id

                //         LEFT JOIN purchase_lines AS TPL ON transactions.id=TPL.transaction_id

                //         WHERE transactions.status='final' AND transactions.type IN ('sell', 'sell_return') $location_filter
                //         AND (TSL.product_id=products.id OR TPL.product_id=products.id)) as total_sold"),

                DB::raw("COALESCE(SUM(IF(tspl.sell_line_id IS NULL, 0, (tspl.quantity - tspl.qty_returned)) ), 0) as total_sold"),
                DB::raw("COALESCE(SUM(IF(tspl.stock_adjustment_line_id IS NULL, 0, tspl.quantity ) ), 0) as total_adjusted"),
                'products.type',
                'units.short_name as unit'
            )
            ->whereNotNull('pl.lot_number')
            ->groupBy('v.id')
            ->groupBy('pl.lot_number');

            return Datatables::of($products)
                ->editColumn('stock', function ($row) {
                    $stock = $row->stock ? $row->stock : 0 ;
                    return '<span class="display_currency total_stock" data-currency_symbol=false data-orig-value="' . (float)$stock . '" data-unit="' . $row->unit . '" >' . (float)$stock . '</span> ' . $row->unit;
                })
                ->editColumn('product', function ($row) {
                    if ($row->variation_name != 'DUMMY') {
                        return $row->product . ' (' . $row->variation_name . ')';
                    } else {
                        return $row->product;
                    }
                })
                ->editColumn('total_sold', function ($row) {
                    if ($row->total_sold) {
                        return '<span class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . (float)$row->total_sold . '" data-unit="' . $row->unit . '" >' . (float)$row->total_sold . '</span> ' . $row->unit;
                    } else {
                        return '0' . ' ' . $row->unit;
                    }
                })
                ->editColumn('total_adjusted', function ($row) {
                    if ($row->total_adjusted) {
                        return '<span class="display_currency total_adjusted" data-currency_symbol=false data-orig-value="' . (float)$row->total_adjusted . '" data-unit="' . $row->unit . '" >' . (float)$row->total_adjusted . '</span> ' . $row->unit;
                    } else {
                        return '0' . ' ' . $row->unit;
                    }
                })
                ->editColumn('exp_date', function ($row) {
                    if (!empty($row->exp_date)) {
                        $carbon_exp = \Carbon::createFromFormat('Y-m-d', $row->exp_date);
                        $carbon_now = \Carbon::now();
                        if ($carbon_now->diffInDays($carbon_exp, false) >= 0) {
                            return $this->productUtil->format_date($row->exp_date) . '<br><small>( <span class="time-to-now">' . $row->exp_date . '</span> )</small>';
                        } else {
                            return $this->productUtil->format_date($row->exp_date) . ' &nbsp; <span class="label label-danger">' . __('report.expired') . '</span><br><small>( <span class="time-from-now">' . $row->exp_date . '</span> )</small>';
                        }
                    } else {
                        return '--';
                    }
                })
                ->removeColumn('unit')
                ->removeColumn('id')
                ->removeColumn('variation_name')
                ->rawColumns(['exp_date', 'stock', 'total_sold', 'total_adjusted'])
                ->make(true);
        }

        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->pluck('name', 'id');
        $brands = Brands::where('business_id', $business_id)
                            ->pluck('name', 'id');
        $units = Unit::where('business_id', $business_id)
                            ->pluck('short_name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.lot_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations'));
    }

    /**
     * Shows purchase payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function purchasePaymentReport(Request $request)
    {
        if (!auth()->user()->can('purchase_payment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $supplier_id = $request->get('supplier_id', null);
            $contact_filter1 = !empty($supplier_id) ? "AND t.contact_id=$supplier_id" : '';
            $contact_filter2 = !empty($supplier_id) ? "AND transactions.contact_id=$supplier_id" : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                    $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'purchase');
            })
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type='purchase' AND transaction_payments.parent_id IS NULL $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type='purchase' AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                              
                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT c.name FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as supplier"),
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    't.ref_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_no',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            
            return Datatables::of($query)
                 ->editColumn('ref_no', function ($row) {
                    if (!empty($row->ref_no)) {
                        return '<a data-href="' . action('PurchaseController@show', [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->ref_no . '</a>';
                    } else {
                        return '';
                    }
                 })
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('method', function ($row) {
                    $method = __('lang_v1.' . $row->method);
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method = __('lang_v1.custom_payment_1') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method = __('lang_v1.custom_payment_2') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method = __('lang_v1.custom_payment_3') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-currency_symbol = true data-orig-value="' . $row->amount . '">' . $row->amount . '</span>';
                })
                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action("TransactionPaymentController@viewPayment", [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['ref_no', 'amount', 'method', 'action'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $suppliers = Contact::suppliersDropdown($business_id, false);

        return view('report.purchase_payment_report')
            ->with(compact('business_locations', 'suppliers'));
    }

    /**
     * Shows sell payment report
     *
     * @return \Illuminate\Http\Response
     */
    public function sellPaymentReport(Request $request)
    {
        if (!auth()->user()->can('sell_payment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        if ($request->ajax()) {
            $customer_id = $request->get('supplier_id', null);
            $contact_filter1 = !empty($customer_id) ? "AND t.contact_id=$customer_id" : '';
            $contact_filter2 = !empty($customer_id) ? "AND transactions.contact_id=$customer_id" : '';

            $query = TransactionPayment::leftjoin('transactions as t', function ($join) use ($business_id) {
                    $join->on('transaction_payments.transaction_id', '=', 't.id')
                    ->where('t.business_id', $business_id)
                    ->where('t.type', 'sell');
            })
                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                ->where('transaction_payments.business_id', $business_id)
                ->where(function ($q) use ($business_id, $contact_filter1, $contact_filter2) {
                    $q->whereRaw("(transaction_payments.transaction_id IS NOT NULL AND t.type='sell' AND transaction_payments.parent_id IS NULL $contact_filter1)")
                        ->orWhereRaw("EXISTS(SELECT * FROM transaction_payments as tp JOIN transactions ON tp.transaction_id = transactions.id WHERE transactions.type='sell' AND transactions.business_id = $business_id AND tp.parent_id=transaction_payments.id $contact_filter2)");
                })
                ->select(
                    DB::raw("IF(transaction_payments.transaction_id IS NULL, 
                                (SELECT c.name FROM transactions as ts
                                JOIN contacts as c ON ts.contact_id=c.id 
                                WHERE ts.id=(
                                        SELECT tps.transaction_id FROM transaction_payments as tps
                                        WHERE tps.parent_id=transaction_payments.id LIMIT 1
                                    )
                                ),
                                (SELECT c.name FROM transactions as ts JOIN
                                    contacts as c ON ts.contact_id=c.id
                                    WHERE ts.id=t.id 
                                )
                            ) as customer"),
                    'transaction_payments.amount',
                    'method',
                    'paid_on',
                    'transaction_payments.payment_ref_no',
                    'transaction_payments.document',
                    't.invoice_no',
                    't.id as transaction_id',
                    'cheque_number',
                    'card_transaction_number',
                    'bank_account_number',
                    'transaction_payments.id as DT_RowId'
                )
                ->groupBy('transaction_payments.id');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(paid_on)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }
            
            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }
            return Datatables::of($query)
                 ->editColumn('invoice_no', function ($row) {
                    if (!empty($row->transaction_id)) {
                        return '<a data-href="' . action('SellController@show', [$row->transaction_id])
                            . '" href="#" data-container=".view_modal" class="btn-modal">' . $row->invoice_no . '</a>';
                    } else {
                        return '';
                    }
                 })
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('method', function ($row) {
                    $method = __('lang_v1.' . $row->method);
                    if ($row->method == 'cheque') {
                        $method .= '<br>(' . __('lang_v1.cheque_no') . ': ' . $row->cheque_number . ')';
                    } elseif ($row->method == 'card') {
                        $method .= '<br>(' . __('lang_v1.card_transaction_no') . ': ' . $row->card_transaction_number . ')';
                    } elseif ($row->method == 'bank_transfer') {
                        $method .= '<br>(' . __('lang_v1.bank_account_no') . ': ' . $row->bank_account_number . ')';
                    } elseif ($row->method == 'custom_pay_1') {
                        $method = __('lang_v1.custom_payment_1') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_2') {
                        $method = __('lang_v1.custom_payment_2') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    } elseif ($row->method == 'custom_pay_3') {
                        $method = __('lang_v1.custom_payment_3') . '<br>(' . __('lang_v1.transaction_no') . ': ' . $row->transaction_no . ')';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency paid-amount" data-orig-value="' . $row->amount . '" data-currency_symbol = true>' . $row->amount . '</span>';
                })
                ->addColumn('action', '<button type="button" class="btn btn-primary btn-xs view_payment" data-href="{{ action("TransactionPaymentController@viewPayment", [$DT_RowId]) }}">@lang("messages.view")
                    </button> @if(!empty($document))<a href="{{asset("/uploads/documents/" . $document)}}" class="btn btn-success btn-xs" download=""><i class="fa fa-download"></i> @lang("purchase.download_document")</a>@endif')
                ->rawColumns(['invoice_no', 'amount', 'method', 'action'])
                ->make(true);
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $customers = Contact::customersDropdown($business_id, false);

        return view('report.sell_payment_report')
            ->with(compact('business_locations', 'customers'));
    }


    /**
     * Shows tables report
     *
     * @return \Illuminate\Http\Response
     */
    public function getTableReport(Request $request)
    {
        if (!auth()->user()->can('table_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = ResTable::leftjoin('transactions AS T', 'T.res_table_id', '=', 'res_tables.id')
                        ->where('T.business_id', $business_id)
                        ->where('T.type', 'sell')
                        ->where('T.status', 'final')
                        ->groupBy('res_tables.id')
                        ->select(DB::raw("SUM(final_total) as total_sell"), 'res_tables.name as table');

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('T.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->total_sell . '</span>';
                })
                ->rawColumns(['total_sell'])
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.table_report')
            ->with(compact('business_locations'));
    }

    /**
     * Shows service staff report
     *
     * @return \Illuminate\Http\Response
     */
    public function getServiceStaffReport(Request $request)
    {
        if (!auth()->user()->can('service_staff_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $query = User::leftjoin('transactions AS T', 'T.res_waiter_id', '=', 'users.id')
                        ->where('T.business_id', $business_id)
                        ->where('T.type', 'sell')
                        ->where('T.status', 'final')
                        ->groupBy('users.id')
                        ->select(DB::raw("SUM(final_total) as total_sell"), DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as service_staff_name"));

            $location_id = $request->get('location_id', null);
            if (!empty($location_id)) {
                $query->where('T.location_id', $location_id);
            }

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            return Datatables::of($query)
                ->editColumn('total_sell', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->total_sell . '</span>';
                })
                ->rawColumns(['total_sell'])
                ->filterColumn('service_staff_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->make(true);
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        return view('report.service_staff_report')
            ->with(compact('business_locations'));
    }

    /**
     * Shows product sell report grouped by date
     *
     * @return \Illuminate\Http\Response
     */
    public function getproductSellGroupedReport(Request $request)
    {

        if (!auth()->user()->can('product_sell_grouped_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->get('location_id', null);

        $vld_str = '';
        if(!empty($location_id)) {
            $vld_str = "AND vld.location_id=$location_id";
        }

        if ($request->ajax()) {
            $variation_id = $request->get('variation_id', null);
            $query = TransactionSellLine::join(
                'transactions as t',
                'transaction_sell_lines.transaction_id',
                '=',
                't.id'
            )
                ->join(
                    'variations as v',
                    'transaction_sell_lines.variation_id',
                    '=',
                    'v.id'
                )
                ->join('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')
                ->join('products as p', 'pv.product_id', '=', 'p.id')
                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')
                ->where('t.business_id', $business_id)
                ->where('t.type', 'sell')
                ->select(
                    'p.name as product_name',
                    'p.enable_stock',
                    'p.type as product_type',
                    'pv.name as product_variation',
                    'v.name as variation_name',
                    't.id as transaction_id',
                    't.transaction_date as transaction_date',
                    DB::raw('DATE_FORMAT(t.transaction_date, "%Y-%m-%d") as formated_date'),
                    DB::raw("(SELECT SUM(vld.qty_available) FROM variation_location_details as vld WHERE vld.variation_id=v.id $vld_str) as current_stock"),
                    DB::raw('SUM(transaction_sell_lines.quantity) as total_qty_sold'),
                    'u.short_name as unit',
                    DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as subtotal')
                )
                ->groupBy('v.id')
                ->groupBy('formated_date');

            if (!empty($variation_id)) {
                $query->where('transaction_sell_lines.variation_id', $variation_id);
            }
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('t.location_id', $permitted_locations);
            }

            if (!empty($location_id)) {
                $query->where('t.location_id', $location_id);
            }

            $customer_id = $request->get('customer_id', null);
            if (!empty($customer_id)) {
                $query->where('t.contact_id', $customer_id);
            }

            return Datatables::of($query)
                ->editColumn('product_name', function ($row) {
                    $product_name = $row->product_name;
                    if ($row->product_type == 'variable') {
                        $product_name .= ' - ' . $row->product_variation . ' - ' . $row->variation_name;
                    }

                    return $product_name;
                })
                ->editColumn('transaction_date', '{{@format_date($formated_date)}}')
                ->editColumn('total_qty_sold', function ($row) {
                    return '<span class="display_currency sell_qty" data-currency_symbol=false data-orig-value="' . (float)$row->total_qty_sold . '" data-unit="' . $row->unit . '" >' . (float) $row->total_qty_sold . '</span> ' .$row->unit;
                })
                ->editColumn('current_stock', function ($row) {
                    if($row->enable_stock) {
                        return '<span class="display_currency current_stock" data-currency_symbol=false data-orig-value="' . (float)$row->current_stock . '" data-unit="' . $row->unit . '" >' . (float) $row->current_stock . '</span> ' .$row->unit;
                    } else {
                        return '';
                    }
                    
                })
                 ->editColumn('subtotal', function ($row) {
                    return '<span class="display_currency row_subtotal" data-currency_symbol = true data-orig-value="' . $row->subtotal . '">' . $row->subtotal . '</span>';
                 })
                
                ->rawColumns(['current_stock', 'subtotal', 'total_qty_sold'])
                ->make(true);
        }
    }

    /**
     * Show sales and stock adjustments report view.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getSalesAndAdjustmentsReport(Request $request)
    {
        if (! auth()->user()->can('sell_n_adjustment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            # Params
            $business_id = $request->session()->get('user.business_id');
            $location_id = $request->input('location_id');
            $month = $request->input('month');

            # Query
            if (! is_null($location_id) && ! is_null($month)) {
                $query = DB::select(
                    'CALL get_sales_n_adjustments_report(?, ?, ?)',
                    array($location_id, $month, $business_id)
                );
            } else {
                $query = [];
            }

            return Datatables::of($query)
                ->editColumn('unit_cost', function ($row) {
                    $unit_cost = 0;
                    if ($row->unit_cost) {
                        $unit_cost = (float)$row->unit_cost;
                    }

                    return '<span class="display_currency" data-currency_symbol=true>' . $unit_cost . '</span>';
                })
                ->editColumn('unit_price', function ($row) {
                    $unit_price = 0;
                    if ($row->unit_price) {
                        $unit_price = (float)$row->unit_price;
                    }

                    return '<span class="display_currency" data-currency_symbol=true>' . $unit_price . '</span>';
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold = (float)$row->total_sold;
                    }

                    return '<span class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . $total_sold . '" data-unit="' . $row->unit . '" >' . $total_sold . '</span>';
                })
                ->editColumn('input_adjustment', function ($row) {
                    $input_adjustment = 0;
                    if ($row->input_adjustment) {
                        $input_adjustment = (float)$row->input_adjustment;
                    }

                    return '<span class="display_currency input_adjustment" data-currency_symbol=false data-orig-value="' . $input_adjustment . '" data-unit="' . $row->unit . '" >' . $input_adjustment . '</span>';
                })
                ->editColumn('output_adjustment', function ($row) {
                    $output_adjustment = 0;
                    if ($row->output_adjustment) {
                        $output_adjustment = (float)$row->output_adjustment;
                    }

                    return '<span class="display_currency output_adjustment" data-currency_symbol=false data-orig-value="' . $output_adjustment . '" data-unit="' . $row->unit . '" >' . $output_adjustment . '</span>';
                })
                ->rawColumns(['unit_price', 'unit_cost', 'total_sold', 'input_adjustment', 'output_adjustment'])
                ->toJson();
        }

        # Data form
        $locations = BusinessLocation::forDropdown($business_id);
        $months = array(
            '1' => __('accounting.january'),
            '2' => __('accounting.february'),
            '3' => __('accounting.march'),
            '4' => __('accounting.april'),
            '5' => __('accounting.may'),
            '6' => __('accounting.june'),
            '7' => __('accounting.july'),
            '8' => __('accounting.august'),
            '9' => __('accounting.september'),
            '10' => __('accounting.october'),
            '11' => __('accounting.november'),
            '12' => __('accounting.december')
        );
        
        return view('report.sell_n_adjustment_report')
            ->with(compact('locations', 'months'));
    }

    /**
     * Generates sales and stock adjustments report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postSalesAndAdjustmentsReport(Request $request)
    {
        if (! auth()->user()->can('sell_n_adjustment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        # Params
        $business_id = $request->session()->get('user.business_id');
        $location_id = $request->input('location_id');
        $month = $request->input('month');
        $size = $request->input('size');
        $report_type = $request->input('report_type');

        # Query
        $query = DB::select(
            'CALL get_sales_n_adjustments_report(?, ?, ?)',
            array($location_id, $month, $business_id)
        );

        # Data
        $months = array(
            '1' => __('accounting.january'),
            '2' => __('accounting.february'),
            '3' => __('accounting.march'),
            '4' => __('accounting.april'),
            '5' => __('accounting.may'),
            '6' => __('accounting.june'),
            '7' => __('accounting.july'),
            '8' => __('accounting.august'),
            '9' => __('accounting.september'),
            '10' => __('accounting.october'),
            '11' => __('accounting.november'),
            '12' => __('accounting.december')
        );

        $month_name = $months[$month];

        $business = Business::find($business_id);

        $location = BusinessLocation::find($location_id);

        # Generates report
        if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.sales_adjustments_report_pdf',
				compact('query', 'size', 'month_name', 'business', 'location'));
            $pdf->setPaper('letter', 'landscape');

			return $pdf->stream(__('report.consumption_report') . ' - ' . $month_name . '.pdf');

		} else {
			return Excel::download(
                new SalesAdjustmentsReportExport($query, $size, $month_name, $business, $location),
                __('report.consumption_report') . ' - ' . $month_name . '.xlsx'
            );
		}
    }

    /**
     * Show cost of sale detail report view.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getCostOfSaleDetailReport(Request $request)
    {
        if (! auth()->user()->can('cost_of_sale_detail_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            // Params
            $business_id = $request->session()->get('user.business_id');
            $warehouse_id = $request->input('warehouse_id');
            $document_type_id = $request->input('document_type_id');
            $start = ! empty(request()->start_date) ? request()->start_date : '';
            $end = ! empty(request()->end_date) ? request()->end_date : '';
            $type = $request->input('type');

            // Query
            if (! is_null($warehouse_id) && ! is_null($start) && ! is_null($end)) {
                $query = collect(DB::select(
                    'CALL get_cost_of_sale_detail_report(?, ?, ?, ?)',
                    array($warehouse_id, $business_id, $start, $end)
                ));
            } else {
                $query = [];
            }

            // Document type filter
            if (! is_null($document_type_id)) {
                $query = $query->where('document_type_id', $document_type_id);
            }

            // Type filter
            if (! is_null($type)) {
                if ($type != 'both') {
                    $query = $query->where('type', $type);
                }
            }

            return Datatables::of($query)
                ->editColumn('input', function ($row) {
                    $input = 0;
                    if ($row->input) {
                        $input = (float)$row->input;
                    }

                    return '<span class="display_currency input" data-currency_symbol=false data-orig-value="' . $input . '" >' . $input . '</span>';
                })
                ->editColumn('output', function ($row) {
                    $output = 0;
                    if ($row->output) {
                        $output = (float)$row->output;
                    }

                    return '<span class="display_currency output" data-currency_symbol=false data-orig-value="' . $output . '" >' . $output . '</span>';
                })
                ->editColumn('annulled', function ($row) {
                    $annulled = 0;
                    if ($row->annulled) {
                        $annulled = (float)$row->annulled;
                    }

                    return '<span class="display_currency annulled" data-currency_symbol=false data-orig-value="' . $annulled . '" >' . $annulled . '</span>';
                })
                ->rawColumns(['input', 'output', 'annulled'])
                ->toJson();
        }

        // Data form
        $warehouses = Warehouse::forDropdown($business_id, false);
        $document_types = DocumentType::forDropdown($business_id, false);
        $types = [
            'both' => __('inflow_outflow.inputs_and_outputs'),
            'input' => __('inflow_outflow.inputs'),
            'output' => __('inflow_outflow.outputs'),
        ];
        
        return view('report.cost_of_sale_detail_report')
            ->with(compact('warehouses', 'document_types', 'types'));
    }

    /**
     * Generates cost of sale detail report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postCostOfSaleDetailReport(Request $request)
    {
        if (! auth()->user()->can('cost_of_sale_detail_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Params
        $business_id = $request->session()->get('user.business_id');
        $warehouse_id = $request->input('warehouse_id');
        $warehouse_name = Warehouse::find($warehouse_id);
        $document_type_id = $request->input('document_type_id');
        $start = ! empty(request()->start_date) ? request()->start_date : '';
        $end = ! empty(request()->end_date) ? request()->end_date : '';
        $type = $request->input('type');
        $size = $request->input('size');
        $report_type = $request->input('report_type');

        // Query
        if (! is_null($warehouse_id) && ! is_null($start) && ! is_null($end)) {
            $query = collect(DB::select(
                'CALL get_cost_of_sale_detail_report(?, ?, ?, ?)',
                array($warehouse_id, $business_id, $start, $end)
            ));
        } else {
            $query = [];
        }

        // Document type filter
        if (! is_null($document_type_id)) {
            $query = $query->where('document_type_id', $document_type_id);
        }

        // Type filter
        if (! is_null($type)) {
            if ($type != 'both') {
                $query = $query->where('type', $type);
            }
        }

        $business = Business::find($business_id);

        // Generates report
        if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.cost_of_sale_detail_report_pdf',
				compact('query', 'size', 'business', 'start', 'end'));
            $pdf->setPaper('a4', 'landscape');

			return $pdf->stream(__('report.warehouse_closure_report') . ' - ' . $warehouse_name->name . '.pdf');

		} else {
			return Excel::download(
                new CostSaleDetailExport($query, $business, $start, $end),
                __('report.warehouse_closure_report') . ' - ' . $warehouse_name->name .  '.xlsx'
            );
		}
    }

    /**
     * Shows stock report form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showStockReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);

        if ($request->ajax()) {
            # Filters
            $location_id = !empty($request->input('location_id')) ? $request->input('location_id') : 0;
            $warehouse_id = !empty($request->input('warehouse_id')) ? $request->input('warehouse_id') : 0;
            $category_id = !empty($request->input('category_id')) ? $request->input('category_id') : 0;
            $sub_category_id = !empty($request->input('sub_category_id')) ? $request->input('sub_category_id') : 0; 
            $brand_id = !empty($request->input('brand_id')) ? $request->input('brand_id') : 0;
            $unit_id = !empty($request->input('unit_id')) ? $request->input('unit_id') : 0;
            $contact_id = !empty($request->input('contact_id')) ? $request->input('contact_id') : 0;
            $start = !empty(request()->start_date) ? request()->start_date : '';
            $end = !empty(request()->end_date) ? request()->end_date : '';
            $stock_filter = $request->input('stock_filter');

            # Products
            $products = collect(DB::select(
                'CALL generate_stock_report(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                array($business_id, $location_id, $warehouse_id, $category_id, $sub_category_id, $brand_id, $unit_id, $contact_id, $start, $end, $stock_filter)
            ));

            return Datatables::of($products)
                ->editColumn('stock', function ($row) use ($product_settings) {
                    $stock = 0;

                    if ($row->stock) {
                        $stock =  (float)$row->stock;
                    }

                    if ($product_settings['show_stock_without_decimals']) {
                        $html = '<span class="display_currency stock" data-currency_symbol=false data-orig-value="' . $stock . '" data-precision="0">' . $stock . '</span>';
                    } else {
                        $html = '<span class="display_currency stock" data-currency_symbol=false data-orig-value="' . $stock . '">' . $stock . '</span>';
                    }

                    return $html;
                })
                ->editColumn('vld_stock', function ($row) use ($product_settings) {
                    $vld_stock = 0;

                    if ($row->vld_stock) {
                        $vld_stock =  (float)$row->vld_stock;
                    }

                    if ($product_settings['show_stock_without_decimals']) {
                        $html = '<span class="display_currency vld_stock" data-currency_symbol=false data-orig-value="' . $vld_stock . '" data-precision="0">' . $vld_stock . '</span>';
                    } else {
                        $html = '<span class="display_currency vld_stock" data-currency_symbol=false data-orig-value="' . $vld_stock . '">' . $vld_stock . '</span>';
                    }

                    return $html;
                })
                ->editColumn('total_sold', function ($row) {
                    $total_sold = 0;
                    if ($row->total_sold) {
                        $total_sold =  (float)$row->total_sold;
                    }
                    return '<span class="display_currency total_sold" data-currency_symbol=false data-orig-value="' . $total_sold . '">' . $total_sold . '</span>';
                })
                ->editColumn('unit_cost', function ($row) {
                    $unit_cost = 0;
                    if ($row->unit_cost) {
                        $unit_cost =  (float)$row->unit_cost;
                    }

                    return '<span <span class="display_currency" data-currency_symbol=true>' . $unit_cost . '</span>';
                })
                ->editColumn('unit_price', function ($row) {
                    $unit_price = 0;
                    if ($row->unit_price) {
                        $unit_price =  (float)$row->unit_price;
                    }

                    return '<span <span class="display_currency" data-currency_symbol=true>' . $unit_price . '</span>';
                })
                ->addColumn('total_value', function ($row) {
                    $total_value = 0;
                    
                    if ($row->unit_cost && $row->stock) {
                        $total_value = (float)($row->unit_cost * $row->stock);
                    }

                    return '<span class="display_currency total_value" data-currency_symbol=true data-orig-value="' . $total_value . '">' . $total_value . '</span>';
                })
                ->removeColumn('enable_stock')
                ->removeColumn('unit')
                ->removeColumn('id')
                ->rawColumns(['unit_cost', 'unit_price', 'total_sold', 'stock', 'vld_stock', 'total_value'])
                ->toJson();
        }

        $categories = Category::where('business_id', $business_id)
            ->where('parent_id', 0)
            ->pluck('name', 'id');

        $brands = Brands::where('business_id', $business_id)
            ->pluck('name', 'id');

        $units = Unit::where('business_id', $business_id)
            ->pluck('short_name', 'id');

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        $warehouses = Warehouse::forDropdown($business_id, false);

        return view('report.stock_report')
            ->with(compact('categories', 'brands', 'units', 'business_locations', 'warehouses'));
    }

    /**
     * Gets stock report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postStockReport(Request $request)
    {
        if (! auth()->user()->can('stock_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::find($business_id);
        $product_settings = empty($business->product_settings) ? null : json_decode($business->product_settings, true);

        # Location filter
        $location_id = !empty($request->input('location_id')) ? $request->input('location_id') : 0;
        $warehouse_id = !empty($request->input('warehouse_id')) ? $request->input('warehouse_id') : 0;
        $category_id = !empty($request->input('category_id')) ? $request->input('category_id') : 0;
        $sub_category_id = !empty($request->input('sub_category_id')) ? $request->input('sub_category_id') : 0; 
        $brand_id = !empty($request->input('brand_id')) ? $request->input('brand_id') : 0;
        $unit_id = !empty($request->input('unit_id')) ? $request->input('unit_id') : 0;
        $contact_id = !empty($request->input('contact_id')) ? $request->input('contact_id') : 0;
        $start = !empty($request->input('start_date')) ? $request->input('start_date') : '';
        $end = !empty($request->input('end_date')) ? $request->input('end_date') : '';
        $stock_filter = $request->input('stock_filter', 0);

        # Products
        $products = collect(DB::select(
            'CALL generate_stock_report(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array($business_id, $location_id, $warehouse_id, $category_id, $sub_category_id, $brand_id, $unit_id, $contact_id, $start, $end, $stock_filter)
        ));

        // if ($stock_filter != 1) {
        //     $products = $products->where('stock', '>', 0);
        // }

        $size = $request->input('size');

        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			// $pdf = \PDF::loadView('reports.stock_report_pdf',
			// 	compact('products', 'size', 'business'));

			// return $pdf->stream('stock_report.pdf');

            return view('reports.stock_report_pdf')
                ->with(compact('products', 'size', 'business', 'start', 'end', 'product_settings'));

		} else {
			return Excel::download(
                new StockReportExport($products, $business, $start, $end, $product_settings, $this->transactionUtil),
                __('report.stock_report') . '.xlsx'
            );
		}
    }

    /**
     * Retrieves supliers list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSuppliers()
    {
        if (request()->ajax()) {
            $term = request()->q;

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');

            $query = Contact::where('business_id', $business_id);
            
            $suppliers = $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . $term .'%')
                        ->orWhere('supplier_business_name', 'like', '%' . $term .'%')
                        ->orWhere('contacts.contact_id', 'like', '%' . $term .'%');
                })
                ->select('contacts.id', 'name as text', 'supplier_business_name as business_name', 'contacts.contact_id')
                ->onlySuppliers()
                ->get();

            return json_encode($suppliers);
        }
    }

    /**
     * Show sales tracking report view.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSalesTrackingReport(Request $request)
    {
        if (! auth()->user()->can('sales_tracking_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            # Date filter
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
            } else {
                $start = '';
                $end =  '';
            }

            # Customer filter
            if (! empty($request->input('customer_id'))) {
                $customer_id = $request->input('customer_id');
            } else {
                $customer_id = 0;
            }

            # Invoiced filter
            if (! is_null($request->input('invoiced'))) {
                $invoiced = $request->input('invoiced');
            } else {
                $invoiced = -1;
            }

            # Delivery type filter
            if (! empty($request->input('delivery_type'))) {
                $delivery_type = $request->input('delivery_type');
            } else {
                $delivery_type = '';
            }

            # Employee filter
            if (! empty($request->input('employee_id'))) {
                $employee_id = $request->input('employee_id');
            } else {
                $employee_id = 0;
            }

            # Orders
            $orders = DB::select(
                'CALL sales_tracking_report(?, ?, ?, ?, ?, ?, ?)',
                array($business_id, $start, $end, $customer_id, $invoiced, $delivery_type, $employee_id)
            );

            return Datatables::of($orders)
                ->editColumn('invoiced', '{{ __("messages.".$invoiced) }}')
                ->editColumn('quoted_amount', '<span class="display_currency" data-currency_symbol="true">$ {{ $quoted_amount ? @num_format($quoted_amount) : @num_format(0) }}</span>')
                ->editColumn('invoiced_amount', '<span class="display_currency" data-currency_symbol="true">$ {{ $invoiced_amount ? @num_format($invoiced_amount) : @num_format(0) }}</span>')
                ->editColumn('delivery_type', '{{ __("order.".$delivery_type) }}')
                ->editColumn('quote_date', '{{ @format_date($quote_date) }}')
                ->rawColumns(['invoiced', 'quoted_amount', 'invoiced_amount', 'delivery_type', 'quote_date'])
                ->toJson();
        }

        $delivery_types = $this->delivery_types;

        return view('report.sales_tracking_report')
            ->with(compact('delivery_types'));
    }

    /**
     * Generates sales tracking report in PDF or Excel.
     *
     * @return \Illuminate\Http\Response
     */
    public function postSalesTrackingReport(Request $request)
    {
        if (! auth()->user()->can('sales_tracking_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        # FILTERS
        $business_id = $request->session()->get('user.business_id');

        # Date filter
        if (! empty(request()->start_date) && ! empty(request()->end_date)) {
            $start = request()->start_date;
            $end =  request()->end_date;
        } else {
            $start = '';
            $end =  '';
        }

        # Customer filter
        if (! empty($request->input('customer_id'))) {
            $customer_id = $request->input('customer_id');
        } else {
            $customer_id = 0;
        }

        # Invoiced filter
        if (! is_null($request->input('invoiced'))) {
            $invoiced = $request->input('invoiced');
        } else {
            $invoiced = '-1';
        }

        # Delivery type filter
        if (! empty($request->input('delivery_type'))) {
            $delivery_type = $request->input('delivery_type');
        } else {
            $delivery_type = '';
        }

        # Employee filter
        if (! empty($request->input('employee_id'))) {
            $employee_id = $request->input('employee_id');
        } else {
            $employee_id = 0;
        }

        # Orders
        $orders = DB::select(
            'CALL sales_tracking_report(?, ?, ?, ?, ?, ?, ?)',
            array($business_id, $start, $end, $customer_id, $invoiced, $delivery_type, $employee_id)
        );

        # REPORT PARAMS
        $size = $request->input('size');

        $business = Business::where('id', $business_id)->first();

        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.sales_tracking_report_pdf',
				compact('orders', 'size', 'business'));

			return $pdf->stream(__('report.sales_tracking_report') . '.pdf');

		} else {
			return Excel::download(new SalesTrackingReportExport($orders, $size, $business), __('report.sales_tracking_report') . '.xlsx');
		}
    }

    /**
     * Retrieves employees list.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmployees()
    {
        if (request()->ajax()) {
            $term = request()->q;

            if (empty($term)) {
                return json_encode([]);
            }

            $business_id = request()->session()->get('user.business_id');

            $query = Employees::where('business_id', $business_id);
            
            $employees = $query->where(function ($query) use ($term) {
                    $query->where('first_name', 'like', '%' . $term .'%')
                        ->orWhere('last_name', 'like', '%' . $term .'%');
                })
                ->select('id', DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as text"))
                ->get();

            return json_encode($employees);
        }
    }

    /**
     * Show lost sales report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLostSalesReport(Request $request)
    {
        if (! auth()->user()->can('sales_tracking_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $reasons = Reason::where('business_id', $business_id)->pluck('reason', 'id');
        $customers_quote = Quote::where('business_id', $business_id)
            ->where('type', 'quote')
            // ->where('customer_name', 'BRYAN ALEXIS PORTALES MURILLO')
            ->pluck('customer_name', 'customer_id');
            
        if (request()->ajax()) {
            # Date filter
            if (!empty(request()->start_date_lost) && ! empty(request()->end_date_lost)) {
                $start = request()->start_date_lost;
                $end =  request()->end_date_lost;
            } else {
                $start = '';
                $end =  '';
            }
            # Employee filter
            if (!empty($request->input('employee_id'))) {
                $employee_id = request()->input('employee_id');
            } else {
                $employee_id = 0;
            }

            #reason filter
            if(!empty($request->input('reason_id'))){
                $reason_id = request()->reason_id;
            }else{
                $reason_id = 0;
            }
            #customer filter

            if(!empty($request->input('customer_id'))){
                $customer_id = request()->customer_id;
            }else{
                $customer_id = 0;
            }

            # Quotes
            if(empty($start) && empty($end)){
                $fecha = new DateTime();
                $start = $fecha->modify('first day of this month')->format('Y-m-d');
                $end = $fecha->modify('last day of this month')->format('Y-m-d');
            }

            $quotes = DB::select('CALL getLostSalesReport(?, ?, ?, ?, ?, ?)', array($business_id, $start, $end, $employee_id, $reason_id, $customer_id));

            // dd($quotes);

            return Datatables::of($quotes)
                ->editColumn('quote_date', function($row){return $this->transactionUtil->format_date($row->quote_date, false);})
                ->editColumn('due_date', function($row){return $this->transactionUtil->format_date($row->due_date, false);})
                ->editColumn('lost_date', function($row){return $this->transactionUtil->format_date($row->lost_date, false);})
                ->editColumn('total_final', function($row){return $this->transactionUtil->num_uf($row->total_final);})
                ->rawColumns(['lost_date', 'total_before_tax', 'total_final'])
                ->toJson();
        }

        return view('report.lost_sales_report', compact('reasons', 'customers_quote'));
    }

    public function postLostSalesReport(Request $request)
    {
        if (! auth()->user()->can('sales_tracking_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        # FILTERS
        $business_id = $request->session()->get('user.business_id');

        if (!empty(request()->start_date_lost) && ! empty(request()->end_date_lost)) {
            $start = request()->start_date_lost;
            $end =  request()->end_date_lost;
        } else {
            $start = '';
            $end =  '';
        }
        # Employee filter
        if (!empty($request->input('employee_id'))) {
            $employee_id = request()->input('employee_id');
        } else {
            $employee_id = 0;
        }

        #reason filter
        if(!empty($request->input('reason_id'))){
            $reason_id = request()->reason_id;
        }else{
            $reason_id = 0;
        }
        #customer filter
        if(!empty($request->input('customer_id'))){
            $customer_id = request()->customer_id;
        }else{
            $customer_id = 0;
        }

        # Quotes
        if(empty($start) && empty($end)){
            $fecha = new DateTime();
            $start = $fecha->modify('first day of this month')->format('Y-m-d');
            $end = $fecha->modify('last day of this month')->format('Y-m-d');
        }
        // dd($business_id, $start, $end, $employee_id, $reason_id);
        $quotes = DB::select('CALL getLostSalesReport(?, ?, ?, ?, ?, ?)', array($business_id, $start, $end, $employee_id, $reason_id, $customer_id));

        # REPORT PARAMS
        $size = $request->input('size');
        $business = Business::where('id', $business_id)->first();
        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.lost_sales_report_pdf',
				compact('quotes', 'size', 'business'));
            $pdf->setPaper('letter', 'landscape');
			return $pdf->stream(__('Ventas perdidas') . '.pdf');

		} else {
			return Excel::download(new LostSaleReportExport($quotes, $size, $business), __('Ventas perdidas') . '.xlsx');
		}
    }

    /**
     * Shows all sales report form.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllSalesReport(Request $request)
    {
        if (! auth()->user()->can('all_sales_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $params = [
                // Filters
                'location_id' => $request->input('location_id'),
                'document_type_id' => $request->input('document_type_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,
                'is_direct_sale' => request()->is_direct_sale,

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search'),
                'order' => request()->get('order')
            ];
    
            // Sales
            $sales = $this->getSalesForReport($params);

            return Datatables::of($sales['data'])
                ->editColumn(
                    'method', function ($row) {
                        if ($row->status != 'annulled') {
                            $method = '';

                            if ($row->payment_condition == 'cash') {
                                if ($row->count_payments > 1) {
                                    $method = __('lang_v1.checkout_multi_pay');
                                } else {
                                    $method = ! empty($row->method) ? __('lang_v1.' . $row->method) : '';
                                }
                            } else {
                                if (! empty($row->payment_condition)) {
                                    $method = ! empty($row->payment_condition) ? __('lang_v1.' . $row->payment_condition) : '';
                                } else {
                                    $method = ! empty($row->method) ? __('lang_v1.' . $row->method) : '';
                                }
                            }

                            return $method;

                        } else {
                            return '';
                        }
                    }
                )
                ->removeColumn('payment_condition')
                ->editColumn(
                    'final_total', function($row) {
                        $total = '<p class="text-right" style="margin-bottom: 0; color: #000;"><span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">"' . $row->final_total . '"</span>';

                        if ($row->amount_return > 0) {
                            $total .= '<br><strong>' . __('sale.return') . ':</strong><br>';
                            $total .= '<a href="' . action('SellReturnController@show', [$row->id]) . '" class="btn-modal">';
                            $total .= '<span class="display_currency" data-currency_symbol="true">' . $row->amount_return . '</span></a>';
                        }

                        $total .= '</p>';

                        return $total;
                    }
                )
                ->editColumn(
                    'discount_amount',
                    function($row) {
                        $discount_amount = 0.00;
                        if ($row->status != 'annulled') {
                            $discount_amount = $this->transactionUtil->getDiscountValue($row->total_before_tax, $row->discount_type, $row->discount_amount);
                        }
                        return '<span class="display_currency discount_amount" data-currency_symbol="true" data-orig-value="' . $discount_amount . '">' . $discount_amount . '</span>';
                    }
                )
                ->editColumn(
                    'total_before_tax',
                    '<span class="display_currency subtotal" data-currency_symbol="true" data-orig-value="{{$total_before_tax}}">{{$total_before_tax}}</span>'
                )
                ->editColumn(
                    'tax_amount', function ($row) {
                        if ($row->status != 'annulled') {
                            if ($row->tax_inc) {
                                $discount_amount = $this->transactionUtil->getDiscountValue($row->total_before_tax, $row->discount_type, $row->discount_amount);
                                $tax_amount = $this->taxUtil->getTaxAmount($row->id, 'sell', $discount_amount);
                                return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="' . $tax_amount . '">' . $tax_amount . '</span>';
                            } else {
                                return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="0.00">0.00</span>';
                            }
                            
                        } else {
                            return '<span class="display_currency tax-amount" data-currency_symbol="true" data-orig-value="0.00">0.00</span>';
                        }
                    }
                )
                ->editColumn('payment_status', '{{ __(\'lang_v1.\' . $payment_status) }}')
                ->editColumn(
                    'customer_name', function ($row) {
                        if ($row->status == 'annulled') {
                            return "<strong style='color: red;'>" . $row->customer_name . ' ' . __('lang_v1.annulled') . "</strong>";
                        } else {
                            return $row->customer_name;
                        }
                    }
                )
                ->addColumn(
                    'total_remaining', function ($row) {
                        $total_remaining =  $row->final_total - $row->total_paid;
                        if ($row->status == 'annulled') {
                            $total_remaining = 0.00;
                        }
                        $total_remaining_html = '<span class="display_currency total_remaining" data-currency_symbol="true" data-orig-value="' . $total_remaining . '">' . $total_remaining . '</span>';
                        return $total_remaining_html;
                    }
                )
                ->editColumn(
                    'total_paid', function ($row) {
                        $total_paid = $row->total_paid > 0 ? $row->total_paid : 0;
                        $total_paid_html = '<span class="display_currency total_paid" data-currency_symbol="true" data-orig-value="' . $total_paid . '">' . $total_paid . '</span>';
                        return $total_paid_html;
                    }
                )
                ->addColumn(
                    'final_total_bc', function($row) {
                        $final_total = $row->final_total > 0 ? $row->final_total : 0;
                        $final_total_html =  '<span class="display_currency final_total_bc" data-currency_symbol="true" data-orig-value="' . $final_total . '">' . $final_total . '</span>';
                        return $final_total_html;
                    }
                )
                ->removeColumn('id', 'tax_inc')
                ->rawColumns([
                    'customer_name',
                    'total_before_tax',
                    'tax_amount',
                    'total_remaining',
                    'final_total',
                    'total_paid',
                    'payment_status',
                    'final_total_bc',
                    'discount_amount'
                ])
                ->setTotalRecords($sales['count'])
                ->setFilteredRecords($sales['count'])
                ->skipPaging()
                ->toJson();
        }

        # Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);        

        $default_location = null;

        # Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        # Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }

        # Document types
        $document_types = DocumentType::forDropdown($business_id, false, false);
        $document_types = $document_types->prepend(__("kardex.all"), 'all');

        return view('report.all_sales_report')
            ->with(compact('locations', 'default_location', 'document_types'));
    }

    /**
     * Gets all sales report report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postAllSalesReport(Request $request)
    {
        if (! auth()->user()->can('all_sales_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            ini_set('memory_limit', '512M');
        
            $business_id = $request->session()->get('user.business_id');

            $params = [
                'location_id' => $request->input('location_id'),
                'document_type_id' => $request->input('document_type_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,
                'is_direct_sale' => request()->is_direct_sale
            ];

            // Sales
            $sales = $this->getSalesForReport($params, true);

            foreach ($sales as $item) {
                $item->discount = 0.00;
                $item->tax = 0.00;

                if ($item->status != 'annulled') {
                    $item->discount = $this->transactionUtil->getDiscountValue($item->total_before_tax, $item->discount_type, $item->discount_amount);

                    if ($item->tax_inc) {
                        $item->tax = $this->taxUtil->getTaxAmount($item->id, 'sell', $item->discount);
                    }
                }
            }

            // Table headers
            if (config('app.business') == 'optics') {
                $headers = [
                    __('messages.date'),
                    __('sale.document_no'),
                    __('document_type.title'),
                    __('contact.customer'),
                    __('sale.location'),
                    __('sale.payment_status'),
                    __('sale.total_invoice'),
                    __('lang_v1.payment_note'),
                    __('sale.total_paid'),
                    __('sale.total_balance_due')
                ];

            } else {
                $headers = [
                    __('messages.date'),
                    __('sale.document_no'),
                    __('document_type.title'),
                    __('customer.customer_code'),
                    __('contact.customer'),
                    __('sale.payment_status'),
                    __('lang_v1.payment_method'),
                    __('sale.subtotal'),
                    __('sale.discount'),
                    __('tax_rate.taxes'),
                    __('sale.total_amount')
                ];
            }

            $report_type = $request->input('report_type');

            $data = [];

            $business = Business::where('id', $business_id)->first();

            $header_data = [];

            $title = __('report.all_sales_report');

            if ($report_type == 'pdf') {
                $header_data['business_name'] = mb_strtoupper($business->business_full_name);
                $header_data['report_name'] = mb_strtoupper($title);
                
            } else {
                $data[] = [mb_strtoupper($business->business_full_name)];
                $data[] = [mb_strtoupper($title)];
                $data[] = [];
                $data[] = $headers;
            }

            foreach ($sales as $item) {
                $customer_name = $item->customer_name;
                $method = '';

                if ($item->status == 'annulled') {
                    $customer_name .= ' - ' . __('lang_v1.annulled');
                    $payment_status = '';
                    $total_remaining = 0;

                } else {
                    $payment_status = __('lang_v1.' . $item->payment_status);
                    $total_remaining = $item->final_total - $item->total_paid;

                    if ($item->payment_condition == 'cash') {
                        if ($item->count_payments > 1) {
                            $method = __('lang_v1.checkout_multi_pay');
                        } else {
                            $method = ! empty($item->method) ? __('lang_v1.' . $item->method) : '';
                        }
                    } else {
                        if (! empty($item->payment_condition)) {
                            $method = ! empty($item->payment_condition) ? __('lang_v1.' . $item->payment_condition) : '';
                        } else {
                            $method = ! empty($item->method) ? __('lang_v1.' . $item->method) : '';
                        }
                    }
                }

                if (config('app.business') == 'optics') {
                    $line = [
                        $this->transactionUtil->format_date($item->transaction_date),
                        $item->correlative,
                        $item->document_name,
                        $customer_name,
                        $item->location,
                        $payment_status,
                        $this->transactionUtil->num_f($item->final_total),
                        $item->note,
                        $this->transactionUtil->num_f($item->total_paid),
                        $this->transactionUtil->num_f($total_remaining),
                    ];

                } else {
                    $line = [
                        $this->transactionUtil->format_date($item->transaction_date),
                        $item->correlative,
                        $item->document_name,
                        $item->customer_id,
                        $customer_name,
                        $payment_status,
                        $method,
                        $this->transactionUtil->num_f($item->total_before_tax),
                        $this->transactionUtil->num_f($item->discount),
                        $this->transactionUtil->num_f($item->tax),
                        $this->transactionUtil->num_f($item->final_total)
                    ];
                }

                $data[] = $line;
            }

            $output = [
                'success' => true,
                'data' => $data,
                'type' => $report_type,
                'header_data' => $header_data,
                'headers' => [$headers]
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

    /**
     * Shows all sales report form.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllSalesWithUtilityReport(Request $request)
    {
        if (! auth()->user()->can('all_sales_with_utility_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $location_id = $request->input('location_id', 0);
            $document_type_id = $request->input('document_type_id', 0);
            $start_date = $request->input('start_date') == 'all' ? 0 : $request->input('start_date');
            $end_date = $request->input('end_date') == 'all' ? 0 : $request->input('end_date');

            # get transactions information
            $sales = collect(DB::select('CALL get_all_sales_with_utility(?, ?, ?, ?, ?)',
                [$business_id, $location_id, $document_type_id, $start_date, $end_date]));

            return Datatables::of($sales)
                ->editColumn('transaction_date', '{{ @format_date($transaction_date) }}')
                ->editColumn('payment_method', function($row){
                    if($row->status == 'final'){
                        return __("payment." . $row->payment_method);
                    } else {
                        return '-';
                    }
                })->editColumn(
                    'cost_total',
                    '<span class="display_currency cost-total" data-currency_symbol="true" data-orig-value="{{ $cost_total }}">{{ $cost_total }}</span>'
                )->editColumn(
                    'final_total',
                    '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="{{ $final_total }}">{{ $final_total }}</span>'
                )->editColumn(
                    'utility',
                    '<span class="display_currency utility" data-currency_symbol="true" data-orig-value="{{ $utility }}">{{ $utility }}</span>'
                )
                ->removeColumn('id')
                ->removeColumn('status')
                ->rawColumns(['transaction_date', 'payment_method', 'cost_total', 'final_total', 'utility', 'action'])
                ->toJson();
        }

        # Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);        

        $default_location = null;

        # Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        # Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }

        # Document types
        $document_types = DocumentType::forDropdown($business_id, false, false);
        $document_types = $document_types->prepend(__("kardex.all"), 'all');

        return view('report.all_sales_with_utility_report')
            ->with(compact('locations', 'default_location', 'document_types'));
    }

    /**
     * Gets all sales report report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postAllSalesWithUtilityReport(Request $request)
    {
        if (! auth()->user()->can('all_sales_with_utility_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $location_id = $request->input('location_id', 0);
        $document_type_id = $request->input('document_type_id', 0);
        $start_date = $request->input('start_date') == 'all' ? 0 : $request->input('start_date');
        $end_date = $request->input('end_date') == 'all' ? 0 : $request->input('end_date');

        # get transactions information
        $sales = collect(DB::select('CALL get_all_sales_with_utility(?, ?, ?, ?, ?)',
            [$business_id, $location_id, $document_type_id, $start_date, $end_date]));

        $business = Business::where('id', $business_id)
            ->first()->business_full_name;

		return Excel::download(new AllSalesWithUtilityReportExport($sales, $business, $this->transactionUtil), __('report.all_sales_with_utility_report') . '.xlsx');
    }

    /**
     * Get daily inventory report
     * @author Arquímides Martínez
     */
    public function getInputOutputReport(Request $request) {
        if (! auth()->user()->can('input_output_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->user()->business_id;
        if($request->ajax()){
			$start_date = $request->input('start_date');
			$end_date = $request->input('end_date');
			$location = $request->input('location') ? $request->input('location') : 0;
            $brand = $request->input('brand') ? $request->input('brand') : 0;
            $category = $request->input('category') ? $request->input('category') : 0;
            $transactions = $request->input('transactions') ? 1 : 0;
            $stock = $request->input('stock') ? 1 : 0;
            
			$inventory = collect(DB::select('CALL get_input_output(?, ?, ?, ?, ?, ?, ?)',
                [$start_date, $end_date,$location, $brand, $category, $transactions, $stock]));
			
			return Datatables::of($inventory)
				->editColumn('initial_inventory', '<span class="display_currency initial_inventory" data-precision="1" data-orig-value="{{ $initial_inventory }}">{{ $initial_inventory }}</span>')
				->editColumn('purchases', '<span class="display_currency purchases" data-precision="1" data-orig-value="{{ $purchases }}">{{ $purchases }}</span>')
                ->editColumn('purchase_transfers', '<span class="display_currency purchase_transfers" data-precision="1" data-orig-value="{{ $purchase_transfers }}">{{ $purchase_transfers }}</span>')
                ->editColumn('input_stock_adjustments', '<span class="display_currency input_stock_adjustments" data-precision="1" data-orig-value="{{ $input_stock_adjustments }}">{{ $input_stock_adjustments }}</span>')
                ->editColumn('sell_returns', '<span class="display_currency sell_returns" data-precision="1" data-orig-value="{{ $sell_returns }}">{{ $sell_returns }}</span>')
                ->editColumn('sales', '<span class="display_currency sales" data-precision="1" data-orig-value="{{ $sales }}">{{ $sales }}</span>')
                ->editColumn('sell_transfers', '<span class="display_currency sell_transfers" data-precision="1" data-orig-value="{{ $sell_transfers }}">{{ $sell_transfers }}</span>')
                ->editColumn('output_stock_adjustments', '<span class="display_currency output_stock_adjustments" data-precision="1" data-orig-value="{{ $output_stock_adjustments }}">{{ $output_stock_adjustments }}</span>')
                ->editColumn('purchase_returns', '<span class="display_currency purchase_returns" data-precision="1" data-orig-value="{{ $purchase_returns }}">{{ $purchase_returns }}</span>')
                ->editColumn('stock', '<span class="display_currency stock" data-precision="1" data-orig-value="{{ $stock }}">{{ $stock }}</span>')
				->removeColumn('category_id', 'category_name')
				->rawColumns(['initial_inventory', 'purchases', 'purchase_transfers', 'input_stock_adjustments', 'sell_returns', 'sales', 'sell_transfers', 'output_stock_adjustments', 'purchase_returns', 'stock'])
				->toJson();

		}

		$locations = BusinessLocation::forDropdown($business_id, true);
        $brands = Brands::brandsDropdown($business_id, false, false);
        $categories = Category::forDropdown($business_id, false, true);

        return view('report.input_output_report',
            compact('locations', 'categories', 'brands'));
    }

    /**
     * Post daily inventory report
     * @author Arquímides Martínez
     */
    public function postInputOutputReport(Request $request) {
        if (! auth()->user()->can('input_output_report.view')) {
            abort(403, 'Unauthorized action.');
        }
		$format = $request->input('report_format');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $location = $request->input('location') ? $request->input('location') : 0;
        $brand = $request->input('brand') ? $request->input('brand') : 0;
        $category = $request->input('category') ? $request->input('category') : 0;
        $transactions = $request->input('transactions') ? 1 : 0;
        $stock = $request->input('stock') ? 1 : 0;
        
        $inventory = collect(DB::select('CALL get_input_output(?, ?, ?, ?, ?, ?, ?)',
            [$start_date, $end_date, $location, $brand, $category, $transactions, $stock]));

        $categories = $inventory->where('category_id', ">", "0");
        $no_categories = $inventory->where('category_id', null);

        $business_id = $request->user()->business_id;
        $business_name = Business::find($business_id)->business_full_name;
        $start_date = $this->transactionUtil->format_date($start_date);
        $end_date = $this->transactionUtil->format_date($end_date);

        if ($format == 'pdf') {
            $pdf = \PDF::loadView('reports.input_output_report_pdf',
                compact('categories', 'no_categories', 'business_name', 'start_date', 'end_date'));

            $pdf->setPaper('A3', 'landscape');
            return $pdf->stream(__('report.input_output_report') . '.pdf');

        } else {
            return Excel::download(new InputOutput($categories, $no_categories, $business_name, $start_date, $end_date), __('report.input_output_report') . '.xlsx');
        }
    }

    /**
     * Get dispatched products report
     * @author Arquímides Martínez
     */
    public function getDispatchedProducts(Request $request) {
        if (! auth()->user()->can('dispatched_products_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->user()->business_id;
        $start_date = $request->input('start_date') ? $request->input('start_date') : date('Y-m-d');
        $end_date = $request->input('end_date') ? $request->input('end_date') : date('Y-m-d');
        $location = $request->input('location') ? $request->input('location') : 0;
        $seller = $request->input('seller') ? $request->input('seller') : 0;

        $products = Transaction::join('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
            ->join('variations as v', 'tsl.variation_id', 'v.id')
            ->join('products as p', 'v.product_id', 'p.id')
            ->join('quotes as q', 'transactions.id', 'q.transaction_id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereRaw('DATE(transactions.transaction_date) BETWEEN ? AND ?', [$start_date, $end_date])
            ->whereRaw('(transactions.location_id = ? OR ? = 0)', [$location, $location])
            ->whereRaw('(q.employee_id = ? OR ? = 0)', [$seller, $seller])
            ->select(
                'v.id as variation_id',
                DB::raw('IF(v.name != "DUMMY", CONCAT(p.name, " ", v.name), p.name) as product_name'),
                DB::raw('IF(v.name != "DUMMY", v.sub_sku, p.sku) as sku')
            )->groupBy('v.id')
            ->orderBy('v.id')
            ->get();

        if($request->ajax()) {
            $dispatched_products = collect(DB::select('CALL get_dispatched_products(?, ?, ?, ?)',
                [$start_date, $end_date, $location, $seller]));
            
            return DataTables::of($dispatched_products)
                ->addColumn('qty_total', function($row) use($products, $dispatched_products) {
                    $qty_total = 0;
                    foreach ($products as $p) {
                        $qty_total += $dispatched_products->where('customer_id', $row->customer_id)
                            ->where('transaction_id', $row->transaction_id)
                            ->sum('product_'. $p->variation_id);
                    }

                    return '<span class="display_currency qty_total" data-precision="1" data-orig-value="'. $qty_total .'">'. $qty_total .'</span>';
                })
                ->editColumn('weight_total',
                    '<span class="display_currency weight_total" data-precision="1" data-orig-value="{{ $weight_total }}">{{ $weight_total }}</span>')
                ->editColumn('final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{ $final_total }}">{{ $final_total }}</span>')
                ->rawColumns(['qty_total', 'weight_total', 'final_total'])
                ->toJson();
        }
        
        $product_counts = $products->count();
		$locations = BusinessLocation::forDropdown($business_id, true);
        $sellers = Employees::SellersDropdown($business_id, false);

        return view('report.dispatched_products',
            compact('locations', 'sellers', 'product_counts'));
    }

    /**
     * Post dispatched products report
     * @author Arquímides Martínez
     */
    public function postDispatchedProducts(Request $request) {
        if (! auth()->user()->can('dispatched_products_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->user()->business_id;
		$format = $request->input('report_format');

        $start_date = $request->input('start_date') ? $request->input('start_date') : date('Y-m-d');
        $end_date = $request->input('end_date') ? $request->input('end_date') : date('Y-m-d');
        $location = $request->input('location') ? $request->input('location') : 0;
        $seller = $request->input('seller') ? $request->input('seller') : 0;

        $products = Transaction::join('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
            ->join('variations as v', 'tsl.variation_id', 'v.id')
            ->join('products as p', 'v.product_id', 'p.id')
            ->join('quotes as q', 'transactions.id', 'q.transaction_id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereRaw('DATE(transactions.transaction_date) BETWEEN ? AND ?', [$start_date, $end_date])
            ->whereRaw('(transactions.location_id = ? OR ? = 0)', [$location, $location])
            ->whereRaw('(q.employee_id = ? OR ? = 0)', [$seller, $seller])
            ->select(
                'v.id as variation_id',
                DB::raw('IF(v.name != "DUMMY", CONCAT(p.name, " ", v.name), p.name) as product_name'),
                DB::raw('IF(v.name != "DUMMY", v.sub_sku, p.sku) as sku')
            )->groupBy('v.id')
            ->orderBy('v.id')
            ->get();
        
        $dispatched_products = collect(DB::select('CALL get_dispatched_products(?, ?, ?, ?)',
            [$start_date, $end_date, $location, $seller]));

        $business_name = Business::find($business_id)->business_full_name;
        $start_date = $this->transactionUtil->format_date($start_date);
        $end_date = $this->transactionUtil->format_date($end_date);
        
        if ($format == 'pdf') {
            $pdf = \PDF::loadView('reports.dispatched_products_report_pdf',
                compact('products', 'dispatched_products', 'business_name', 'start_date', 'end_date'));

            $pdf->setPaper('A3', 'landscape');
            return $pdf->stream(__('report.dispatched_products_report') . '.pdf');

        } else {
            return Excel::download(new DispatchedProducts($products, $dispatched_products, $business_name, $start_date, $end_date),
                __('report.dispatched_products_report') . '.xlsx');
        }
    }

    public function getDispatchedProductsCount(Request $request) {
        $start_date = $request->input('start_date') ? $request->input('start_date') : date('Y-m-d');
        $end_date = $request->input('end_date') ? $request->input('end_date') : date('Y-m-d');
        $location = $request->input('location') ? $request->input('location') : 0;
        $seller = $request->input('seller') ? $request->input('seller') : 0;

        $products = Transaction::join('transaction_sell_lines as tsl', 'transactions.id', 'tsl.transaction_id')
            ->join('variations as v', 'tsl.variation_id', 'v.id')
            ->join('products as p', 'v.product_id', 'p.id')
            ->join('quotes as q', 'transactions.id', 'q.transaction_id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereRaw('DATE(transactions.transaction_date) BETWEEN ? AND ?', [$start_date, $end_date])
            ->whereRaw('(transactions.location_id = ? OR ? = 0)', [$location, $location])
            ->whereRaw('(q.employee_id = ? OR ? = 0)', [$seller, $seller])
            ->select('v.id as variation_id')
            ->groupBy('v.id')
            ->orderBy('v.id')
            ->get();

        $count = $products->count();

        return json_encode($count);
    }

    /**
     * Get Connect report for Disproci
     * @author Arquímides Martínez
     */
    public function getConnectReport(Request $request) {
        if (! auth()->user()->can('connect_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->user()->business_id;

        if(request()->ajax()) {
            $start_date = $request->input('start_date') ? $request->input('start_date') : date('Y-m-d');
            $end_date = $request->input('end_date') ? $request->input('end_date') : date('Y-m-d');
            $location = $request->input('location') ? $request->input('location') : 0;
            $seller = $request->input('seller') ? $request->input('seller') : 0;

            $connect_report = collect(DB::select('CALL get_connect_report(?, ?, ?, ?)',
            [$start_date, $end_date, $location, $seller]));
        
            return DataTables::of($connect_report)
                ->editColumn('latitude',
                    '<span class="display_currency latitude" data-precision="8" data-orig-value="{{ $latitude }}">{{ $latitude }}</span>')
                ->editColumn('length',
                    '<span class="display_currency length" data-precision="8" data-orig-value="{{ $length }}">{{ $length }}</span>')
                ->editColumn('from', function($row) {
                    return date('H:i:s', strtotime($row->from));
                })
                ->editColumn('to', function($row) {
                    return date('H:i:s', strtotime($row->to));
                })
                ->editColumn('cost',
                    '<span class="display_currency cost" data-precision="2" data-currency_symbol="true" data-orig-value="{{ $cost }}">{{ $cost }}</span>')
                ->editColumn('weight',
                    '<span class="display_currency weight" data-precision="2" data-orig-value="{{ $weight }}">{{ $weight }}</span>')
                ->editColumn('volume',
                    '<span class="display_currency volume" data-precision="6" data-orig-value="{{ $volume }}">{{ $volume }}</span>')
                ->editColumn('download_time', function($row) {
                    return date('H:i:s', strtotime($row->download_time));
                })
                ->rawColumns(['latitude', 'length', 'cost', 'weight', 'volume'])
                ->toJson();
        }

		$locations = BusinessLocation::forDropdown($business_id, true);
        $sellers = Employees::SellersDropdown($business_id, false);

        return view('report.connect_report',
            compact('locations', 'sellers'));
    }

    /**
     * Post connect report for Disproci
     * @author Arquímides Martínez
     */
    public function postConnectReport(Request $request) {
        if (! auth()->user()->can('connect_report.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->user()->business_id;

        $start_date = $request->input('start_date') ? $request->input('start_date') : date('Y-m-d');
        $end_date = $request->input('end_date') ? $request->input('end_date') : date('Y-m-d');
        $location = $request->input('location') ? $request->input('location') : 0;
        $seller = $request->input('seller') ? $request->input('seller') : 0;

        $connect_report = collect(DB::select('CALL get_connect_report(?, ?, ?, ?)',
            [$start_date, $end_date, $location, $seller]));

        $business_name = Business::find($business_id)->business_full_name;
        $start_date = $this->transactionUtil->format_date($start_date);
        $end_date = $this->transactionUtil->format_date($end_date);
        
        return Excel::download(new ConnectReport($connect_report, $business_name, $start_date, $end_date),
            __('report.connect_report') . '.xlsx');
    }

    /**
     * Get price lists report
     * 
     * @return Illuminate\Http\Response
     */
    public function getPriceListsReport() {
        if (!auth()->user()->can('price_lists_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = auth()->user()->business_id;

        $locations = BusinessLocation::forDropdown($business_id);
        $categories = Category::where('parent_id', 0)
            ->where('business_id', $business_id)
            ->pluck('name', 'id');
        $brands = Brands::brandsDropdown($business_id);

        return view('report.price_lists_report', compact('locations', 'categories', 'brands'));
    }

    /**
     * Post price lists report
     * 
     * @return Illuminate\Http\Request
     * @return Excel
     */
    public function postPriceListsReport(Request $request) {
        if (!auth()->user()->can('price_lists_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = \Validator::make($request->all(), [ 'location' => 'required' ]);

        if ($validator->fails()) {
            return $validator->getMessageBag();
        }

        $business_id = auth()->user()->business_id;
        $location_id = $request->get('location');
        $category_id = $request->get('category') ?? 0;
        $brand_id = $request->get('brand') ?? 0;

        $price_lists = collect(DB::select('CALL product_price_lists(?, ?, ?, ?)',
            [$business_id, $location_id, $category_id, $brand_id]));

        $business_name = Business::find($business_id)->business_full_name;

        return Excel::download(new PriceListsReport($business_name, $price_lists),
            __('report.price_lists_report') . '.xlsx');
    }

    /**
     * Get list price report
     * @author Arquímides Martínez
     */
    public function getListPriceReport(Request $request) {
        if (!auth()->user()->can('list_price_report_pdf.view')
            || !auth()->user()->can('list_price_report_excel.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->user()->business_id;

        if($request->ajax()) {
            $brand = $request->input('brand') ? $request->input('brand') : 0;
            $category = $request->input('category') ? $request->input('category') : 0;
    
            $products = Product::join('variations as v', 'products.id', 'v.product_id')
                ->leftJoin('brands as b', 'products.brand_id', 'b.id')
                ->leftJoin('categories as c', 'products.category_id', 'c.id')
                ->select(
                    'products.id as product_id',
                    'v.id as variation_id',
                    'b.id as brand_id',
                    'c.id as category_id',
                    'v.sub_sku as sku',
                    DB::raw('IF(v.name = "DUMMY", products.name, CONCAT(products.name, " ", v.name)) as product_name'),
                    'b.name as brand_name',
                    'c.name as category_name',
                    'v.sell_price_inc_tax as default_price'
                );

            /** Filter by brand */
            if(!empty($brand)) {
                $products->where('brand_id', $brand);
            }
    
            /** Filter by category */
            if(!empty($category)) {
                $products->where('category_id', $category);
            }

            $count = $products;

            return DataTables::of($products)
                ->editColumn('default_price',
                    '<span class="display_currency" data-precision="2" data-currency_symbol="true" data-orig-value="{{ $default_price }}">{{ $default_price }}</span>'
                )
                ->filterColumn('product_name', function($query, $keyword) {
                    $query->whereRaw('CONCAT(p.name, " ", v.name) LIKE ?', [$keyword]);

                })->rawColumns(['default_price'])
                ->toJson();
        }

        $brands = Brands::brandsDropdown($business_id, false, false);
        $categories = Category::forDropdown($business_id, false, true);
        $count = SellingPriceGroup::
            join('variation_group_prices as vgp', 'selling_price_groups.id', 'vgp.price_group_id')
            ->groupBy('selling_price_groups.id')->get();

        $count = count($count);
        return view('report.list_price_report',
            compact('brands', 'categories', 'count'));
    }

    /**
     * List price report
     * @author Arquímides Martínez
     */
    public function postListPriceReport(Request $request) {
        if (!auth()->user()->can('list_price_report_pdf.view')
            || !auth()->user()->can('list_price_report_excel.view')) {
            abort(403, 'Unauthorized action.');
        }

        $brand = $request->input('brand') ? $request->input('brand') : 0;
        $category = $request->input('category') ? $request->input('category') : 0;
		$format = $request->input('report_format');

        $products = Product::join('variations as v', 'products.id', 'v.product_id')
            ->leftJoin('brands as b', 'products.brand_id', 'b.id')
            ->leftJoin('categories as c', 'products.category_id', 'c.id')
            ->leftJoin('variation_group_prices as vgp', 'v.id', 'vgp.variation_id')
            ->select(
                'products.id as product_id',
                'v.id as variation_id',
                'b.id as brand_id',
                'c.id as category_id',
                'vgp.price_group_id',
                'v.sub_sku as sku',
                DB::raw('IF(v.name = "DUMMY", products.name, CONCAT(products.name, " ", v.name)) as product_name'),
                'b.name as brand_name',
                'c.name as category_name',
                'v.sell_price_inc_tax as default_price'
            );
        
        /** Filter by brand */
        if(!empty($brand)) {
            $products->where('brand_id', $brand);
        }

        /** Filter by category */
        if(!empty($category)) {
            $products->where('category_id', $category);
        }

        $price_groups = SellingPriceGroup::
            join('variation_group_prices as vgp', 'selling_price_groups.id', 'vgp.price_group_id');

        $list_prices = 
            $price_groups->select('selling_price_groups.name')
                ->groupBy('selling_price_groups.id')
                ->get()->toArray();
        $price_groups =
            $price_groups->select(
                'selling_price_groups.id as selling_price_id',
                'selling_price_groups.name as selling_price_name')->get();

        foreach ($price_groups as $pg) {
            $products->addSelect(
                DB::raw('SUM(IF(vgp.price_group_id = '.$pg->selling_price_id.', vgp.price_inc_tax, 0)) as `'.$pg->selling_price_name.'`'));
        }

        $business_id = $request->user()->business_id;
        $products = $products->groupBy('products.id')->get()->toArray();
        $business_name = Business::find($business_id)->business_full_name;
        $paper = 'letter';

        if ($format == 'pdf') {
            $pdf = \PDF::loadView('reports.list_price_report_pdf',
                compact('products', 'business_name', 'list_prices'));

            if(count($list_prices) > 3) {
                $paper = 'A3';
            }

            $pdf->setPaper($paper, 'landscape');
            return $pdf->stream(__('report.list_price_report') . '.pdf');

        } else {
            return Excel::download(new ListPriceReport($products, $business_name, $list_prices),
                __('report.list_price_report') . '.xlsx');
        }
    }

    /**
     * Gets sales for report.
     * 
     * @param  array  $params
     * @param  bool  $print
     * @return array
     */
    public function getSalesForReport($params, $print = false)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        // Location filter
        if (! empty($params['location_id']) && $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Document type filter
        if (! empty($params['document_type_id']) && $params['document_type_id'] != 'all') {
            $document_type_id = $params['document_type_id'];
        } else {
            $document_type_id = 0;
        }

        // Created by filter
        if (! empty($params['created_by'])) {
            $created_by = $params['created_by'];
        } else {
            $created_by = 0;
        }

        // Customer filter
        if (! empty($params['customer_id'])) {
            $customer_id = $params['customer_id'];
        } else {
            $customer_id = 0;
        }

        // Customer filter
        if (isset($params['seller_id']) && !empty($params['seller_id'])) {
            $seller_id = $params['seller_id'];
        } else {
            $seller_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Direct sale filter
        if (! empty($params['is_direct_sale'])) {
            $is_direct_sale = $params['is_direct_sale'];
        } else {
            $is_direct_sale = -1;
        }

        // Commission agent filter
        if (! empty($params['commission_agent'])) {
            $commission_agent = $params['commission_agent'];
        } else {
            $commission_agent = 0;
        }

        // Payment status filter
        if (! empty($params['payment_status'])) {
            $payment_status = $params['payment_status'];
        } else {
            $payment_status = '';
        }

        if ($print) {
            // Sales
            $parameters = [
                $business_id,
                $location_id,
                $document_type_id,
                $created_by,
                $customer_id,
                $start,
                $end,
                $is_direct_sale,
                $commission_agent,
                $payment_status
            ];

            if (config('app.business') == 'optics') {
                $sales = DB::select(
                    'CALL get_all_sales_report_optics(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    $parameters
                );
    
            } else {
                $sales = DB::select(
                    'CALL get_all_sales_report(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    $parameters
                );
            }

            $result = $sales;

        } else {
            // Datatable parameters
            $start_record = $params['start_record'];
            $page_size = $params['page_size'];
            $search_array = $params['search'];
            $search = ! is_null($search_array['value']) ? $search_array['value'] : '';
            $order = $params['order'];
    
            // Count sales
            $count = DB::select(
                'CALL count_all_sales(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $location_id,
                    $document_type_id,
                    $created_by,
                    $customer_id,
                    $seller_id,
                    $start,
                    $end,
                    $is_direct_sale,
                    $commission_agent,
                    $payment_status,
                    $search
                )
            );
    
            if (config('app.business') == 'optics') {
                // Sales
                $parameters = [
                    $business_id,
                    $location_id,
                    $document_type_id,
                    $created_by,
                    $customer_id,
                    $start,
                    $end,
                    $is_direct_sale,
                    $commission_agent,
                    $payment_status,
                    $search,
                    $start_record,
                    $page_size,
                    $order[0]['column'],
                    $order[0]['dir']
                ];

                $sales = DB::select(
                    'CALL get_all_sales_optics(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    $parameters
                );
    
            } else {
                // Sales
                $parameters = [
                    $business_id,
                    $location_id,
                    $document_type_id,
                    $seller_id,
                    $created_by,
                    $customer_id,
                    $start,
                    $end,
                    $is_direct_sale,
                    $commission_agent,
                    $payment_status,
                    $search,
                    $start_record,
                    $page_size,
                    $order[0]['column'],
                    $order[0]['dir']
                ];
                $sales = DB::select(
                    'CALL get_all_sales(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    $parameters
                );
            }
    
            $result = [
                'data' => $sales,
                'count' => $count[0]->count
            ];
        }

        return $result;
    }

    /**
     * Show detailed sales report form.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDetailedCommissionsReport(Request $request)
    {
        if (! auth()->user()->can('detailed_commissions_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $commission_agent = $request->input('commission_agent');

            /*if (! empty($commission_agent) && $commission_agent != 'all') {
                $user_id = Employees::find($commission_agent)->user_id;
                $commission_agent = User::find($user_id)->id;
            }*/

            // Datatable parameters
            $start_record = $request->get('start');
            $page_size = $request->get('length');
            $search = $request->get('search');

            // Parameters
            $params = [
                'location_id' => $request->input('location_id'),
                'commission_agent' => $commission_agent,
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,
                'start_record' => $start_record,
                'page_size' => $page_size,
                'search' => $search['value']
            ];
    
            // Commissions
            $commissions = $this->getDataToDetailedCommissionsReport($params);

            return Datatables::of($commissions['data'])
                ->editColumn('quantity', '{{ @num_format($quantity) }}')
                ->editColumn('price_inc', '$ {{ @num_format($price_inc) }}')
                ->editColumn('price_exc', '$ {{ @num_format($price_exc) }}')
                ->editColumn('unit_cost', '$ {{ @num_format($unit_cost) }}')
                ->editColumn('total_cost', '$ {{ @num_format($total_cost) }}')
                ->editColumn('unit_price', '$ {{ @num_format($unit_price) }}')
                ->editColumn('payment_balance', '$ {{ @num_format($payment_balance) }}')
                ->rawColumns([
                    'quantity',
                    'price_inc',
                    'price_exc',
                    'unit_cost',
                    'total_cost',
                    'unit_price',
                    'payment_balance'
                ])
                ->setTotalRecords($commissions['count'])
                ->setFilteredRecords($commissions['count'])
                ->skipPaging()
                ->toJson();
        }

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__('kardex.all_2'), 'all');
        }

        // Commission agents
        $commission_agents = Employees::SellersDropdown($business_id, false);
        $commission_agents = $commission_agents->prepend(__('kardex.all'), 'all');

        return view('report.detailed_commissions_report')
            ->with(compact('locations', 'default_location', 'commission_agents'));
    }

    /**
     * Get detailed sales report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postDetailedCommissionsReport(Request $request)
    {
        if (! auth()->user()->can('detailed_commissions_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            ini_set('memory_limit', '512M');

            $business_id = $request->session()->get('user.business_id');

            $commission_agent = $request->input('commission_agent');

            /*if (! empty($commission_agent) && $commission_agent != 'all') {
                $user_id = Employees::find($commission_agent)->user_id;
                $commission_agent = User::find($user_id)->id;
            }*/

            $params = [
                'location_id' => $request->input('location_id'),
                'commission_agent' => $commission_agent,
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,
                'start_record' => 0,
                'page_size' => -1,
                'search' => null
            ];

            // Commissions
            $commissions = $this->getDataToDetailedCommissionsReport($params);

            // Table headers
            if (config('app.business') == 'optics') {
                $headers = [
                    __('accounting.date'),
                    __('inflow_outflow.document_no'),
                    __('document_type.title'),
                    __('lang_v1.payment_condition'),
                    __('contact.customer'),
                    __('accounting.location'),
                    __('category.category'),
                    __('product.sub_category'),
                    __('product.brand'),
                    'SKU',
                    __('business.product'),
                    __('lang_v1.quantity'),
                    __('purchase.unit_price'),
                    __('quote.seller'),
                    __('graduation_card.optometrist'),
                    __('report.unit_cost'),
                    __('report.total_cost')
                ];

            } else {
                $headers = [
                    __('accounting.date'),
                    __('inflow_outflow.document_no'),
                    __('document_type.title'),
                    __('lang_v1.payment_condition'),
                    __('customer.customer_code'),
                    __('contact.customer'),
                    __('accounting.location'),
                    __('category.category'),
                    __('product.sub_category'),
                    __('product.brand'),
                    'SKU',
                    __('business.product'),
                    __('lang_v1.quantity'),
                    __('report.price_inc_tax'),
                    __('report.price_exc_tax'),
                    __('sale.payments'),
                    __('sale.payment_status'),
                    __('quote.seller'),
                    __('report.unit_cost'),
                    __('report.total_cost'),
                    __('customer.customer_portfolio'),
                    __('geography.state'),
                    __('geography.city')
                ];
            }

            $report_type = $request->input('report_type');

            $data = [];

            $business = Business::where('id', $business_id)->first();

            $header_data = [];

            $title = config('app.business') == 'optics' ? __('report.optics_detailed_commissions_report') : __('report.detailed_commissions_report');

            if ($report_type == 'pdf') {
                $header_data['business_name'] = mb_strtoupper($business->business_full_name);
                $header_data['report_name'] = mb_strtoupper($title);
                
            } else {
                $data[] = [mb_strtoupper($business->business_full_name)];
                $data[] = [mb_strtoupper($title)];
                $data[] = [];
                $data[] = $headers;
            }

            foreach ($commissions['data'] as $item) {
                if (config('app.business') == 'optics') {
                    $line = [
                        $this->transactionUtil->format_date($item->transaction_date),
                        $item->doc_no,
                        $item->doc_type,
                        $item->payment_condition,
                        $item->customer_name,
                        $item->location,
                        $item->category,
                        $item->sub_category,
                        $item->brand_name,
                        $item->sku,
                        $item->product_name,
                        $this->transactionUtil->num_f($item->quantity),
                        $this->transactionUtil->num_f($item->price_inc),
                        $item->seller_name,
                        ! isset($item->optometrist) ? $item->optometrist : null,
                        $this->transactionUtil->num_f($item->unit_cost),
                        $this->transactionUtil->num_f($item->total_cost)
                    ];

                } else {
                    $line = [
                        $this->transactionUtil->format_date($item->transaction_date),
                        $item->doc_no,
                        $item->doc_type,
                        $item->payment_condition,
                        $item->customer_id,
                        $item->customer_name,
                        $item->location,
                        $item->category,
                        $item->sub_category,
                        $item->brand_name,
                        $item->sku,
                        $item->product_name,
                        $this->transactionUtil->num_f($item->quantity),
                        $this->transactionUtil->num_f($item->price_inc),
                        $this->transactionUtil->num_f($item->price_exc),
                        $this->transactionUtil->num_f($item->payment_balance),
                        $item->payment_status,
                        $item->seller_name,
                        $this->transactionUtil->num_f($item->unit_cost),
                        $this->transactionUtil->num_f($item->total_cost),
                        $item->portfolio_name,
                        $item->state,
                        $item->city
                    ];
                }

                $data[] = $line;
            }

            $output = [
                'success' => true,
                'data' => $data,
                'type' => $report_type,
                'header_data' => $header_data,
                'headers' => [$headers]
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

    /**
     * Get data for detailed sales report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToDetailedCommissionsReport($params)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        // Location filter
        if (! empty($params['location_id']) && $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Commission agent filter
        $commission_agent = ! empty($params['commission_agent']) && $params['commission_agent'] != 'all' ? $params['commission_agent'] : 0;

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Search text filter
        $search = ! is_null($params['search']) ? $params['search'] : '';

        // Commissions count
        $count = DB::select(
            'CALL count_detailed_commissions_report(?, ?, ?, ?, ?, ?)',
            array(
                $business_id,
                $location_id,
                $commission_agent,
                $start,
                $end,
                $search
            )
        );

        $page_size = $params['page_size'];

        // Commissions
        $commissions = DB::select(
            'CALL detailed_commissions_report(?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $business_id,
                $location_id,
                $commission_agent,
                $start,
                $end,
                $search,
                $params['start_record'],
                $page_size
            )
        );

        $result = [
            'data' => $commissions,
            'count' => $count[0]->count
        ];

        return $result;
    }

    /**
     * Generate customer account statement in PDF or Excel.
     * 
     * @return \Illuminate\Http\Response
     */
    public function postAccountStatement()
    {
        if (! auth()->user()->can('account_statement.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $customer_id = request()->input('customer_id');
        $payment_status = request()->input('payment_status', 0);
        
        // Params
        $params = [
            'business_id' => $business_id,
            'customer_id' => $customer_id,
            'payment_status' => $payment_status,
            'start_date' => request()->start_date,
            'end_date' => request()->end_date
        ];

        // Lines
        $lines = $this->getLinesForAccountStatement($params);

        $date = \Carbon::now();
        $size = request()->input('size');
        $report_type = request()->input('report_type');

        $business = Business::find($business_id);
        
        $location = BusinessLocation::first();
        $business->landmark = $location->landmark;
        $business->city = $location->city;
        $business->state = $location->state;
        $business->mobile = $location->mobile;
        
        $customer = Customer::find($customer_id);

        // Generates report
        if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.account_statement_pdf',
				compact('lines', 'size', 'date', 'business', 'customer'));

			return $pdf->stream(__('report.account_statement') . ' - ' . $customer->name . '.pdf');

		} else {
			return Excel::download(
                new AccountStatementExport($this->transactionUtil, $lines, $date, $business, $customer),
                __('report.account_statement') . ' - ' . $customer->name .  '.xlsx'
            );
		}
    }

    /**
     * Get lines for customer account statement.
     * 
     * @param  array  $params
     * @return array
     */
    public function getLinesForAccountStatement($params)
    {
        // Customer filter
        $customer_id = ! empty($params['customer_id']) ? $params['customer_id'] : 0;

        // Sales
        $sales = Transaction::join('document_types', 'transactions.document_types_id', 'document_types.id')
            ->leftJoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where('transactions.customer_id', $customer_id)
            ->where('transactions.business_id', $params['business_id'])
            ->select(
                DB::raw("CONCAT(document_types.short_name, transactions.correlative) as correlative"),
                'transactions.transaction_date',
                'transactions.final_total',
                DB::raw("IF(customers.is_default = 1, transactions.customer_name, customers.name) as customer_name"),
                'transactions.pay_term_number',
                'transactions.payment_balance'
            );

        // Sales returns
        $sales_returns = Transaction::join('document_types', 'transactions.document_types_id', 'document_types.id')
            ->join('transactions as parent_transactions', 'transactions.return_parent_id', 'parent_transactions.id')
            ->leftJoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell_return')
            ->where('transactions.status', 'final')
            ->where('transactions.customer_id', $customer_id)
            ->where('transactions.business_id', $params['business_id'])
            ->select(
                DB::raw("CONCAT(document_types.short_name, transactions.correlative) as correlative"),
                'transactions.transaction_date',
                'transactions.final_total',
                'parent_transactions.transaction_date',
                DB::raw("IF(customers.is_default = 1, transactions.customer_name, customers.name) as customer_name")
            );

        // Payments
        $payments = TransactionPayment::join('transactions', 'transaction_payments.transaction_id', 'transactions.id')
            ->leftJoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.customer_id', $customer_id)
            ->where('transaction_payments.business_id', $params['business_id'])
            ->select(
                'transaction_payments.payment_ref_no',
                'transaction_payments.transfer_ref_no',
                'transaction_payments.paid_on',
                'transaction_payments.amount',
                'transactions.transaction_date',
                'transactions.pay_term_number',
                DB::raw("IF(customers.is_default = 1, transactions.customer_name, customers.name) as customer_name")
            );

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $sales->whereDate('transactions.transaction_date', '>=', $params['start_date'])
                ->whereDate('transactions.transaction_date', '<=', $params['end_date']);

            $sales_returns->whereDate('parent_transactions.transaction_date', '>=', $params['start_date'])
                ->whereDate('parent_transactions.transaction_date', '<=', $params['end_date']);

            $payments->whereDate('transactions.transaction_date', '>=', $params['start_date'])
                ->whereDate('transactions.transaction_date', '<=', $params['end_date']);
        }

        // Payment status filter
        if ($params['payment_status'] == 1) {
            $sales->whereIn('transactions.payment_status', ['due', 'partial']);

            $sales_returns->whereIn('parent_transactions.payment_status', ['due', 'partial']);

            $payments->whereIn('transactions.payment_status', ['due', 'partial']);
        }

        $sales = $sales->orderBy('transactions.transaction_date')->get();

        $sales_returns = $sales_returns->orderBy('transactions.transaction_date')->get();

        $payments = $payments->orderBy('transaction_payments.paid_on')->get();

        $result = collect();

        foreach ($sales as $sale) {
            $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $sale->transaction_date);
            $expiration_date = $transaction_date->addDays($sale->pay_term_number);

            if ($expiration_date->lt(\Carbon::now())) {
                $delay_date = $expiration_date->diffInDays(\Carbon::now());
            } else {
                $delay_date = 0;
            }

            $item = [
                'date' => $sale->transaction_date,
                'no_doc' => $sale->correlative,
                'currency' => 'usd',
                'customer' => $sale->customer_name,
                'amount' => $sale->final_total,
                'expiration' => $expiration_date->format('Y-m-d H:i:s'),
                'payment' => $sale->payment_balance,
                'balance' => $sale->final_total - $sale->payment_balance,
                'delay' => $delay_date
            ];

            $result->push($item);
        }

        foreach ($sales_returns as $sale_return) {
            $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $sale_return->transaction_date);
            $expiration_date = $transaction_date->addDays($sale_return->pay_term_number);

            if ($expiration_date->lt(\Carbon::now())) {
                $delay_date = $expiration_date->diffInDays(\Carbon::now());
            } else {
                $delay_date = 0;
            }

            $item = [
                'date' => $sale_return->transaction_date,
                'no_doc' => $sale_return->correlative,
                'currency' => 'usd',
                'customer' => $sale_return->customer_name,
                'amount' => $sale_return->final_total * -1,
                'expiration' => $expiration_date->format('Y-m-d H:i:s'),
                'payment' => 0,
                'balance' => $sale_return->final_total * -1,
                'delay' => $delay_date
            ];

            $result->push($item);
        }

        foreach ($payments as $payment) {
            $transaction_date = \Carbon::createFromFormat('Y-m-d H:i:s', $payment->paid_on);
            $expiration_date = $transaction_date->addDays($payment->pay_term_number);

            if ($expiration_date->lt(\Carbon::now())) {
                $delay_date = $expiration_date->diffInDays(\Carbon::now());
            } else {
                $delay_date = 0;
            }

            $item = [
                'date' => $payment->paid_on,
                'no_doc' => $payment->payment_ref_no ?? $payment->transfer_ref_no,
                'currency' => 'usd',
                'customer' => $payment->customer_name,
                'amount' => $payment->amount * -1,
                'expiration' => $expiration_date->format('Y-m-d H:i:s'),
                'payment' => 0,
                'balance' => $payment->amount * -1,
                'delay' => $delay_date
            ];

            $result->push($item);
        }

        $result = $result->sortBy('date');

        return $result;
    }

    /**
     * Get collections
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response | Json
     */
    public function getCollections() {
        if (!auth()->user()->can('cxc.collections')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = auth()->user()->business_id;

        if (request()->ajax()) {
            $location_id = request()->input('location') ? request()->input('location') : 0;
            $seller_id = request()->input('seller') ? request()->input('seller') : 0;
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');

            $collections = collect(DB::select('CALL get_collection_transactions(?, ?, ?, ?, ?)',
                [$business_id, $location_id, $seller_id, $start_date, $end_date]));

            return Datatables::of($collections)
                ->editColumn('transaction_date', '{{ @format_date($transaction_date) }}')
                ->addColumn(
                    'quantity',
                    '<span class="display_currency" data-currency_symbol="false" data-precision="0">{{ $quantity }}</span>'
                )
                ->addColumn(
                    'price_inc_tax',
                    '<span class="display_currency" data-currency_symbol="true">{{ $unit_price_inc_tax * $quantity }}</span>'
                )
                ->rawColumns(['quantity', 'price_inc_tax'])
                ->toJson();
        }

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, true);
        $sellers = CustomerPortfolio::pluck('name', 'id');

        return view('report.collections', compact('locations', 'sellers'));
    }

    /**
     * Post collections
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postCollections() {
        if (!auth()->user()->can('cxc.collections')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = auth()->user()->business_id;
        $location_id = request()->input('location') ? request()->input('location') : 0;
        $seller_id = request()->input('seller') ? request()->input('seller') : 0;
        $start_date = request()->input('start_date');
        $end_date = request()->input('end_date');

        $collection_transactions = collect(DB::select('CALL get_collection_transactions(?, ?, ?, ?, ?)',
            [$business_id, $location_id, $seller_id, $start_date, $end_date]));

        $collections = collect(DB::select('CALL get_collections(?, ?, ?, ?)',
            [$business_id, $location_id, $start_date, $end_date]));

        $business_name = Business::find($business_id)->business_full_name;
        $start_date = $this->transactionUtil->format_date($start_date);
        $end_date = $this->transactionUtil->format_date($end_date);

        return Excel::download(new CollectionReport($collection_transactions, $collections, $business_name, $start_date, $end_date, $this->transactionUtil),
            __('cxc.collections') . '.xlsx');
    }

    /**
     * Get lab errors report.
     * 
     * @return void
     */
    public function getLabErrorsReport()
    {
        if (! auth()->user()->can('errors_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $params = [
                'location_id' => request()->input('location_id'),
                'status_id' => request()->input('status_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date
            ];

            // Location filter
            if (! empty($params['location_id']) && $params['location_id'] != 'all') {
                $location_id = $params['location_id'];
            } else {
                $location_id = 0;
            }

            // Status lab order filter
            if (! empty($params['status_id']) && $params['status_id'] != 'all') {
                $status_id = $params['status_id'];
            } else {
                $status_id = 0;
            }

            // Date filter
            if (! empty($params['start_date']) && ! empty($params['end_date'])) {
                $start = $params['start_date'];
                $end =  $params['end_date'];
            } else {
                $start = '';
                $end =  '';
            }

            // Lab orders
            $lab_orders = DB::select(
                'CALL lab_errors_report(?, ?, ?, ?)',
                array(
                    $location_id,
                    $status_id,
                    $start,
                    $end
                )
            );

            return Datatables::of($lab_orders)
                ->editColumn(
                    'correlative',
                    '{{ $correlative }}<br><small>{{ $document }}</small>'
                )
                ->editColumn(
                    'status',
                    '<i class="fa fa-circle" style="color: {{ $color }};"></i>&nbsp; {{ $status }}'
                )
                ->editColumn(
                    'no_order',
                    '{{ $no_order }}
                    @if ($number_times > 1)
                    <br><small>{{ __("lab_order.number_times_msg", ["number" => $number_times]) }}</small>
                    @endif'
                )
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-actions" data-lab-order-id="{{ $id }}" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            ' <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <div id="loading" class="text-center">
                                <img src="{{ asset(\'img/loader.gif\') }}" alt="loading" />
                            </div>
                        </ul>
                    </div>'
                )
                ->rawColumns(['correlative', 'status', 'no_order', 'action'])
                ->toJson();
        }

        $business_id = request()->session()->get('user.business_id');

        $customers = Customer::pluck('name', 'id');

        $patients = Patient::pluck('full_name', 'id');

        $status_lab_orders = StatusLabOrder::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        $external_labs = ExternalLab::pluck('name', 'id');
        
        $products = Product::pluck('name', 'id');

        // Hoops
        if (auth()->user()->can('lab_order.admin')) {
            $has_hoop = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_hoop = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "AROS")
                ->where("p.clasification", "!=", "material")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        }

        // Glasses OD
        if (auth()->user()->can('lab_order.admin')) {
            $has_glass_od = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_glass_od = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "LENTES")
                ->where("p.clasification", "!=", "material")
                ->where("p.name", "like", "%DERECHO%")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        }

        // Glasses OS
        if (auth()->user()->can('lab_order.admin')) {
            $has_glass_os = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_glass_os = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "LENTES")
                ->where("p.clasification", "!=", "material")
                ->where("p.name", "like", "%IZQUIERDO%")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        }

        // Glasses VS or BI
        if (auth()->user()->can('lab_order.admin')) {
            $has_glass = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_glass = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "LENTES")
                ->where("p.clasification", "!=", "material")
                ->where("p.name", "not like", "%IZQUIERDO%")
                ->where("p.name", "not like", "%DERECHO%");
            
            $has_glass->where(function ($query) {
                $query->where("p.name", "like", '%V.S.%');
                $query->orWhere("p.name", "like", "%VS.%");
                $query->orWhere("p.name", "like", "%V.S%");
                $query->orWhere("p.name", "like", "%VS%");
                $query->orWhere("p.name", "like", "%bifocal%");
                $query->orWhere("p.name", "like", "%invisible%");
            });

            $has_glass = $has_glass->select(
                "v.id as id",
                "p.name as name"
            )->pluck('p.name', 'v.id');
        }

        $code = $this->transactionUtil->generateLabOrderCode();

        $business_locations = BusinessLocation::pluck('name', 'id');

        $default_location = null;
        
        $warehouses = Warehouse::select('id', 'name')
            ->where('status', 'active')
            ->pluck('name', 'id');

        $default_warehouse = $this->crystal_warehouse;

        $employees = Employees::select('id', DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"));
        $employees = $employees->pluck('full_name', 'id');

        // Header text and columns
        $auxiliar = 0;
        if (!empty(request()->get('opc'))) {
            $auxiliar = request()->get('opc');
        }

        // Locations
        if (auth()->user()->can('lab_order.update')) {
            $locations = BusinessLocation::all()->pluck('name', 'id');
            $locations = $locations->prepend(__("kardex.all_2"), 'all');

            $default_location = null;

        } else {
            $locations = BusinessLocation::forDropdown($business_id, false, false);

            $default_location = null;

            // Access only to one locations
            if (count($locations) == 1) {
                foreach ($locations as $id => $name) {
                    $default_location = $id;
                }
                
            // Access to all locations
            } else if (auth()->user()->permitted_locations() == 'all') {
                $locations = $locations->prepend(__("kardex.all_2"), 'all');
            }
        }

        // Status lab orders
        $status = StatusLabOrder::pluck('name', 'id');
        $status = $status->prepend(__('kardex.all'), 'all');

        return view('report.lab_errors_report')
            ->with(compact(
                'customers',
                'patients',
                'status_lab_orders',
                'business_locations',
                'default_location',
                'warehouses',
                'code',
                'external_labs',
                'products',
                'employees',
                'auxiliar',
                'has_hoop',
                'has_glass_od',
                'has_glass_os',
                'has_glass',
                'locations',
                'status',
                'default_warehouse'
            ));
    }

    /**
     * Get external labs report.
     * 
     * @return void
     */
    public function getExternalLabsReport()
    {
        if (! auth()->user()->can('external_labs_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $params = [
                'location_id' => request()->input('location_id'),
                'status_id' => request()->input('status_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date
            ];

            // Location filter
            if (! empty($params['location_id']) && $params['location_id'] != 'all') {
                $location_id = $params['location_id'];
            } else {
                $location_id = 0;
            }

            // Status lab order filter
            if (! empty($params['status_id']) && $params['status_id'] != 'all') {
                $status_id = $params['status_id'];
            } else {
                $status_id = 0;
            }

            // Date filter
            if (! empty($params['start_date']) && ! empty($params['end_date'])) {
                $start = $params['start_date'];
                $end =  $params['end_date'];
            } else {
                $start = '';
                $end =  '';
            }

            // Lab orders
            $lab_orders = DB::select(
                'CALL external_labs_report(?, ?, ?, ?)',
                array(
                    $location_id,
                    $status_id,
                    $start,
                    $end
                )
            );

            return Datatables::of($lab_orders)
                ->editColumn(
                    'correlative',
                    '{{ $correlative }}<br><small>{{ $document }}</small>'
                )
                ->editColumn(
                    'status',
                    '<i class="fa fa-circle" style="color: {{ $color }};"></i>&nbsp; {{ $status }}'
                )
                ->editColumn(
                    'no_order',
                    '{{ $no_order }}
                    @if ($number_times > 1)
                    <br><small>{{ __("lab_order.number_times_msg", ["number" => $number_times]) }}</small>
                    @endif'
                )
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs btn-actions" data-lab-order-id="{{ $id }}" data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            ' <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <div id="loading" class="text-center">
                                <img src="{{ asset(\'img/loader.gif\') }}" alt="loading" />
                            </div>
                        </ul>
                    </div>'
                )
                ->rawColumns(['correlative', 'status', 'no_order', 'action'])
                ->toJson();
        }

        $business_id = request()->session()->get('user.business_id');

        $customers = Customer::pluck('name', 'id');

        $patients = Patient::pluck('full_name', 'id');

        $status_lab_orders = StatusLabOrder::where('status', 'active')
            ->orderBy('name')
            ->get();
        
        $external_labs = ExternalLab::pluck('name', 'id');
        
        $products = Product::pluck('name', 'id');

        // Hoops
        if (auth()->user()->can('lab_order.admin')) {
            $has_hoop = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_hoop = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "AROS")
                ->where("p.clasification", "!=", "material")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        }

        // Glasses OD
        if (auth()->user()->can('lab_order.admin')) {
            $has_glass_od = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_glass_od = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "LENTES")
                ->where("p.clasification", "!=", "material")
                ->where("p.name", "like", "%DERECHO%")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        }

        // Glasses OS
        if (auth()->user()->can('lab_order.admin')) {
            $has_glass_os = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_glass_os = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "LENTES")
                ->where("p.clasification", "!=", "material")
                ->where("p.name", "like", "%IZQUIERDO%")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        }

        // Glasses VS or BI
        if (auth()->user()->can('lab_order.admin')) {
            $has_glass = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->select(DB::raw("CONCAT(COALESCE(p.name, ''), ' ', COALESCE(v.sub_sku, '')) as name"), 'v.id')
                ->pluck('name', 'v.id');
        } else {
            $has_glass = DB::table("variations as v")
                ->join("products as p", "v.product_id", "p.id")
                ->join("categories as c", "p.category_id", "c.id")
                ->where("c.name", "LENTES")
                ->where("p.clasification", "!=", "material")
                ->where("p.name", "not like", "%IZQUIERDO%")
                ->where("p.name", "not like", "%DERECHO%");
            
            $has_glass->where(function ($query) {
                $query->where("p.name", "like", '%V.S.%');
                $query->orWhere("p.name", "like", "%VS.%");
                $query->orWhere("p.name", "like", "%V.S%");
                $query->orWhere("p.name", "like", "%VS%");
                $query->orWhere("p.name", "like", "%bifocal%");
                $query->orWhere("p.name", "like", "%invisible%");
            });

            $has_glass = $has_glass->select(
                "v.id as id",
                "p.name as name"
            )->pluck('p.name', 'v.id');
        }

        $code = $this->transactionUtil->generateLabOrderCode();

        $business_locations = BusinessLocation::pluck('name', 'id');

        $default_location = null;
        
        $warehouses = Warehouse::select('id', 'name')
            ->where('status', 'active')
            ->pluck('name', 'id');

        $default_warehouse = $this->crystal_warehouse;

        $employees = Employees::select('id', DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"));
        $employees = $employees->pluck('full_name', 'id');

        // Header text and columns
        $auxiliar = 0;
        if (!empty(request()->get('opc'))) {
            $auxiliar = request()->get('opc');
        }

        // Locations
        if (auth()->user()->can('lab_order.update')) {
            $locations = BusinessLocation::all()->pluck('name', 'id');
            $locations = $locations->prepend(__("kardex.all_2"), 'all');

            $default_location = null;

        } else {
            $locations = BusinessLocation::forDropdown($business_id, false, false);

            $default_location = null;

            // Access only to one locations
            if (count($locations) == 1) {
                foreach ($locations as $id => $name) {
                    $default_location = $id;
                }
                
            // Access to all locations
            } else if (auth()->user()->permitted_locations() == 'all') {
                $locations = $locations->prepend(__("kardex.all_2"), 'all');
            }
        }

        // Status lab orders
        $status = StatusLabOrder::pluck('name', 'id');
        $status = $status->prepend(__('kardex.all'), 'all');

        return view('report.external_labs_report')
            ->with(compact(
                'customers',
                'patients',
                'status_lab_orders',
                'business_locations',
                'default_location',
                'warehouses',
                'code',
                'external_labs',
                'products',
                'employees',
                'auxiliar',
                'has_hoop',
                'has_glass_od',
                'has_glass_os',
                'has_glass',
                'locations',
                'status',
                'default_warehouse'
            ));
    }

    /**
     * Show transfer sheet form.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getTransferSheet()
    {
        if (! auth()->user()->can('transfer_sheet.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // // Warehouses
        // $warehouses = Warehouse::forDropdown($business_id, false, false);

        // $default_warehouse = null;

        // // Access only to one warehouse
        // if (count($warehouses) == 1) {
        //     foreach ($warehouses as $id => $name) {
        //         $default_warehouse = $id;
        //     }
            
        // // Access to all warehouses
        // } else if (Warehouse::permittedWarehouses() == 'all') {
        //     $warehouses = $warehouses->prepend(__('kardex.all_2'), 'all');
        // }

        // Warehouses
        $warehouses = Warehouse::pluck('name', 'id');
        $warehouses = $warehouses->prepend(__('kardex.all_2'), 'all');

        $default_warehouse = null;

        return view('report.transfer_sheet')
            ->with(compact('warehouses', 'default_warehouse'));
    }

    /**
     * Get transfer sheet report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postTransferSheet(Request $request)
    {
        if (! auth()->user()->can('transfer_sheet.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Business filter
        $business_id = $request->session()->get('user.business_id');

        // Date filter
        $transfer_date = $this->transactionUtil->uf_date($request->get('transfer_date'));

        $warehouses = collect();

        $warehouse_id = request()->get('warehouse_id');

        if ($warehouse_id == 'all') {
            $warehouses = Warehouse::where('business_id', $business_id)
                ->orderBy('name')
                ->get();

        } else {
            $warehouse = Warehouse::where('id', $warehouse_id)->first();
            $warehouses->push($warehouse);
        }

        $lines = collect();

        foreach ($warehouses as $warehouse) {
            // Get data
            $rows = collect(DB::select(
                'CALL transfer_sheet(?, ?, ?)',
                array($business_id, $transfer_date, $warehouse->id)
            ));

            if ($rows->count() > 0) {
                $lines->push($rows);
            }
        }

        // Delivers
        $delivers = request()->get('delivers');

        // Receives
        $receives = request()->get('receives');
        
        $size = $request->input('size');

        $enable_signature_column = $request->input('enable_signature_column');

        $business = Business::where('id', $business_id)->first();

        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
            // $pdf = \PDF::loadView('reports.transfer_sheet_pdf',
			// 	compact('lines', 'size', 'business', 'enable_signature_column', 'delivers', 'receives'));

            // return $pdf->stream(__('lab_order.transfers_sheet') . '.pdf');

            return view('reports.transfer_sheet_pdf')
                ->with(compact('lines', 'size', 'business', 'enable_signature_column', 'delivers', 'receives'));

        } else {
			return Excel::download(new TransferSheetReportExport($lines, $size, $business, $enable_signature_column, $delivers, $receives), __('lab_order.transfers_sheet') . '.xlsx');
		}
    }

    /**
     * Show payment notes report form.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPaymentNoteReport(Request $request)
    {
        if (! auth()->user()->can('payment_note_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $params = [
                'location_id' => $request->input('location_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date
            ];
    
            // Payments
            $payments = $this->getDataToPaymentNoteReport($params);

            return Datatables::of($payments)
                ->editColumn(
                    'amount',
                    '<span class="display_currency amount" data-currency_symbol="true" data-orig-value="{{ $amount ? $amount : 0.00 }}">{{ $amount ? $amount : 0.00 }}</span>'
                )
                ->editColumn(
                    'balance',
                    '<span class="display_currency" data-currency_symbol="true" data-orig-value="{{ $balance ? $balance : 0.00 }}">{{ $balance ? $balance : 0.00 }}</span>'
                )
                ->addColumn('status', function($row) {
                    $balance = $row->balance;

                    $status = 'partial';

                    if ($balance == 0) {
                        $status = 'paid';
                    } else if ($balance == $row->final_total) {
                        $status = 'due';
                    }

                    return __('lang_v1.' . $status);
                })
                ->rawColumns(['amount', 'balance', 'status'])
                ->toJson();
        }

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }

        return view('report.payment_note_report')
            ->with(compact('locations', 'default_location'));
    }

    /**
     * Get payment notes report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postPaymentNoteReport(Request $request)
    {
        if (! auth()->user()->can('payment_note_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $params = [
            'location_id' => $request->input('location_id'),
            'start_date' => request()->start_date,
            'end_date' => request()->end_date
        ];

        // Payments
        $payments = $this->getDataToPaymentNoteReport($params);

        $size = $request->input('size');

        $business = Business::where('id', $business_id)->first();

        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.payment_note_report_pdf',
				compact('payments', 'size', 'business'));

			return $pdf->stream(__('report.payment_notes_report') . '.pdf');

		} else {
			return Excel::download(new PaymentNoteReportExport($payments, $size, $business), __('report.payment_notes_report') . '.xlsx');
		}
    }

    /**
     * Get data for payment notes report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToPaymentNoteReport($params)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        // Location filter
        if (! empty($params['location_id']) || $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Payments
        $payments = DB::select(
            'CALL payment_note_report(?, ?, ?, ?)',
            array(
                $business_id,
                $location_id,
                $start,
                $end
            )
        );

        return $payments;
    }

     /**
     * Show lab orders report form.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLabOrdersReport(Request $request)
    {
        if (! auth()->user()->can('lab_orders_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            $params = [
                'location_id' => $request->input('location_id'),
                'status_id' => $request->input('status_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date
            ];
    
            // Lab orders
            $lab_orders = $this->getDataToLabOrdersReport($params);

            return Datatables::of($lab_orders)
                ->editColumn(
                    'correlative',
                    '{{ $correlative }}<br><small>{{ $document }}</small>'
                )
                ->editColumn(
                    'status',
                    '<i class="fa fa-circle" style="color: {{ $color }};"></i>&nbsp; {{ $status }}'
                )
                ->editColumn(
                    'delivery', function($row) {
                        $html = '';

                        if (! empty($row->delivery)) {
                            $html .= $this->productUtil->format_date($row->delivery, true);
                        }

                        return $html;
                    }
                )
                ->editColumn(
                    'created_at', function($row) {
                        $html = '';

                        if (! empty($row->created_at)) {
                            $html .= $this->productUtil->format_date($row->created_at, true);
                        }

                        return $html;
                    }
                )
                ->rawColumns(['correlative', 'status', 'delivery', 'created_at'])
                ->toJson();
        }

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__('kardex.all_2'), 'all');
        }

        // Status lab orders
        $status = StatusLabOrder::where('business_id', $business_id)
            ->pluck('name', 'id');

        $status = $status->prepend(__('kardex.all'), 'all');

        return view('report.lab_orders_report')
            ->with(compact('locations', 'default_location', 'status'));
    }

    /**
     * Get lab orders report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postLabOrdersReport(Request $request)
    {
        if (! auth()->user()->can('lab_orders_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $params = [
            'location_id' => $request->input('location_id'),
            'status_id' => $request->input('status_id'),
            'start_date' => request()->start_date,
            'end_date' => request()->end_date
        ];

        // Lab orders
        $lab_orders = $this->getDataToLabOrdersReport($params);

        $size = $request->input('size');

        $business = Business::where('id', $business_id)->first();

        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.lab_orders_report_pdf',
				compact('lab_orders', 'size', 'business'));
            $pdf->setPaper('letter', 'landscape');

			return $pdf->stream(__('report.lab_orders_report') . '.pdf');

		} else {
			return Excel::download(new LabOrdersReportExport($lab_orders, $size, $business), __('report.lab_orders_report') . '.xlsx');
		}
    }

    /**
     * Get data for lab orders report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToLabOrdersReport($params)
    {
        // Business filter
        $business_id = request()->session()->get('user.business_id');

        // Location filter
        if (! empty($params['location_id']) || $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Status lab order filter
        if (! empty($params['status_id']) || $params['status_id'] != 'all') {
            $status_id = $params['status_id'];
        } else {
            $status_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Lab orders
        $lab_orders = DB::select(
            'CALL lab_orders_report(?, ?, ?, ?, ?)',
            array(
                $business_id,
                $location_id,
                $status_id,
                $start,
                $end
            )
        );

        return $lab_orders;
    }

    /**
     * Gets products report.
     *
     * @return \Illuminate\Http\Response
     */
    public function postProductsReport(Request $request)
    {
        if (! auth()->user()->can('products.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $conf_units = Business::select('enable_unit_groups')->where('id', $business_id)->first();
        $conf_units = $conf_units->enable_unit_groups;

        // Filters
        $clasification = ! empty(request()->input('clasification_report')) ? request()->input('clasification_report') : '';
        $category = ! empty(request()->input('category')) ? request()->input('category') : 0;
        $sub_category = ! empty(request()->input('sub_category')) ? request()->input('sub_category') : 0;
        $brand = ! empty(request()->input('brand')) ? request()->input('brand') : 0;

        $is_material = $clasification == 'material' ? 1 : 0;

        if ($conf_units == 1) {
            $products = DB::select(
                'CALL get_products_for_unit_groups_report(?, ?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $clasification,
                    $category,
                    $sub_category,
                    $brand,
                    $is_material
                )
            );

        } else {
            $products = DB::select(
                'CALL get_products_report(?, ?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $clasification,
                    $category,
                    $sub_category,
                    $brand,
                    $is_material
                )
            );
        }

        $size = $request->input('size');

        $business = Business::where('id', $business_id)->first();

        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			// $pdf = \PDF::loadView('reports.products_report_pdf',
			// 	compact('products', 'size', 'business'));

			// return $pdf->stream('products_report.pdf');

            return view('reports.products_report_pdf')
                ->with(compact('products', 'size', 'business'));

		} else {
			return Excel::download(new ProductsReportExport($products, $size, $business), __('report.products_report') . '.xlsx');
		}
    }

    /**
     * Get cost history report.
     * 
     * @param  int  $variation_id
     * @return @return \Illuminate\Http\Response
     */
    public function generateCostHistory($variation_id)
    {
        $variation = Variation::find($variation_id);
        $product = Product::find($variation->product_id);

        $business_id = request()->session()->get('user.business_id');

        $purchases = Transaction::join('purchase_lines', 'purchase_lines.transaction_id', 'transactions.id')
            ->leftJoin('contacts', 'contacts.id', 'transactions.contact_id')
            ->whereIn('transactions.type', ['opening_stock', 'purchase'])
            ->where('transactions.business_id', $business_id)
            ->where('purchase_lines.variation_id', $variation_id)
            ->select(
                'transactions.*',
                DB::raw("IF(contacts.supplier_business_name IS NULL, contacts.name, contacts.supplier_business_name) AS supplier"),
                'purchase_lines.quantity',
                'purchase_lines.purchase_price AS unit_cost'
            )
            ->orderBy('transactions.transaction_date')
            ->orderBy('transactions.id')
            ->groupBy('transactions.id')
            ->get();

        $avg_cost = 0;

        $lines = [];

        foreach ($purchases as $purchase) {
            // Allow recalculation of product cost
            $flag = false;

            // Purchase date
            $transaction_date = $purchase->transaction_date;

            // Add time when transaction_date ends at 00:00:00
            $hour = substr($transaction_date, 11, 18);

            if ($hour == '00:00:00' || $hour == '') {
                $transaction_date = substr($transaction_date, 0, 10) . ' ' . substr($purchase->created_at, 11, 18);
            }

            if ($purchase->type == 'purchase' && $purchase->purchase_type == 'international') {
                $has_apportionment = ApportionmentHasTransaction::where('transaction_id', $purchase->id)->first();

                if (! empty($has_apportionment)) {
                    $apportionment = Apportionment::find($has_apportionment->apportionment_id);
                    $flag = $apportionment->is_finished == 0 ? false : true;
                }

            } else {
                $flag = true;
            }

            if ($flag) {
                $purchase_lines = PurchaseLine::join('transactions', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('purchase_lines.transaction_id', $purchase->id)
                    ->where('transactions.business_id', $business_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->select('purchase_lines.*')
                    ->orderBy('purchase_lines.id')
                    ->get();

                // Check if there are several lines of the same product in the purchase
                $flag_line = $purchase_lines->count() > 1 ? 1 : 0;

                foreach ($purchase_lines as $purchase_line) {
                    $purchase_line_purchase_price = $purchase_line->purchase_price;

                    if ($purchase->type == 'purchase' && $purchase->purchase_type == 'international') {
                        $purchase_line_purchase_price = $purchase_line->purchase_price_inc_tax;
                    }

                    $result = DB::select(
                        'CALL get_stock_before_a_specific_time(?, ?, ?, ?, ?)',
                        [$business_id, $variation_id, $purchase_line->id, $transaction_date, $flag_line]
                    );

                    $stock = $result[0]->stock;

                    // Set default purchase price exc. tax
                    $avg_cost = (($avg_cost * $stock) + ($purchase_line_purchase_price * $purchase_line->quantity)) / ($stock + $purchase_line->quantity);
                
                    $line = [
                        'date' => $transaction_date,
                        'reference' => $purchase->ref_no,
                        'supplier' => $purchase->supplier,
                        'quantity' => $purchase_line->quantity,
                        'unit_cost' => $purchase_line_purchase_price,
                        'stock' => $stock,
                        'avg_cost' => $avg_cost
                    ];

                    array_push($lines, $line);
                }
            }
        }

        $business = Business::where('id', $business_id)->first();

        $pdf = \PDF::loadView(
            'reports.cost_history_pdf',
			compact('lines', 'business', 'variation', 'product')
        );

        return $pdf->stream(__('product.cost_history') . '.pdf');
    }

    /**
     * Show glasses consumption report view.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getGlassesConsumptionReport(Request $request)
    {
        if (! auth()->user()->can('glasses_consumption_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            // Parameters
            $params = [
                // Filters
                'business_id' => $business_id,
                'warehouse_id' => request()->input('warehouse_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date,

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search'),
                'order' => request()->get('order')
            ];

            // Records
            $records = $this->getDataToGlassesConsumptionReport($params);

            return Datatables::of($records['data'])
                ->editColumn('quantity', function ($row) {
                        $quantity = 0;

                        if ($row->quantity) {
                            $quantity = (float)$row->quantity;
                        }

                        return '<span class="display_currency" data-currency_symbol=false data-orig-value="' . $quantity . '">' . $quantity . '</span>';
                    }
                )
                ->addColumn('base', function ($row) {
                        $base = '';

                        $bases = [0, 2, 4, 6, 8, 10];

                        foreach ($bases as $item) {
                            $pattern_1 = "/B$item/";
                            $pattern_2 = "/$item\//";

                            if (preg_match($pattern_1, $row->product) || preg_match($pattern_2, $row->product)) {
                                $base = $item;
                                break;
                            }
                        }

                        return $base;
                    }
                )
                ->rawColumns(['date', 'quantity'])
                ->setTotalRecords($records['count'])
                ->setFilteredRecords($records['count'])
                ->skipPaging()
                ->toJson();
        }

        // Data form
        $warehouses = Warehouse::where('business_id', $business_id)->pluck('name', 'id');
        $warehouses = $warehouses->prepend(__("kardex.all_2"), 'all');
        
        return view('report.glasses_consumption_report')
            ->with(compact('warehouses'));
    }

    /**
     * Generates glasses consumption report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postGlassesConsumptionReport(Request $request)
    {
        if (! auth()->user()->can('glasses_consumption_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            // Params
            $params = [
                'business_id' => $business_id,
                'warehouse_id' => request()->input('warehouse_id'),
                'start_date' => request()->start_date,
                'end_date' => request()->end_date
            ];

            // Records
            $records = $this->getDataToGlassesConsumptionReport($params, true);

            // Report type
            $report_type = $request->input('report_type');

            // Additional data
            $business = Business::find($business_id);
            $start = ! empty(request()->start_date) ? request()->start_date : '';
            $end = ! empty(request()->end_date) ? request()->end_date : '';

            // Title
            $title = __('report.glasses_consumption_report');

            // Table headers
            $headers = [
                __('messages.date'),
                __('lab_order.no_order'),
                __('sale.document_no'),
                __('document_type.title'),
                __('product.sku'),
                __('product.product'),
                __('report.base'),
                __('report.addition'),
                __('lang_v1.quantity'),
            ];

            // Data
            $data = [];

            $header_data = [];

            if ($report_type == 'pdf') {
                $header_data['business_name'] = mb_strtoupper($business->business_full_name);
                $header_data['report_name'] = mb_strtoupper($title);
                
            } else {
                $data[] = [mb_strtoupper($business->business_full_name)];
                $data[] = [mb_strtoupper($title)];
                $data[] = [];
                $data[] = $headers;
            }

            $bases = [0, 2, 4, 6, 8, 10];

            foreach ($records as $item) {
                $base = '';

                foreach ($bases as $base_item) {
                    $pattern_1 = "/B$base_item/";
                    $pattern_2 = "/$base_item\//";

                    if (preg_match($pattern_1, $item->product) || preg_match($pattern_2, $item->product)) {
                        $base = $base_item;
                        break;
                    }
                }

                $line = [
                    $this->transactionUtil->format_date($item->date),
                    $item->no_order,
                    $item->correlative,
                    $item->document_type,
                    $item->sku,
                    $item->product,
                    $base,
                    $item->addition,
                    $this->transactionUtil->num_f($item->quantity)
                ];

                $data[] = $line;
            }

            $output = [
                'success' => true,
                'data' => $data,
                'type' => $report_type,
                'header_data' => $header_data,
                'headers' => [$headers]
            ];

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Get data for glasses consumption report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToGlassesConsumptionReport($params, $print = false)
    {
        // Business filter
        $business_id = $params['business_id'];

        // Warehouse filter
        if (! empty($params['warehouse_id']) && $params['warehouse_id'] != 'all') {
            $warehouse_id = $params['warehouse_id'];
        } else {
            $warehouse_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        if ($print) {
            // Records
            $records = DB::select(
                'CALL print_glasses_consumption_report(?, ?, ?, ?)',
                array(
                    $business_id,
                    $warehouse_id,
                    $start,
                    $end
                )
            );

            $result = $records;

        } else {
            // Datatable parameters
            $start_record = $params['start_record'];
            $page_size = $params['page_size'];
            $search_array = $params['search'];
            $search = ! is_null($search_array['value']) ? $search_array['value'] : '';
            $order = $params['order'];

            // Count records
            $count = DB::select(
                'CALL count_glasses_consumption_report(?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $warehouse_id,
                    $start,
                    $end,
                    $search
                )
            );

            // Records
            $records = DB::select(
                'CALL get_glasses_consumption_report(?, ?, ?, ?, ?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $warehouse_id,
                    $start,
                    $end,
                    $search,
                    $start_record,
                    $page_size,
                    $order[0]['column'],
                    $order[0]['dir']
                )
            );

            $result = [
                'data' => $records,
                'count' => $count[0]->count
            ];
        }

        return $result;
    }

    /**
     * Show stock report by location view.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getStockReportByLocation(Request $request)
    {
        if (! auth()->user()->can('stock_report_by_location.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        if ($request->ajax()) {
            // Parameters
            $params = [
                // Filters
                'business_id' => $business_id,

                // Datatable parameters
                'start_record' => request()->get('start'),
                'page_size' => request()->get('length'),
                'search' => request()->get('search')
            ];

            // Records
            $records = $this->getDataToStockReportByLocation($params);

            return Datatables::of($records['data'])
                ->editColumn('quantity', '{{ @num_format($quantity) }}')
                ->rawColumns(['quantity'])
                ->setTotalRecords($records['count'])
                ->setFilteredRecords($records['count'])
                ->skipPaging()
                ->toJson();
        }
        
        return view('report.stock_report_by_location');
    }

    /**
     * Generates stock report by location in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postStockReportByLocation(Request $request)
    {
        if (! auth()->user()->can('stock_report_by_location.view')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');

            // Params
            $params = [
                'business_id' => $business_id
            ];

            // Records
            $records = $this->getDataToStockReportByLocation($params, true);

            // Report type
            $report_type = $request->input('report_type');

            // Additional data
            $business = Business::find($business_id);

            // Title
            $title = __('report.stock_report_by_location');

            // Table headers
            $headers = [
                __('product.sku'),
                __('accounting.location'),
                __('lang_v1.quantity'),
            ];

            // Data
            $data = [];

            $header_data = [];

            if ($report_type == 'pdf') {
                $header_data['business_name'] = mb_strtoupper($business->business_full_name);
                $header_data['report_name'] = mb_strtoupper($title);
                
            } else {
                $data[] = [mb_strtoupper($business->business_full_name)];
                $data[] = [mb_strtoupper($title)];
                $data[] = [];
                $data[] = $headers;
            }

            foreach ($records as $item) {
                $line = [
                    $item->sku,
                    $item->location,
                    $this->transactionUtil->num_f($item->quantity)
                ];

                $data[] = $line;
            }

            $output = [
                'success' => true,
                'data' => $data,
                'type' => $report_type,
                'header_data' => $header_data,
                'headers' => [$headers]
            ];

        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
            
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Get data for stock report by location.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToStockReportByLocation($params, $print = false)
    {
        // Business filter
        $business_id = $params['business_id'];

        if ($print) {
            // Records
            $records = DB::select(
                'CALL print_stock_report_by_location(?)',
                array($business_id)
            );

            $result = $records;

        } else {
            // Datatable parameters
            $start_record = $params['start_record'];
            $page_size = $params['page_size'];
            $search_array = $params['search'];
            $search = ! is_null($search_array['value']) ? $search_array['value'] : '';

            // Count records
            $count = DB::select(
                'CALL count_stock_report_by_location(?, ?)',
                array(
                    $business_id,
                    $search
                )
            );

            // Records
            $records = DB::select(
                'CALL get_stock_report_by_location(?, ?, ?, ?)',
                array(
                    $business_id,
                    $search,
                    $start_record,
                    $page_size
                )
            );

            $result = [
                'data' => $records,
                'count' => $count[0]->count
            ];
        }

        return $result;
    }

    /**
     * Show sales per seller report view.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getSalesPerSellerReport(Request $request)
    {
        if (! auth()->user()->can('sales_per_seller_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $business_details = $this->businessUtil->getDetails($business_id);

        // Sellers
        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $sellers = [];

        if ($commsn_agnt_setting == 'user') {
            $is_cmmsn_agnt = 0;
        } else {
            $is_cmmsn_agnt = 1;
        }

        $sellers = User::where('is_cmmsn_agnt', $is_cmmsn_agnt)->withTrashed();

        if (config('app.business') != 'optics') {
            $sellers = $sellers->where('business_id', $business_id);
        }

        $sellers = $sellers->select(
                'id',
                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name")
            )
            ->orderBy('full_name')
            ->pluck('full_name', 'id');

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);        

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }
        
        return view('report.sales_per_seller_report')
            ->with(compact('sellers', 'locations', 'default_location'));
    }

    /**
     * Generates sales per seller report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postSalesPerSellerReport(Request $request)
    {
        if (! auth()->user()->can('sales_per_seller_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::find($business_id);

        // Params
        $params = [
            'business' => $business,
            'seller' => request()->input('seller'),
            'location_id' => request()->input('location_id'),
            'start_date' => request()->start_date,
            'end_date' => request()->end_date
        ];

        // Records
        $records = $this->getDataToSalesPerSellerReport($params);

        // Report type
        $report_type = $request->input('report_type');

        // Additional data
        $start = ! empty(request()->start_date) ? request()->start_date : '';
        $end = ! empty(request()->end_date) ? request()->end_date : '';

        // Title
        $title = __('report.sales_per_seller_report');

        // Report type
        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.sales_per_seller_report_pdf',
                compact('records', 'title', 'business', 'start', 'end'));

			return $pdf->stream(__('report.sales_per_seller_report') . '.pdf');

		} else {
			return Excel::download(
                new SalesPerSellerReportExport($records, $business, $start, $end, $this->transactionUtil),
                __('report.sales_per_seller_report') . '.xlsx'
            );
		}
    }

    /**
     * Get data to sales per seller report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToSalesPerSellerReport($params)
    {
        // Business filter
        $business_id = $params['business']->id;

        // Location filter
        if (! empty($params['location_id']) && $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Seller filter
        if (! empty($params['seller']) && $params['seller'] != 'all') {
            $sellers = User::where('id', $params['seller'])->get();

        } else {
            $business_details = $this->businessUtil->getDetails($business_id);
            $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

            if ($commsn_agnt_setting == 'user') {
                $is_cmmsn_agnt = 0;
            } else {
                $is_cmmsn_agnt = 1;
            }

            $sellers = User::where('is_cmmsn_agnt', $is_cmmsn_agnt)->withTrashed();

            if (config('app.business') != 'optics') {
                $sellers = $sellers->where('business_id', $business_id);
            }

            $sellers = $sellers->orderBy('first_name')->get();
        }

        // Records
        $records = [];

        foreach ($sellers as $seller) {
            $sales = DB::select(
                'CALL sales_per_seller(?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $seller->id,
                    $location_id,
                    $start,
                    $end
                )
            );

            $record = [
                'seller' => $seller,
                'sales' => $sales
            ];

            $records[] = $record;
        }

        $result = $records;

        return $result;
    }

    /**
     * Show payment report view.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getPaymentReport(Request $request)
    {
        if (! auth()->user()->can('payment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');

        $business_details = $this->businessUtil->getDetails($business_id);

        // Sellers
        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;
        $sellers = [];

        if ($commsn_agnt_setting == 'user') {
            $is_cmmsn_agnt = 0;
        } else {
            $is_cmmsn_agnt = 1;
        }

        $sellers = User::where('is_cmmsn_agnt', $is_cmmsn_agnt)->withTrashed();

        if (config('app.business') != 'optics') {
            $sellers = $sellers->where('business_id', $business_id);
        }

        $sellers = $sellers->select(
                'id',
                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name")
            )
            ->orderBy('full_name')
            ->pluck('full_name', 'id');

        // Locations
        $locations = BusinessLocation::forDropdown($business_id, false, false);        

        $default_location = null;

        // Access only to one locations
        if (count($locations) == 1) {
            foreach ($locations as $id => $name) {
                $default_location = $id;
            }
            
        // Access to all locations
        } else if (auth()->user()->permitted_locations() == 'all') {
            $locations = $locations->prepend(__("kardex.all_2"), 'all');
        }
        
        return view('report.payment_report')
            ->with(compact('sellers', 'locations', 'default_location'));
    }

    /**
     * Generates payment report in PDF or Excel.
     * 
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postPaymentReport(Request $request)
    {
        if (! auth()->user()->can('payment_report.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $business = Business::find($business_id);

        // Params
        $params = [
            'business' => $business,
            'seller' => request()->input('seller'),
            'location_id' => request()->input('location_id'),
            'start_date' => request()->start_date,
            'end_date' => request()->end_date
        ];

        // Records
        $records = $this->getDataToPaymentReport($params);

        // Report type
        $report_type = $request->input('report_type');

        // Additional data
        $start = ! empty(request()->start_date) ? request()->start_date : '';
        $end = ! empty(request()->end_date) ? request()->end_date : '';

        // Title
        $title = __('report.payment_report');

        // Report type
        $report_type = $request->input('report_type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.payment_report_pdf',
                compact('records', 'title', 'business', 'start', 'end'));

			return $pdf->stream(__('report.payment_report') . '.pdf');

		} else {
			return Excel::download(
                new PaymentReportExport($records, $business, $start, $end, $this->transactionUtil),
                __('report.payment_report') . '.xlsx'
            );
		}
    }

    /**
     * Get data to sales per seller report.
     * 
     * @param  array  $params
     * @return array
     */
    public function getDataToPaymentReport($params)
    {
        // Business filter
        $business_id = $params['business']->id;

        // Location filter
        if (! empty($params['location_id']) && $params['location_id'] != 'all') {
            $location_id = $params['location_id'];
        } else {
            $location_id = 0;
        }

        // Date filter
        if (! empty($params['start_date']) && ! empty($params['end_date'])) {
            $start = $params['start_date'];
            $end =  $params['end_date'];
        } else {
            $start = '';
            $end =  '';
        }

        // Seller filter
        if (! empty($params['seller']) && $params['seller'] != 'all') {
            $sellers = User::where('id', $params['seller'])->get();

        } else {
            $business_details = $this->businessUtil->getDetails($business_id);
            $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

            if ($commsn_agnt_setting == 'user') {
                $is_cmmsn_agnt = 0;
            } else {
                $is_cmmsn_agnt = 1;
            }

            $sellers = User::where('is_cmmsn_agnt', $is_cmmsn_agnt)->withTrashed();

            if (config('app.business') != 'optics') {
                $sellers = $sellers->where('business_id', $business_id);
            }

            $sellers = $sellers->orderBy('first_name')->get();
        }

        // Records
        $records = [];

        foreach ($sellers as $seller) {
            $payments = DB::select(
                'CALL payment_report(?, ?, ?, ?, ?)',
                array(
                    $business_id,
                    $seller->id,
                    $location_id,
                    $start,
                    $end
                )
            );

            $record = [
                'seller' => $seller,
                'payments' => $payments
            ];

            $records[] = $record;
        }

        $result = $records;

        return $result;
    }
}
