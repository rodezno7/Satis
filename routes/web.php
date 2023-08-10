<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include_once('install_r.php');

Route::middleware(['IsInstalled'])->group(function (){

    Route::get('/', function () {
        return view('welcome');
    });
    Auth::routes();
    Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');
    Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');
    Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');
});

Route::middleware(['IsInstalled', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function(){
    Route::get('/start', 'UserController@getFirstSession');
    Route::post('/user/first-session', 'UserController@updatePasswordFirst');
});

Route::post('credits/show-report', 'CreditRequestController@showReport');
Route::resource('credits', 'CreditRequestController');

Route::get('business_types/get-data', 'BusinessTypeController@getBusinessTypeData');
Route::get('payment_terms/get-data', 'PaymentTermController@getPaymentTermData');
Route::get('/documents/default', 'DocumentTypeController@verifyDefault');   

Route::get('/documents/default', 'DocumentTypeController@verifyDefault');
//only create customer
Route::get('/customers/verified_document/{type}/{value}', 'CustomerController@verifiedIfExistsDocument');
//only edit customer
Route::get('/customers/verified_documentID/{type}/{value}/{edit}', 'CustomerController@verifiedIfExistsDocumentID');

//ruta para anular una terminal
Route::post('/terminal/anull/{id}', 'PosController@cancel');
//ruta para activar una terminal
Route::post('/terminal/activate/{id}', 'PosController@activate');

Route::get('/sell/update/{id}', 'SellController@editInvoiceTrans');

//ver el cliente y su saldo
Route::get('/customer-balances/{id}', 'CustomerController@showBalances');
//Ver las facturas por cliente xD
Route::get('/customer-balances/getData/{id}', 'CustomerController@getInvoicePerCustomer');

/** Accounts receivable and report */
Route::get('/accounts-receivable', 'CustomerController@accountsReceivable');
Route::post('/accounts-receivable-report', 'CustomerController@accountsReceivableReport');

Route::get('/customers/get_only_customers', 'CustomerController@getClients');
Route::get('/products/get_only_products', 'ProductController@getProductsSelect');

//Routes for authenticated users only
Route::middleware(['PasswordChanged', 'IsInstalled', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function(){
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    //rutas para busineestypes y paymentTerm
    Route::resource('business_types', 'BusinessTypeController');
    Route::resource('payment_terms', 'PaymentTermController');

    Route::resource('quote/reason', 'ReasonController');

    //Sirve para modificar la anulacion de ventas
    Route::put('/business/update_annull_sale', 'BusinessController@updateAnullSaleExpiry');

    //Ruta para saldos por cliente xD getBalancesCustomersData
    Route::get('/balances_customer', 'CustomerController@indexBalancesCustomer');
    Route::get('/balances_customer/get-data', 'CustomerController@getBalancesCustomersData');

    // Ganancias
    Route::get('/home/get-profits', 'HomeController@getProfitsDetails');

    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home/get-purchase-details', 'HomeController@getPurchaseDetails');
    Route::post('/home/get-sell-details', 'HomeController@getSellDetails');
    Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');
    Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');
    Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');
    Route::get('/home/get-stock-expiry-products', 'HomeController@getStockExpiryProducts');
    Route::get('/home/get-total-stock', 'HomeController@getTotalStock');
    Route::post('/home/choose-month', 'HomeController@chooseMonth');

    //Slider options
    Route::get('/carrousel', 'SliderController@index');
    Route::get('/carrousel/index', 'SliderController@getSliderIndex');
    Route::get('/image/upload', 'SliderController@create');
    Route::post('/image/store', 'SliderController@store');
    Route::get('/image/{id}/edit', 'SliderController@edit');
    Route::patch('/image/{id}/update', 'SliderController@update');
    Route::patch('/image/{id}/status', 'SliderController@setImageStatus');
    Route::get('/image/{id}/show', 'SliderController@show');
    Route::get('/image/{id}/download', 'SliderController@downloadSlide');
    Route::delete('/image/{id}/delete', 'SliderController@destroy');
    // Peak sales hours chart routes
    Route::get('/home/peak-sales-hours-month-chart', 'HomeController@getPeakSalesHoursByMonthChart');
    Route::get('/home/peak-sales-hours-chart', 'HomeController@getPeakSalesHoursChart');

    Route::get('/business/change-modal', 'BusinessController@getChangeBusiness')->name('business.getChangeBusiness');
    Route::patch('/business/change', 'BusinessController@changeBusiness')->name('business.changeBusiness');
    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
    Route::get('business-accounting', 'BusinessController@getAccountingSettings')->name('business.getAccountingSettings');
    Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');
    Route::post('/business/update-accounting', 'BusinessController@postAccountingSettings');
    Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');
    Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');
    Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');

    Route::resource('brands', 'BrandController');

    //DocumentType
    Route::resource('documents','DocumentTypeController');

    Route::resource('payment-account', 'PaymentAccountController');

    Route::resource('tax-rates', 'TaxRateController');
    Route::post('/tax_groups/get_tax_groups', 'TaxGroupController@getTaxGroups');
    Route::post('/tax_groups/get_tax_percent', 'TaxGroupController@getTaxPercent');
    Route::resource('tax_groups', 'TaxGroupController');
    
    Route::get('/unitgroups/getUnitGroupsData', 'UnitGroupController@getUnitGroupsData');
    Route::get('/unitgroups/groupHasLines/{id}', 'UnitGroupController@groupHasLines');
    Route::resource('unitgroups', 'UnitGroupController');
    Route::get('units/getUnits', 'UnitController@getUnits');
    Route::resource('units', 'UnitController');

    Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
    Route::post('/contacts/import', 'ContactController@postImportContacts');
    Route::post('/contacts/check-contact-id', 'ContactController@checkContactId');
    Route::get('/contacts/customers', 'ContactController@getCustomers');
    Route::get('/contacts/suppliers', 'ContactController@getSuppliers');
    Route::get('/contacts/showSupplier/{id}', 'ContactController@showSupplier');
    Route::get('/contacts/get_tests', 'ContactController@getTests');
    Route::resource('contacts', 'ContactController');

    /** Payment Commitment */
    Route::get('/purchases/get-debt-purchases', 'PurchaseController@getDebtPurchases');
    Route::get('/payment-commitments/add-payment-commitment-row', 'PaymentCommitmentController@addPaymentCommitmentRow');
    Route::get('/payment-commitments/annul-payment-commitment/{id}', 'PaymentCommitmentController@annul');
    Route::get('/payment-commitments/print/{id}', 'PaymentCommitmentController@print');
    Route::resource('payment-commitments', 'PaymentCommitmentController');

    Route::get('categories/getCategoriesData', 'CategoryController@getCategoriesData');
    Route::post('categories/updateCatalogueId', 'CategoryController@updateCatalogueId');
    Route::resource('categories', 'CategoryController');

    Route::resource('variation-templates', 'VariationTemplateController');

    Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
    Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
    Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
    Route::post('/products/mass-delete', 'ProductController@massDestroy');
    Route::get('/products/list', 'ProductController@getProducts');
    Route::get('/products/list_stock_transfer', 'ProductController@getProductsTransferStock');
    Route::get('/products/list_for_quotes', 'ProductController@getProductsToQuote');
    Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');

    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
    Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
    Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
    Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
    Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
    Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
    Route::get('/products/quick_add', 'ProductController@quickAdd');
    Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');

    Route::get('/products/view/{id}', 'ProductController@view');
    Route::get('/products/viewSupplier/{id}', 'ProductController@viewSupplier');
    Route::post('/products/addSupplier/{id}', 'ProductController@addSupplier');
    Route::get('/products/deleteSupplier/{id}/{supplierId}', 'ProductController@deleteSupplier');
    Route::get('/products/viewKit/{id}', 'ProductController@viewKit');
    Route::get('/products/productHasSuppliers/{id}', 'ProductController@productHasSuppliers');
    Route::get('/products/kitHasProduct/{id}', 'ProductController@kitHasProduct');
    Route::get('/products/getProductsData', 'ProductController@getProductsData');
    Route::get('/products/createProduct', 'ProductController@createProduct');
    Route::get('/products/getUnitPlan/{id}', 'ProductController@getUnitplan');
    Route::get('/products/getUnitsFromGroup/{id}', 'ProductController@getUnitsFromGroup');
    Route::get('/products/showProduct/{id}', 'ProductController@showProduct');
    Route::get('/products/showStock/{variation_id}/{location_id}', 'ProductController@showStock');
    Route::get('/products/getMeasureFromKitLines/{id}', 'ProductController@getMeasureFromKitLines');
    Route::get('/products/getKardex', 'ReporterController@getKardex');
    Route::post('/products/getKardexReport', 'ReporterController@getKardexReport');
    //Route::get('/products/runData', 'ReporterController@runData');

    // Product accounts by locations
    Route::get('/products/get-product-accounts/{product_id}', 'ProductController@getProductAccountsLocation');
    Route::post('/products/post-product-accounts/{product_id}', 'ProductController@postProductAccountsLocation');

    Route::get('products/get-services', 'ProductController@getServices');

    Route::get('/products/recalculate-product-cost/{variation_id}', 'ProductController@recalculateProductCost');
    Route::get('/products/get-recalculate-cost', 'ProductController@getRecalculateCost');
    Route::post('/products/get-recalculate-cost', 'ProductController@postRecalculateCost');

    Route::resource('products', 'ProductController');
    Route::get('/products/create', 'ProductController@create')->name('product.create');

    Route::get('/purchases/get_products', 'PurchaseController@getProducts');
    Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
    Route::get('/purchases/debts-to-pay-report', 'PurchaseController@debtsToPay');
    Route::post('/purchases/debts-to-pay-report', 'PurchaseController@debtsToPayReport');
    Route::get('/expenses/get_suppliers', 'ExpenseController@getSuppliers');
    Route::get('/expenses/get_contacts', 'ExpenseController@getAccount');
    Route::get('/expenses/{id}/print','ExpenseController@printExpense');
    Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');
    Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');
    Route::get('/purchases/print/{id}/{type}', 'PurchaseController@printInvoice');
    Route::post('/purchases/close-book', 'PurchaseController@closePurchaseBook');
    Route::post('/purchases/is-closed', 'PurchaseController@isClosed');
    Route::get('/purchases/update-imports', 'PurchaseController@updateImports');

    Route::resource('purchases', 'PurchaseController');

    // Fixed assets and fixed asset types
    Route::resource('fixed-assets', 'FixedAssetController');
    Route::resource('fixed-asset-types', 'FixedAssetTypeController');

    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
    Route::get('/sells/drafts', 'SellController@getDrafts');
    Route::get('/sells/quotations', 'SellController@getQuotations');
    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');

    // Update payment balance
    Route::get('/sells/update-payment-balance', 'SellController@updatePaymentBalance');

    /** Get parent correlative form final customer */
    Route::get('/sells/get-parent-correlative', 'SellController@getParentCorrelative');
    Route::get('/sells/get-trans-due-by-customer/{customer_id}', 'SellController@getTransDueByCustomer');
    Route::resource('sells', 'SellController');

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');
    Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');
    Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');
    Route::get('/sells/{transaction_id}/print-ccf', 'SellPosController@printCCF')->name('sell.print-ccf');
    Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');
    Route::post('/pos/getCorrelatives', 'SellPosController@getCorrelatives');
    Route::get('/pos/annul/{v}', 'SellPosController@annul');
    Route::post('/pos/check-customer-patient-name', 'SellPosController@checkCustomerPatientName');
    Route::get('/sell-pos/update-fiscal-document-data', 'SellPosController@updateFiscalDocumentData');
    Route::post('/pos/check-pos-number', 'SellPosController@checkPosNumber');
    Route::get('/sell-pos/update-unit-cost-to-sell-lines/{tsl_initial?}/{tsl_final?}', 'SellPosController@updateUnitCostToSellLines');
    Route::get('/sell-pos/update-sale-price-to-sell-lines/{tsl_initial?}/{tsl_final?}', 'SellPosController@updateSalePriceToSellLines');
    Route::get('/sell-pos/update-sale-price-to-purchase-lines/{pl_initial?}/{pl_final?}', 'SellPosController@updateSalePriceToPurchaseLines');
    Route::resource('pos', 'SellPosController');
    Route::resource('terminal', 'PosController');


    Route::get('roles/verifyRoleName/{name}', 'RoleController@verifyRoleName');
    Route::get('roles/verifyDelete/{id}', 'RoleController@verifyDelete');
    Route::get('roles/getRolesData', 'RoleController@getRolesData');    
    Route::get('roles/getPermissionsByRoles', 'RoleController@getPermissionsByRoles');
    Route::resource('roles', 'RoleController');
    Route::get('modules/getModulesData', 'ModuleController@getModulesData');
    Route::get('modules/getModules', 'ModuleController@getModules');
    Route::resource('modules', 'ModuleController');

    Route::get('permissions/getPermissionsData', 'PermissionController@getPermissionsData');
    Route::resource('permissions', 'PermissionController');
    Route::get('users/getUsersData', 'ManageUserController@getUsersData');
    Route::post('users/changePassword', 'ManageUserController@changePassword');
    Route::resource('users', 'ManageUserController');

    // //Rutas Employees
    Route::get('employees/getEmployeesData', 'ManageEmployeesController@getEmployeesData');
    Route::get('/employees/verify-if-exists-agent-code', 'ManageEmployeesController@verifiedIfExistsAgentCode');
    Route::resource('employees', 'ManageEmployeesController');

    // Rutas Positions
    Route::get('positions/getPositionsData', 'ManagePositionsController@getPositionsData');
    Route::resource('positions', "ManagePositionsController");

    // Rutas Contact Mode
    Route::get('crm-contactmode/getContactModeData', 'CRMContactModeController@getContactModeData');
    Route::resource('crm-contactmode', 'CRMContactModeController');

    // Rutas Contact Reason 
    Route::get('crm-contactreason/getContactReasonData', 'CRMContactReasonController@getContactReasonData');
    Route::resource('crm-contactreason', 'CRMContactReasonController');

    //Rutas Oportunities
    Route::get('oportunities/getOportunityData', 'OportunityController@getOportunityData');
    Route::resource('oportunities', 'OportunityController');
    Route::get('follow-oportunities/getFollowsByOportunity/{id}', 'FollowOportunitiesController@getFollowsByOportunity');
    Route::get('follow-oportunities/getProductsByFollowOportunity/{id}', 'FollowOportunitiesController@getProductsByFollowOportunity');
    Route::get('oportunities/convert-to-customer/{id}', 'OportunityController@createCustomer');
    Route::post('oportunities/convert-to-customer', 'OportunityController@storeCustomer');
    
    Route::resource('follow-oportunities', 'FollowOportunitiesController', ['except'=>'create']);
    Route::get('/follow-oportunities/create/{id}', 'FollowOportunitiesController@create');
    Route::get('/follow-oportunities/showOportunities', 'FollowOportunitiesController@showOportunities');


    /** Quotes */
    Route::get("quotes/get_quotes", "QuoteController@getQuotes");
    Route::get('quotes/addProduct/{variation_id}/{warehouse_id}/{selling_price_group_id?}', 'QuoteController@addProduct');
    Route::get('quotes/addProductNotStock/{variation_id}', 'QuoteController@addProductNotStock');
    Route::get('quotes/getQuotesData', 'QuoteController@getQuotesData');
    Route::get('quotes/getLinesByQuote/{id}', 'QuoteController@getLinesByQuote');
    Route::get('quotes/viewQuote/{id}', 'QuoteController@viewQuote');
    Route::get('quotes/excel/{id}', 'QuoteController@viewExcel');
    Route::resource('quotes', 'QuoteController');

    /** Orders */
    Route::post("orders/get_quote_lines", "OrderController@getQuoteLines");
    Route::get("orders/get_product_row/{quote_id}/{variation_id}/{location_id}/{row_count}", "OrderController@getProductRow");
    Route::get("orders/orders_planner", "OrderController@orderPlanner");
    Route::post('orders/orders_planner_report', 'OrderController@orderPlannerReport');
    Route::get("orders/change_order_status/{id}/{employee_id?}", "OrderController@changeOrderStatus");
    Route::post('orders/refresh-orders-list', 'OrderController@refreshOrdersList');
    Route::get('orders/get_in_charge_people', 'OrderController@getInChargePeople');
    Route::get('orders/get_orders', 'OrderController@getOrders');
    Route::resource('orders', 'OrderController');

    //Rutas Document Correlatives
    Route::get('correlatives/getCorrelativesData', 'DocumentCorrelativeController@getCorrelativesData');
    Route::resource('correlatives', 'DocumentCorrelativeController');

    Route::resource('group-taxes', 'GroupTaxController');

    Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');
    Route::resource('barcodes', 'BarcodeController');

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');
    Route::post('/invoice-schemes/UpdateDiscoount', 'InvoiceSchemeController@UpdateDiscoount');
    Route::resource('invoice-schemes', 'InvoiceSchemeController');

    //Print Labels
    Route::get('/labels/show', 'LabelsController@show');
    Route::get('/labels/add-product-row', 'LabelsController@addProductRow');
    Route::post('/labels/preview', 'LabelsController@preview');
    Route::get('/labels/show/barcode-setting/{has_logo?}', 'LabelsController@getBarcodeSetting');

    //Reports...
    Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');
    Route::get('/reports/table-report', 'ReportController@getTableReport');
    Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');
    Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');
    Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');
    Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');
    Route::get('/reports/stock-report', 'ReportController@getStockReport');
    Route::get('/reports/stock-details', 'ReportController@getStockDetails');
    Route::get('/reports/tax-report', 'ReportController@getTaxReport');
    Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');
    Route::get('/reports/expense-report', 'ReportController@getExpenseReport');
    Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');
    Route::get('/reports/register-report', 'ReportController@getRegisterReport');
    Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');
    Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');
    Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');
    Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');
    Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');
    Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');
    Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');
    Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');
    Route::get('/reports/product-sell-grouped-report', 'ReportController@getproductSellGroupedReport');
    Route::get('/reports/lot-report', 'ReportController@getLotReport');
    Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');
    Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');
    Route::get('/reports/cash_register_report', 'ReporterController@getCashRegisterReport');
    Route::get('/reports/new_cash_register_report', 'ReporterController@getNewCashRegisterReport');
    Route::get('/reports/audit-tape-report/{cashier_closure_id}', 'ReporterController@getAuditTapeReport');

    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', 'LocationSettingsController@index')->name('settings');
        Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');
    Route::get('business-location/accounting-accounts/{location_id}', 'BusinessLocationController@getAccountingAccountByLocation');
    Route::post('business-location/accounting-accounts', 'BusinessLocationController@postAccountingAccountByLocation');
    Route::resource('business-location', 'BusinessLocationController');

    //Invoice layouts..
    Route::resource('invoice-layouts', 'InvoiceLayoutController');

    //Expense Categories...
    Route::resource('expense-categories', 'ExpenseCategoryController');

    //Expenses...
    Route::get('/expenses/get_add_expenses/{bank_transaction_id?}', 'ExpenseController@getAddExpenses');
    Route::post('/expenses/post_add_expenses', 'ExpenseController@postAddExpenses');
    Route::get('/expenses/get_add_expense', 'ExpenseController@getAddExpense');
    Route::get('/expenses/get-purchases-expenses', 'ExpenseController@getPurchasesExpenses');
    Route::get('/expenses/get_expense_details/{expense_id}', 'ExpenseController@getExpenseDetails');
    Route::resource('expenses', 'ExpenseController');

    //Transaction payments...
    Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');
    Route::get('/payments/view-payment/{payment_id}/{entity_type?}', 'TransactionPaymentController@viewPayment');
    Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
    Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');
    Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
    Route::get('payments/multi-payments', 'TransactionPaymentController@multiPayments');
    Route::post('payments/multi-payments', 'TransactionPaymentController@storeMultiPayments');
    Route::resource('payments', 'TransactionPaymentController');
    Route::delete('/payments/{id}/{entity_type?}', 'TransactionPaymentController@destroy');
    Route::get('/payments/{id}/edit/{entity_type?}', 'TransactionPaymentController@edit');

    //Printers...
    Route::resource('printers', 'PrinterController');

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
    Route::resource('stock-adjustments', 'StockAdjustmentController');

    Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');
    Route::get('/cash-register/close-register', 'CashRegisterController@getCloseRegister');
    Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');
    Route::resource('cash-register', 'CashRegisterController');

    Route::resource('cash-detail', 'CashDetailController');

    // Cashier closure
    Route::get('/cashier-closure/generate-accounting-entry/{cashier_closure_id}', 'CashierClosureController@createSaleAccountingEntry');
    Route::get('/cashier-closure/get-cashier-closure/{cashier_closure_id?}', 'CashierClosureController@getCashierClosure');
    Route::post('/cashier-closure/post-cashier-closure', 'CashierClosureController@postCashierClosure');
    Route::get('/cashier-closure/get-daily-z-cut-report/{location_id}/{cashier_id?}/{cashier_closure_id?}', 'CashierClosureController@dailyZCutReport');
    Route::get('/cashier-closure/get-opening-cash-register/{cashier_closure_id}', 'CashierClosureController@openingCashRegister');
    Route::get('/cashier-closure/show-daily-z-cut/{id}', 'CashierClosureController@showDailyZCut');
    Route::get('/reports/daily-z-cut-report', 'ReportController@getDailyZCutReport');
    Route::resource('cashier-closure', 'CashierClosureController');

    //Import products
    Route::get('/import-products', 'ImportProductsController@index');
    Route::post('/import-products/check-file', 'ImportProductsController@checkFile');
    Route::post('/import-products/store', 'ImportProductsController@store');
    Route::post('/import-products/import', 'ImportProductsController@import');
    Route::get('/edit-products', 'ImportProductsController@edit');
    Route::post('/edit-products/check-file', 'ImportProductsController@checkEditFile');
    Route::post('/edit-products/import', 'ImportProductsController@update');

    //Sales Commission Agent
    Route::resource('sales-commission-agents', 'SalesCommissionAgentController');

    //Stock Transfer
    Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');
    Route::post('/stock-transfers/get_product_row_transfer', 'StockTransferController@getProductRowTransfer');
    Route::post('stock-transfers/receive/{id}', 'StockTransferController@receive');
    Route::get('stock-transfers/send', 'StockTransferController@send');
    Route::post('stock-transfers/count/{id}', 'StockTransferController@count');
    Route::resource('stock-transfers', 'StockTransferController');

    Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');
    Route::post('/opening-stock/save', 'OpeningStockController@save');

    //Customer Groups
    Route::resource('customer-group', 'CustomerGroupController');

    //Import opening stock
    Route::get('/import-opening-stock', 'ImportOpeningStockController@index');
    Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');

    //Sell return
    Route::resource('sell-return', 'SellReturnController');
    Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');
    Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');
    Route::get('/sell-return/add/{id}', 'SellReturnController@add');

    //Backup
    Route::get('backup/download/{file_name}', 'BackUpController@download');
    Route::get('backup/delete/{file_name}', 'BackUpController@delete');
    Route::resource('backup', 'BackUpController', ['only' => [
        'index', 'create', 'store'
    ]]);

    Route::resource('selling-price-group', 'SellingPriceGroupController');

    Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');
    Route::post('notification/send', 'NotificationController@send');

    Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');
    Route::get('/purchase-return/purchase_return_discount/{id}', 'PurchaseReturnController@getPurchaseReturnDiscount');
    Route::post('/purchase-return/purchase_return_discount/{id}', 'PurchaseReturnController@postPurchaseReturnDiscount');
    Route::resource('/purchase-return', 'PurchaseReturnController');

    //Restaurant module
    Route::group(['prefix' => 'mod'], function () {

        Route::resource('tables', 'Restaurant\TableController');
        Route::resource('modifiers', 'Restaurant\ModifierSetsController');

        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');
        Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');
        Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');

        Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');

        Route::get('/kitchen', 'Restaurant\KitchenController@index');
        Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');
        Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');

        Route::get('/orders', 'Restaurant\OrderController@index');
        Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');
        Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');
    });

    Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');
    Route::resource('bookings', 'Restaurant\BookingController');

    //Accounting Routes
    Route::get('catalogue/verifyDeleteAccount/{id}', 'CatalogueController@verifyDeleteAccount');
    Route::get('catalogue/getAccounts', 'CatalogueController@getAccounts');
    Route::post('catalogue/get_accounts_for_select2', 'CatalogueController@getAccountsForSelect2');
    Route::get('catalogue/getAccountsParents/{account}', 'CatalogueController@getAccountsParents');
    Route::get('catalogue/verifyCode/{account}/{newCode}', 'CatalogueController@verifyCode');
    Route::get('catalogue/verifyClasif/{code}', 'CatalogueController@verifyClasif');
    Route::get('catalogue/getTree', 'CatalogueController@getTree');
    Route::get('catalogue/getInfoAccount/{id}/{date}', 'CatalogueController@getInfoAccount');
    Route::get('catalogue/getCatalogueData/{id}', 'CatalogueController@getCatalogueData');
    Route::post('catalogue/importCatalogue', 'CatalogueController@importCatalogue');
    Route::resource('catalogue', 'CatalogueController')->except(['create']);


    Route::get('entries/search/{code}', 'AccountingEntrieController@search');
    Route::get('entries/search-period', 'AccountingEntrieController@searchPeriod');
    Route::get('entries/clone-entrie/{id}', 'AccountingEntrieController@cloneEntrie');
    Route::get('entries/create-period', 'AccountingEntrieController@createPeriod');
    Route::get('entries/get-periods', 'AccountingEntrieController@getPeriods');
    Route::get('entries/getEntries/{type}/{location}/{period}', 'AccountingEntrieController@getEntries');
    Route::get('entries/getDetails/{id}', 'AccountingEntrieController@getDetails');
    Route::get('entries/getTotalEntrie/{id}', 'AccountingEntrieController@getTotalEntrie');
    Route::get('entries/getEntrieDetails/{id}', 'AccountingEntrieController@getEntrieDetails');
    Route::get('entries/getEntrieDetailsDebe/{id}', 'AccountingEntrieController@getEntrieDetailsDebe');
    Route::get('entries/getEntrieDetailsHaber/{id}', 'AccountingEntrieController@getEntrieDetailsHaber');
    Route::post('entries/editEntrie', 'AccountingEntrieController@editEntrie');
    Route::post('entries/allentries', 'ReporterController@allEntries');
    Route::get('entries/singleEntrie/{id}/{type}', 'ReporterController@singleEntrie');
    Route::get('entries/searchBankTransaction/{id}', 'AccountingEntrieController@searchBankTransaction');
    Route::get('entries/getNumberEntrie/{date}', 'AccountingEntrieController@getNumberEntrie');
    Route::get('entries/getCorrelativeEntrie/{date}', 'AccountingEntrieController@getCorrelativeEntrie');
    Route::get('entries/changeStatus/{id}/{number}', 'AccountingEntrieController@changeStatus');
    Route::get('entries/getResultCreditorAccounts/{date}', 'AccountingEntrieController@getResultCreditorAccounts');
    Route::get('entries/getResultDebtorAccounts/{date}', 'AccountingEntrieController@getResultDebtorAccounts');
    Route::get('entries/getProfitAndLossAccount', 'AccountingEntrieController@getProfitAndLossAccount');

    Route::get('entries/getApertureDebitAccounts/{date}', 'AccountingEntrieController@getApertureDebitAccounts');
    Route::get('entries/getApertureCreditAccounts/{date}', 'AccountingEntrieController@getApertureCreditAccounts');
    
    Route::get('/entries/assign-short-name', 'AccountingEntrieController@assignShortName');
    Route::get('entries/setNumeration/{mode}/{period}', 'AccountingEntrieController@setNumeration');
    Route::resource('entries', 'AccountingEntrieController')->except(['create']);

    Route::get('auxiliars/getLedger/{id}', 'ReporterController@getLedger');
    Route::get('auxiliars/getAuxiliarDetail/{id}/{from}/{to}', 'ReporterController@getAuxiliarDetail');
    Route::post('auxiliars/getAllAuxiliarReport', 'ReporterController@getAllAuxiliarReport');
    Route::get('auxiliars/getAuxiliarDetails', 'ReporterController@getAuxiliarDetails');

    Route::get('auxiliars/getAuxiliarRange/{start}/{end}', 'ReporterController@getAuxiliarRange');
    Route::get('auxiliars', 'ReporterController@auxiliars');

    Route::get('ledgers/getHigherDetails/{id}/{from}/{to}', 'ReporterController@getHigherDetails');
    Route::get('ledgers/getHigherAccounts', 'ReporterController@getHigherAccounts');
    Route::post('ledgers/getHigherReport', 'ReporterController@getHigherReport');
    Route::get('ledgers/getLedgerRange/{start}/{end}', 'ReporterController@getLedgerRange');
    Route::get('ledgers', 'ReporterController@getHighers');
    Route::get('/journal-book', 'ReporterController@getGralJournalBook');
    Route::post('/post-journal-book', 'ReporterController@postGralJournalBook');

    Route::post('balances/getBalances', 'ReporterController@getBalanceReport');
    Route::post('balances/getBalanceComprobation', 'ReporterController@getBalanceComprobation');
    Route::get('balances/getSignatures/{id}', 'ReporterController@getSignatures');
    Route::post('balances/setSignatures', 'ReporterController@setSignatures');
    Route::get('balances', 'ReporterController@getBalances');
    Route::post('iva_books/getIvaBooksReport', 'ReporterController@getIvaBooksReport');
    Route::get('iva_books', 'ReporterController@getIvaBooks');

    Route::post('result/getResultStatus', 'ReporterController@getResultStatus');

    Route::get('banks/getBanksData', 'BankController@getBanksData');
    Route::get('banks/getBanks', 'BankController@getBanks');
    Route::get('/banks/get-bank-accounts/{bank_id}', 'BankController@getBankAccounts');
    Route::get('banks/getCheckNumber/{id}', 'BankController@getCheckNumber');
    Route::resource('banks', 'BankController');

    Route::get('bank-accounts/getBankAccountsData', 'BankAccountController@getBankAccountsData');
    Route::get('bank-accounts/getBankAccounts', 'BankAccountController@getBankAccounts');
    Route::get('bank-accounts/getBankAccountsById/{id}', 'BankAccountController@getBankAccountsById');
    Route::resource('bank-accounts', 'BankAccountController');

    Route::get('bank-transactions/getBankTransactionsData/{period}/{type}/{bank}', 'BankTransactionController@getBankTransactionsData');
    Route::get('bank-transactions/getConfiguration', 'BankTransactionController@getConfiguration');
    Route::get('bank-transactions/getDateValidation/{type}/{checkbook}/{date}', 'BankTransactionController@getDateValidation');
    Route::get('bank-transactions/getDateByPeriod/{id}', 'BankTransactionController@getDateByPeriod');
    Route::get('bank-transactions/validateDate/{id}/{dat}', 'BankTransactionController@validateDate');
    Route::get('bank-transactions/cancelCheck/{id}', 'BankTransactionController@cancelCheck');
    Route::post('bank-transactions/getBankTransactions', 'ReporterController@getBankTransactions');
    Route::get('bank-transactions/printCheck/{id}/{print}', 'BankTransactionController@printCheck');
    Route::get('bank-transactions/printTransfer/{id}', 'BankTransactionController@printTransferFormat');

    Route::resource('bank-transactions', 'BankTransactionController');

    Route::get('fiscal-years/getFiscalYearsData', 'FiscalYearController@getFiscalYearsData');
    Route::get('fiscal-years/getYears', 'FiscalYearController@getYears');
    Route::resource('fiscal-years', 'FiscalYearController');

    Route::get('accounting-periods/getPeriodsData', 'AccountingPeriodController@getPeriodsData');
    Route::get('accounting-periods/getPeriodStatus/{id}', 'AccountingPeriodController@getPeriodStatus');
    Route::resource('accounting-periods', 'AccountingPeriodController');


    Route::get('type-entries/getTypesData', 'TypeEntrieController@getTypesData');
    Route::get('type-entries/getTypes', 'TypeEntrieController@getTypes');
    Route::resource('type-entries', 'TypeEntrieController');

    Route::get('type-bank-transactions/getTypeBankTransactionsData', 'TypeBankTransactionController@getTypeBankTransactionsData');
    Route::get('type-bank-transactions/getTypeBankTransactions', 'TypeBankTransactionController@getTypeBankTransactions');
    Route::get('type-bank-transactions/get_if_enable_checkbook/{bank_transaction_id}', 'TypeBankTransactionController@getIfEnableCheckbook');
    Route::resource('type-bank-transactions', 'TypeBankTransactionController');

    Route::get('bank-checkbooks/getBankCheckbooksData', 'BankCheckbookController@getBankCheckbooksData');
    Route::get('bank-checkbooks/getBankCheckbooks/{id}', 'BankCheckbookController@getBankCheckbooks');
    Route::get('bank-checkbooks/validateNumber/{id}/{number}', 'BankCheckbookController@validateNumber');
    Route::get('bank-checkbooks/validateRange/{id}/{number}', 'BankCheckbookController@validateRange');
    Route::resource('bank-checkbooks', 'BankCheckbookController');



    //RRHH Routes
    //Routes settings 
    Route::get('rrhh-setting', 'SettingController@index');
    Route::post('rrhh-setting', 'SettingController@store');

    //Routes Employees
    Route::resource('rrhh-employees', 'EmployeesController');
    Route::get('rrhh-employees-getEmployees', 'EmployeesController@getEmployees');
    Route::get('rrhh-employees-getPhoto/{id}', 'EmployeesController@getPhoto');
    Route::post('rrhh-employees/uploadPhoto', 'EmployeesController@uploadPhoto');
    Route::get('/rrhh-employees/verified_document/{type}/{value}/{id?}', 'EmployeesController@verifiedIfExistsDocument');


    // Route::get('/rrhh-import-employees', 'RrhhImportEmployeesController@create');
    // Route::post('/rrhh-import-employees/import', 'RrhhImportEmployeesController@import')->name('employees.import');
    // Route::get('/rrhh-edit-employees', 'RrhhImportEmployeesController@edit');
    // Route::post('/rrhh-edit-employees/import', 'RrhhImportEmployeesController@update');


    Route::resource('rrhh-assistances', 'AssistanceEmployeeController');
    Route::get('rrhh-assistances-getAssistances', 'AssistanceEmployeeController@getAssistances');
    Route::post('/rrhh-assistances-report', 'AssistanceEmployeeController@postAssistancesReport');
    Route::get('rrhh-assistances-show/{id}', 'AssistanceEmployeeController@show');
    Route::get('rrhh-assistances-viewImage/{id}', 'AssistanceEmployeeController@viewImage');

    //Routes documents by employees
    Route::get('rrhh-documents-getByEmployee/{id}', 'RrhhDocumentsController@getByEmployee');
    Route::get('rrhh-documents-createDocument/{id}', 'RrhhDocumentsController@createDocument');
    Route::get('rrhh-documents-files/{id}', 'RrhhDocumentsController@files');
    Route::get('rrhh-documents-viewFile/{id}', 'RrhhDocumentsController@viewFile');
    Route::post('rrhh-documents-updateDocument', 'RrhhDocumentsController@updateDocument');
    Route::resource('rrhh-documents', 'RrhhDocumentsController')->except(['create', 'show', 'update']);

    //Routes contract by employees
    Route::get('rrhh-contracts-getByEmployee/{id}', 'RrhhContractController@getByEmployee');
    Route::get('rrhh-contracts-create/{id}', 'RrhhContractController@create');
    Route::get('rrhh-contracts-generate/{id}', 'RrhhContractController@generate');
    Route::post('rrhh-contracts-update', 'RrhhContractController@updateContract');
    Route::post('rrhh-contracts-finish/{id}', 'RrhhContractController@finishContract');
    Route::get('rrhh-contracts-show/{id}/{employee_id}', 'RrhhContractController@show');
    Route::get('rrhh-contracts-createDocument/{id}/{employee_id}', 'RrhhContractController@createDocument');
    Route::post('rrhh-contracts-storeDocument', 'RrhhContractController@storeDocument');
    Route::resource('rrhh-contracts', 'RrhhContractController')->only(['store', 'show']);
    Route::get('rrhh-contracts-masive', 'RrhhContractController@createMassive');
    Route::post('rrhh-contracts-masive-1', 'RrhhContractController@storeMassive');

    //Routes economic dependencies by employees
    Route::resource('rrhh-economic-dependence', 'RrhhEconomicDependenceController')->except(['index', 'create', 'show', 'update']);
    Route::get('rrhh-economic-dependence-getByEmployee/{id}', 'RrhhEconomicDependenceController@getByEmployee');
    Route::get('rrhh-economic-dependence-create/{id}', 'RrhhEconomicDependenceController@createEconomicDependence');
    Route::post('rrhh-economic-dependence-update', 'RrhhEconomicDependenceController@updateEconomicDependence');

    //Routes economic dependencies by employees
    Route::resource('rrhh-study', 'RrhhStudyController')->except(['index', 'create', 'show', 'update']);
    Route::get('rrhh-study-getByEmployee/{id}', 'RrhhStudyController@getByEmployee');
    Route::get('rrhh-study-create/{id}', 'RrhhStudyController@createStudy');
    Route::post('rrhh-study-update', 'RrhhStudyController@updateStudy');

    //Routes personnel action by employees
    Route::resource('rrhh-personnel-action', 'RrhhPersonnelActionController')->except(['create', 'show', 'update']);
    Route::get('rrhh-personnel-action-getByEmployee/{id}', 'RrhhPersonnelActionController@getByEmployee');
    Route::get('rrhh-personnel-action-create/{id}', 'RrhhPersonnelActionController@createPersonnelAction');
    Route::get('rrhh-personnel-action-masive', 'RrhhPersonnelActionController@createMasive');
    Route::post('rrhh-personnel-action-masive', 'RrhhPersonnelActionController@storeMasive');
    Route::get('rrhh-personnel-action-view/{id}', 'RrhhPersonnelActionController@viewPersonnelAction');
    Route::post('rrhh-personnel-action-update', 'RrhhPersonnelActionController@updatePersonnelAction');
    Route::get('rrhh-personnel-action', 'RrhhPersonnelActionController@index');
    Route::get('rrhh-personnel-action-getByAuthorizer', 'RrhhPersonnelActionController@getByAuthorizer');
    Route::post('rrhh-personnel-action/{id}/confirmAuthorization', 'RrhhPersonnelActionController@confirmAuthorization');
    Route::get('/rrhh-personnel-action/{id}/authorization-report', 'RrhhPersonnelActionController@authorizationReport')->name('rrhh-personnel-action.authorizationReport');
    Route::get('rrhh-personnel-action-createDocument/{id}', 'RrhhPersonnelActionController@createDocument');
    Route::post('rrhh-personnel-action-storeDocument', 'RrhhPersonnelActionController@storeDocument');
    Route::get('rrhh-personnel-action-files/{id}', 'RrhhPersonnelActionController@files');
    Route::get('rrhh-personnel-action-viewFile/{id}', 'RrhhPersonnelActionController@viewFile');

    //Routes economic dependencies by employees
    Route::resource('rrhh-absence-inability', 'RrhhAbsenceInabilityController')->except(['index', 'create', 'show', 'update']);
    Route::get('rrhh-absence-inability-getByEmployee/{id}', 'RrhhAbsenceInabilityController@getByEmployee');
    Route::get('rrhh-absence-inability-create/{id}', 'RrhhAbsenceInabilityController@createAbsenceInability');
    Route::post('rrhh-absence-inability-update', 'RrhhAbsenceInabilityController@updateAbsenceInability');

    //Routes catalogos RRHH
    Route::resource('rrhh-catalogues', 'RrhhHeaderController');
    Route::resource('rrhh-catalogues-data', 'RrhhDataController');
    Route::get('rrhh/getCataloguesData/{id}', 'RrhhDataController@getCatalogueData');
    Route::get('rrhh/create-item/{id}', 'RrhhDataController@createItem');
    Route::get('rrhh/edit-item/{id}', 'RrhhDataController@editItem');

    Route::resource('rrhh-type-wages', 'RrhhTypeWageController');
    Route::get('rrhh/getTypeWagesData', 'RrhhTypeWageController@getTypeWagesData');

    Route::resource('rrhh-type-personnel-action', 'RrhhTypePersonnelActionController');
    Route::get('rrhh/getTypePersonnelActionData', 'RrhhTypePersonnelActionController@getTypePersonnelActionData');
    Route::post('rrhh-type-personnel-action/{id}', 'RrhhTypePersonnelActionController@update');

    Route::resource('/rrhh-catalogues/type-contract', 'RrhhTypeContractController');
    Route::get('/rrhh/getTypes', 'RrhhTypeContractController@getTypes');
    



    /** Cost Centers */
    Route::get('cost_centers/get_main_accounts/{cost_center_id}', 'CostCenterController@getMainAccounts');
    Route::post('cost_centers/post_main_accounts/{cost_center_id}', 'CostCenterController@postMainAccounts');
    Route::get('cost_centers/get_operation_accounts/{cost_center_id}', 'CostCenterController@getOperationAccounts');
    Route::post('cost_centers/post_operation_accounts/{cost_center_id}', 'CostCenterController@postOperationAccounts');
    Route::resource('cost_centers', 'CostCenterController');

    Route::get('geography/getCountriesData', 'CountryController@getCountriesData');
    Route::get('geography/getCountries', 'CountryController@getCountries');
    Route::post('geography/update/{id}', 'CountryController@updateCountry');
    Route::resource('geography', 'CountryController');

    Route::get('zones/getZonesData', 'ZoneController@getZonesData');
    Route::get('zones/getZones', 'ZoneController@getZones');
    Route::resource('zones', 'ZoneController');

    Route::get('states/getStatesData', 'StateController@getStatesData');
    Route::get('states/getStates', 'StateController@getStates');
    Route::get('states/getStatesByCountry/{id}', 'StateController@getStatesByCountry');
    Route::resource('states', 'StateController');

    Route::get('cities/getCitiesData', 'CityController@getCitiesData');
    Route::get('cities/changeStatus/{id}', 'CityController@changeStatus');
    Route::get('cities/getCitiesByState/{id}', 'CityController@getCitiesByState');
    Route::get('cities/getCitiesByStateSelect2/{id?}', 'CityController@getCitiesByStateSelect2');
    Route::resource('cities', 'CityController');

    Route::resource('crm', 'CRMController');

    Route::get('portfolios/getPortfoliosData', 'CustomerPortfolioController@getPortfoliosData');
    Route::resource('portfolios', 'CustomerPortfolioController');

    Route::resource('cashiers', 'CashierController');

    Route::get('claims/getClaimTypesData', 'ClaimTypeController@getClaimTypesData');
    Route::get('claims/getClaimTypes', 'ClaimTypeController@getClaimTypes');
    Route::get('claims/getClaimsData', 'ClaimController@getClaimsData');
    Route::get('claims/getClaimCorrelative', 'ClaimController@getClaimCorrelative');
    Route::get('claims/getNexState/{state_id}/{claim_id}', 'ClaimController@getNexState');
    Route::get('claims/getUsersByClaimType/{id}', 'ClaimController@getUsersByClaimType');
    Route::resource('claims', 'ClaimController');
    Route::get('claim-types/getClaimTypeCorrelative', 'ClaimTypeController@getClaimTypeCorrelative');
    Route::get('claim-types/getUserById/{id}', 'ClaimTypeController@getUserById');
    Route::get('claim-types/getUsersByClaimType/{id}', 'ClaimTypeController@getUsersByClaimType');
    Route::get('claim-types/getSuggestedClosingDate/{date}/{days}', 'ClaimTypeController@getSuggestedClosingDate');
    
    Route::resource('claim-types', 'ClaimTypeController');

    Route::get('sdocs/getSDocsData', 'SupportDocumentsController@getSDocsData');
    Route::resource('sdocs', 'SupportDocumentsController');

    Route::get('manage-credit-requests/getCreditsData', 'ManageCreditRequestController@getCreditsData');
    Route::post('manage-credit-requests/edit', 'ManageCreditRequestController@editCredit');

    Route::get('manage-credit-requests/view/{id}', 'ManageCreditRequestControllerupdate/product@viewCredit');
    Route::resource('manage-credit-requests', 'ManageCreditRequestController');

    // Customers
    Route::get('/customers-import', 'CustomerController@getImportCustomers')->name('customers.import');
    Route::post('/customers-import', 'CustomerController@postImportCustomers');
    Route::get('customers/get_customers', 'CustomerController@getCustomers');
    Route::get('customers/getCustomersData', 'CustomerController@getCustomersData');
    Route::get('customers/get_add_customer/{customer_name?}', 'CustomerController@getAddCustomer');
    Route::get('customers/get_contacts/{id}', 'CustomerController@getContacts');
    Route::post('/customers/store_contacts/{id}', 'CustomerController@addContact');
    Route::post('/customers/export', 'CustomerController@export');
    Route::resource('customers', 'CustomerController');
    Route::get('customers/edit/{id}', 'CustomerController@edit');
    Route::post('customers/update/{id}', 'CustomerController@update');

    Route::get('follow-customers/getFollowsByCustomer/{id}', 'FollowCustomerController@getFollowsByCustomer');
    Route::get('follow-customers/getProductsByFollowCustomer/{id}', 'FollowCustomerController@getProductsByFollowCustomer');
    Route::resource('follow-customers', 'FollowCustomerController');

    //Status Claims Routes
    Route::get('status-claims/getStatusClaimsData', 'StatusClaimController@getStatusClaimsData');
    Route::get('status-claims/getStatusClaimCorrelative', 'StatusClaimController@getStatusClaimCorrelative');
    Route::get('status-claims/getStatusClaims', 'StatusClaimController@getStatusClaims');
    Route::resource('status-claims', 'StatusClaimController');

    // Sale Price Scales Routes
    Route::resource('sale_price_scale', 'SalePriceScaleController');
    Route::get('/get_sale_price_scale/{id}', 'ProductController@getSalePriceScale');
    Route::post('/store_sale_price_scale', 'ProductController@storeSalePriceScale');
    Route::delete('/destroy_sale_price_scale/{id}', 'ProductController@destroySalePriceScale');
    Route::post('/edit_sale_price_scale/{id}', 'ProductController@editSalePriceScale');

    // Warehouses Routes
    Route::get('/warehouses/get_warehouses/{id}', 'WarehouseController@getWarehouseByLocation');
    Route::get('/warehouses/get-location/{warehouse_id}', 'WarehouseController@getLocation');
    Route::get('/warehouses/create-permissions', 'WarehouseController@createPermissions');
    Route::resource('warehouses', 'WarehouseController');

    //Credit Documents
    Route::get('credit-documents/getCDocsData', 'CreditDocumentsController@getCDocsData');
    Route::get('credit-documents/getTransactionByInvoice/{invoice}/{doctype}', 'CreditDocumentsController@getTransactionByInvoice');
    Route::get('credit-documents/reception/{id}', 'CreditDocumentsController@reception');
    Route::get('credit-documents/custodian/{id}', 'CreditDocumentsController@custodian');
    Route::post('credit-documents/saveReception/{id}', 'CreditDocumentsController@saveReception');
    Route::post('credit-documents/saveCustodian/{id}', 'CreditDocumentsController@saveCustodian');
    Route::resource('credit-documents', 'CreditDocumentsController');

    Route::post('print_pos', 'ReporterController@printPOS');
    Route::get('print_test', function(){
        return view('reports.print_test');
    });

    // Movement Types Routes
    Route::resource('movement-types', 'MovementTypeController');

    // Payment terms
    Route::get('/payment-terms/get-payment-terms', 'PaymentTermController@getPaymentTerms');
    Route::resource('/payment-terms','PaymentTermController');

    // Sales book to final consumer
    Route::get('book-final-consumer', 'ReporterController@viewBookFinalConsumer');
    Route::post('book-final-consumer/get-report', 'ReporterController@getBookFinalConsumer');

    // Sales book to taxpayer
    Route::get('book-taxpayer', 'ReporterController@viewBookTaxpayer');
    Route::post('book-taxpayer/get-report', 'ReporterController@getBookTaxpayer');

    // Sales purchases book
    Route::get('purchases-book', 'ReporterController@viewPurchasesBook');
    Route::post('purchases-book/get-report', 'ReporterController@getPurchasesBook');

    // Purchases import routes
    Route::get('/purchases-import', 'PurchaseController@getImportPurchases')->name('purchases.import');
    Route::post('/purchases-import', 'PurchaseController@postImportPurchases');
    Route::post('/purchases-import/process-purchase', 'PurchaseController@importPurchases');

    // Sales and adjustments report routes
    Route::get('/reports/sales-n-adjustments-report', 'ReportController@getSalesAndAdjustmentsReport');
    Route::post('/reports/sales-n-adjustments-report', 'ReportController@postSalesAndAdjustmentsReport');

    // Correlative validation route
    Route::get('/pos/validateCorrelative/{location}/{document}/{correlative}/{transaction_id?}', 'SellPosController@validateCorrelative');

    // Generate accounting entry
    Route::get('/generate-accounting-entry-by-transaction/{transaction_id}', 'SellPosController@createTransAccountingEntry');

    // Cost of sale detail report routes
    Route::get('/product-reports/warehouse-closure-report', 'ReportController@getCostOfSaleDetailReport');
    Route::post('/product-reports/warehouse-closure-report', 'ReportController@postCostOfSaleDetailReport');

    // Kardex Routes
    Route::get('/kardex/get-recalculate-cost', 'KardexController@getRecalculateCost');
    Route::get('/kardex/recalculate-kardex-cost/{variation_id}', 'KardexController@recalculateProductCost');
    Route::get('/kardex/check-cost-balance', 'KardexController@checkCostBalance');
    Route::resource('kardex', 'KardexController');
    Route::get('/kardex/refresh-balance/{warehouse_id}/{variation_id}', 'KardexController@refreshBalance');
    Route::get('register-kardex', 'KardexController@getRegisterKardex');
    Route::post('post-register-kardex', 'KardexController@postRegisterKardex');
    Route::get('/kardex/products/list', 'KardexController@getProducts');
    Route::post('/kardex/report', 'KardexController@generateReport');
    Route::get('/kardex/print_invoice/{transaction_id}/{kardex_id}', 'KardexController@printInvoicePurchase');
    Route::get('/kardex/update_cost/{variation_id}', 'KardexController@updateCost');
    Route::get('/refresh-all-balances', 'KardexController@refreshAllBalances');
    Route::post('/kardex/generate-product-kardex', 'KardexController@generateProductKardex');
    Route::get('/kardex/fix-repeated-transfer/{transfer_id}/{warehouse_id}/{param_variation_id?}', 'KardexController@fixRepeatedTransfer');
    Route::get('/kardexs/compare-stock-kardex', 'KardexController@compareStockAndKardex');
    Route::get('/kardexs/compare-stock-kardex-strict', 'KardexController@compareStockAndKardexStrict');
    Route::get('/kardexs/compare-and-generate-product-kardex/{warehouse_id?}/{variation_initial?}/{variation_final?}', 'KardexController@compareAndGenerateProductKardex');
    Route::get('/kardexs/compare-and-refresh-balance/{warehouse_id?}/{variation_initial?}/{variation_final?}', 'KardexController@compareAndRefreshBalance');
    Route::get('/kardexs/fix-variation-location-detail/{warehouse_id?}/{variation_initial?}/{variation_final?}', 'KardexController@fixVariationLocationDetail');
    Route::get('/kardexs/fix-stock-adjustments/{variation_id}/{location_id}/{warehouse_id}', 'KardexController@fixStockAdjustments');
    Route::get('/kardexs/fix-kit-products/{warehouse_id?}', 'KardexController@fixKitProducts');
    Route::get('/kardexs/compare-sell-and-purchase-lines/{warehouse_id}', 'KardexController@compareSellAndPurchaseLines');
    Route::get('/kardexs/fix-purchase-lines/{sell_transfer_id}/{no_massive?}', 'KardexController@fixPurchaseLines');

    // Stock report routes
    Route::get('/product-reports/show-stock-report', 'ReportController@showStockReport');
    Route::post('/reports/stock-report', 'ReportController@postStockReport');
    Route::get('/reports/get_suppliers', 'ReportController@getSuppliers');

    /** Daily inventory report */
    Route::get('/product-reports/input-output-report', 'ReportController@getInputOutputReport');
    Route::post('/product-reports/input-output-report', 'ReportController@postInputOutputReport');

    /** List price report */
    Route::get('/product-reports/list-price-report', 'ReportController@getListPriceReport');
    Route::post('/product-reports/list-price-report', 'ReportController@postListPriceReport');

    /** Dispatched products report */
    Route::get('/reports/dispatched-products-count', 'ReportController@getDispatchedProductsCount');
    Route::get('/reports/dispatched-products-report', 'ReportController@getDispatchedProducts');
    Route::post('/reports/dispatched-products-report', 'ReportController@postDispatchedProducts');

    /** Connect report for Disproci */
    Route::get('/reports/connect-report', 'ReportController@getConnectReport');
    Route::post('/reports/connect-report', 'ReportController@postConnectReport');

    /** Price List report for Nuves/AGL */
    Route::get('/reports/price-lists-report', 'ReportController@getPriceListsReport');
    Route::post('/reports/post-price-lists-report', 'ReportController@postPriceListsReport');

    Route::get('/debs-pay', function(){
        return view('debs_to_pay.index');
    });

    Route::post('/tax_groups/get_tax_groups', 'TaxGroupController@getTaxGroups');
    Route::post('/tax_groups/get_taxes', 'TaxGroupController@getTaxes');

    // Routes to create permissions
    Route::get('permissions-no-exist', 'PermissionController@getPermissionsNoExist');
    Route::get('permissions-no-exist/register', 'PermissionController@getRegisterPermissions');

    // Stock adjustment report routes
    Route::get('stock-adjustments/print/{id}', 'StockAdjustmentController@printInvoice');
    Route::get('stock-adjustments/create/{ref_count}/{type}/get-reference', 'StockAdjustmentController@getReference');

    //Report remission nte
    Route::get('/reports/remission_note/{id}', 'StockTransferController@getRemissionNote')->name('remission_note');
    //Edit payment method
    Route::get('/sell/payment_method/{id}', 'TransactionPaymentController@editPaymentMethod');

    // Physical inventory routes
    Route::resource('physical-inventory', 'PhysicalInventoryController');
    Route::get('/physical-inventory/change-status/{id}/{status}', 'PhysicalInventoryController@changeStatus');
    Route::get('/physical-inventory/finalize/{id}', 'PhysicalInventoryController@finalize');
    Route::get('/physical-inventory/products/list', 'PhysicalInventoryController@getProducts');
    Route::get('/physical-inventory/mapping/{id}', 'PhysicalInventoryController@mapping');
    Route::post('/physical-inventory/update-execution-date', 'PhysicalInventoryController@updateExecutionDate');
    Route::post('/physical-inventory/update-code', 'PhysicalInventoryController@updateCode');

    // Physical inventory line routes
    Route::resource('physical-inventory-line', 'PhysicalInventoryLineController');
    Route::post('/physical-inventory-line/update-line', 'PhysicalInventoryLineController@updateLine');

    //Products history
    Route::get('/products/purchase_history/{id}', 'ProductController@getHistoryPurchase');
    Route::get('/products/get_history_purchase/{id}', 'ProductController@getDataHistoryPurchase');

    //Report client history
    Route::get('/reports/history_purchase_clients', 'ReporterController@getHistoryPurchaseClients');
    Route::post('/reports/history_purchase_clients/get_report', 'ReporterController@getHistoryPurchaseClientsReport');

    // Sales tracking report routes
    Route::get('/reports/sales-tracking-report', 'ReportController@getSalesTrackingReport');
    Route::post('/reports/sales-tracking-report', 'ReportController@postSalesTrackingReport');
    Route::get('/reports/get_employees', 'ReportController@getEmployees');

    //Lost sales
    Route::get('/quotes/lost_sale/{id}', 'QuoteController@createLostSale');
    Route::get('/quotes/lost_sale/{id}/edit', 'QuoteController@editLostSale');
    Route::post('/quotes/lost_sale/store', 'QuoteController@storeLostSale');
    Route::put('/quotes/lost_sale/{id}/update', 'QuoteController@updateLostSale');

    Route::get('/reports/lost-sales', 'ReportController@getLostSalesReport');
    Route::post('/reports/lost-sales', 'ReportController@postLostSalesReport');

    // Validate if exists the tax number in customers module
    Route::get('/customer/verify-if-exists-tax-number', 'CustomerController@verifiedIfExistsTaxNumber');

    // Validate if exists the tax number in suppliers module
    Route::get('/contact/verify-if-exists-tax-number', 'ContactController@verifiedIfExistsNIT');

    // Validate if exists the dni in suppliers module
    Route::get('/contact/verify-if-exists-dni', 'ContactController@verifiedIfExistsDUI');

    // Check if the client has nit and nrc in POS
    Route::get('/customer/verified_tax_number_sell_pos', 'CustomerController@verifiedTaxNumberSellPos');
    // Check if the client has nit and nrc in PURCHASE
    Route::get('/contact/verified_tax_number_purchase', 'ContactController@verifiedTaxNumberPurchases');

    // All Sales report routes
    Route::get('/sales-reports/all-sales-report', 'ReportController@getAllSalesReport');
    Route::post('/sales-reports/all-sales-report', 'ReportController@postAllSalesReport');
    
    // All Sales report routes
    Route::get('/sales-reports/all-sales-with-utility-report', 'ReportController@getAllSalesWithUtilityReport');
    Route::post('/sales-reports/all-sales-with-utility-report', 'ReportController@postAllSalesWithUtilityReport');

    /** Sales summary by seller */
    Route::get('/reports/sales-summary-report', 'ReporterController@getSalesSummarySellerReport');
    Route::post('/reports/sales-summary-report', 'ReporterController@postSalesSummarySellerReport');

    /** Sales by seller report */
    Route::get('/reports/sales-by-seller-report', 'ReporterController@getSalesBySellerReport');
    Route::post('/reports/sales-by-seller-report', 'ReporterController@postSalesBySellerReport');

    /** Expense Purchase report */
    Route::get('/reports/expense-purchase-report', 'ReporterController@getExpensePurchaseReport');
    Route::post('/reports/expense-purchase-report', 'ReporterController@postExpensePurchaseReport');

    /** Bank reconciliation report */
    Route::post('/bank-transactions/get-bank-reconciliation', 'BankTransactionController@getBankReconciliation');

    // Detailed sales report routes
    Route::get('/reports/detailed-commissions-report', 'ReportController@getDetailedCommissionsReport');
    Route::post('/reports/detailed-commissions-report', 'ReportController@postDetailedCommissionsReport');

    // International Purchases
    Route::resource('/international-purchases', 'InternationalPurchaseController');
    // Check if the client has Tax number and nrc in purchases
    Route::get('/contact/verify-tax-number-reg-number', 'ContactController@verifyTaxNumberAndRegNumber');

    // Price list import
    Route::get('/import-price-list', 'ProductController@getPriceList');
    Route::post('/import-price-list/import', 'ProductController@postPriceList')->name('import-price-lists');

    // Import expenses routes
    Route::resource('import-expenses', 'ImportExpenseController');

    // Calculate tax and payments
    Route::get('/sales/calculate-tax-and-payments', 'SellPosController@calculateTaxAndPayments');

    // Sales toggle dropdown
    Route::get("pos/get_toggle_dropdown/{id}", "SellController@getToggleDropdown");

    // Create all opening balances
    Route::get('/customers/create-opening-balances/{business_id}', 'CustomerController@createAllOpeningBalances');

    // Apportionments
    Route::resource('/apportionments', 'ApportionmentController');
    Route::get('/get_import_expenses', 'ImportExpenseController@getImportExpenses');
    Route::post('/get_import_expense_row', 'ImportExpenseController@getImportExpenseRow');
    Route::get('/get_purchases', 'PurchaseController@getPurchases');
    Route::post('/get_purchase_row', 'PurchaseController@getPurchaseRow');
    Route::post('/get_product_list', 'ApportionmentController@getProductList');
    Route::post('/add_product_row', 'ApportionmentController@addProductRow');

    // Account statement
    Route::get('/reports/account-statement', 'ReportController@getAccountStatement');
    Route::post('/reports/account-statement', 'ReportController@postAccountStatement');

    // Collections report
    Route::get('collections', 'ReportController@getCollections');
    Route::post('post-collections', 'ReportController@postCollections');

    // Mail routes
    Route::post('/balances_customer/send-account-statement', 'MailController@sendAccountStatement');

    // Account statement toggle dropdown
    Route::get("balances_customer/get_toggle_dropdown/{id}", "CustomerController@getToggleDropdown");

    // Create kardex lines route
    Route::get('/kardex/create-kardex-lines/{variation_id}', 'KardexController@createKardexLines');

    // Treasury annexes routes
    Route::get('/treasury-annexes', 'ReporterController@getTreasuryAnnexes');
    Route::post('/treasury-annexes/annex-1', 'ReporterController@exportAnnex1');
    Route::post('/treasury-annexes/annex-2', 'ReporterController@exportAnnex2');
    Route::post('/treasury-annexes/annex-3', 'ReporterController@exportAnnex3');
    Route::post('/treasury-annexes/annex-5', 'ReporterController@exportAnnex5');
    Route::post('/treasury-annexes/annex-6', 'ReporterController@exportAnnex6');
    Route::post('/treasury-annexes/annex-7', 'ReporterController@exportAnnex7');
    Route::post('/treasury-annexes/annex-8', 'ReporterController@exportAnnex8');
    Route::post('/treasury-annexes/annex-9', 'ReporterController@exportAnnex9');

    // Retentions routes
    Route::resource('retentions', 'RetentionController');

    // Cost history report
    Route::get('/reports/cost-history/{variation_id}', 'ReportController@generateCostHistory');

    // Glasses consumption report routes
    Route::get('/reports/glasses-consumption', 'ReportController@getGlassesConsumptionReport');
    Route::post('/reports/glasses-consumption', 'ReportController@postGlassesConsumptionReport');

    // Stock report by location routes
    Route::get('/reports/stock-by-location', 'ReportController@getStockReportByLocation');
    Route::post('/reports/stock-by-location', 'ReportController@postStockReportByLocation');

    // Sales per seller report
    Route::get('/reports/sales-per-seller', 'ReportController@getSalesPerSellerReport');
    Route::post('/reports/sales-per-seller', 'ReportController@postSalesPerSellerReport');

    // Payment report
    Route::get('/reports/payment', 'ReportController@getPaymentReport');
    Route::post('/reports/payment', 'ReportController@postPaymentReport');

    // Binnacle routes
    Route::resource('binnacle', 'BinnacleController');

    // --- BEGIN OPTICS ROUTES ---

    Route::namespace('Optics')->group(function () {
        // Patients Routes
        Route::get('patients/getPatientsData', 'PatientController@getPatientsData');
        Route::resource('patients', 'PatientController');
        Route::get('patients/getEmployeeByCode/{code}', 'PatientController@getEmployeeByCode');
        Route::get('patients_lab/get_patients', 'PatientController@getPatients');
        Route::get('patients/create/{patient_name?}', 'PatientController@create');

        // Material types
        Route::resource('material_type', 'MaterialTypeController');

        // Diagnostics
        Route::resource('diagnostic', 'DiagnosticController');

        // External labs
        Route::resource('external-labs', 'ExternalLabController');

        // Status lab orders
        Route::resource('status-lab-orders', 'StatusLabOrderController');

        // Graduation cards
        Route::resource('graduation-cards', 'GraduationCardController');

        // Lab erders
        Route::get('lab-orders/addProduct/{variation_id}/{warehouse_id}', 'LabOrderController@addProduct');
        Route::get('lab-orders/getProductsByOrder/{id}', 'LabOrderController@getProductsByOrder');
        Route::get('lab-orders/get-report/{id}', 'LabOrderController@getReport');
        Route::get('lab-orders/fillHoopFields/{variation_id}/{transaction_id}', 'LabOrderController@fillHoopFields');
        Route::get('lab-orders/fillHoopFields2/{variation_id}', 'LabOrderController@fillHoopFields2');
        Route::get('pos/lab-orders/create_lab_order', 'LabOrderController@createLabOrder');
        Route::get('lab-orders/close-edit-modal/{id}', 'LabOrderController@closeEditModal');
        Route::get('lab-orders-by-location', 'LabOrderController@getLabOrdersByLocation');
        Route::get('lab-orders/markPrinted/{id}', 'LabOrderController@markPrinted');
        Route::get('lab-orders/second-time/{id}', 'LabOrderController@createOrderSecondTime');
        Route::get('/lab-orders/print/{id}', 'LabOrderController@print');
        Route::get('/lab-orders/change-status/{order_id}/{status_id}', 'LabOrderController@changeStatus');
        Route::get('/lab-orders/print-change-status/{order_id}/{status_id}', 'LabOrderController@changeStatusAndPrint');
        Route::get('/lab-orders/transfer-change-status/{order_id}/{status_id}', 'LabOrderController@changeStatusAndTransfer');
        Route::get('/lab-orders/copy-change-status/{order_id}/{status_id}', 'LabOrderController@changeStatusAndCopy');
        Route::get('/lab-orders/edit-change-status/{order_id}/{status_id}', 'LabOrderController@changeStatusAndEdit');
        Route::get('/lab-orders/getHoops', 'LabOrderController@getHoops');
        Route::get("lab_order/get_toggle_dropdown/{id}", "LabOrderController@getToggleDropdown");
        Route::post('/lab_orders/multiple-change-status', 'LabOrderController@multipleChangeStatus');
        Route::resource('lab-orders', 'LabOrderController');

        // Flow reasons
        Route::resource('flow-reason', 'FlowReasonController');

        // Inflows and outflows
        Route::resource('inflow-outflow', 'InflowOutflowController');
        Route::get('inflow-outflow/create/{type}', 'InflowOutflowController@create');
        Route::get('/pos/inflow-outflow/get_suppliers', 'InflowOutflowController@getSuppliers');
        Route::get('/pos/inflow-outflow/get_employees', 'InflowOutflowController@getEmployees');

        if (config('app.business') == 'optics') {
            // Products
            Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
            Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
            Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
            Route::post('/products/mass-delete', 'ProductController@massDestroy');
            Route::get('/products/list', 'ProductController@getProducts');
            Route::get('/products/list_stock_transfer', 'ProductController@getProductsTransferStock');
            Route::get('/products/list_for_transfers', 'ProductController@getProductsStockTransfer');
            Route::get('/products/list_for_quotes', 'ProductController@getProductsToQuote');
            Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');
            Route::get('/lab_orders/products/list_for_lab_orders', 'ProductController@getProductsToLabOrder');
    
            Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
            Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
            Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
            Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
            Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
            Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
            Route::get('/products/quick_add', 'ProductController@quickAdd');
            Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');
    
            Route::get('/products/view/{id}', 'ProductController@view');
            Route::get('/products/viewSupplier/{id}', 'ProductController@viewSupplier');
            Route::get('/products/viewKit/{id}', 'ProductController@viewKit');
            Route::get('/products/productHasSuppliers/{id}', 'ProductController@productHasSuppliers');
            Route::get('/products/kitHasProduct/{id}', 'ProductController@kitHasProduct');
            Route::get('/products/getProductsData', 'ProductController@getProductsData');
            Route::get('/products/createProduct', 'ProductController@createProduct');
            Route::get('/products/getUnitPlan/{id}', 'ProductController@getUnitplan');
            Route::get('/products/getUnitsFromGroup/{id}', 'ProductController@getUnitsFromGroup');
            Route::get('/products/showProduct/{id}', 'ProductController@showProduct');
            Route::get('/products/showStock/{variation_id}/{location_id}', 'ProductController@showStock');
            Route::get('/products/getMeasureFromKitLines/{id}', 'ProductController@getMeasureFromKitLines');
    
            Route::get("products/get_toggle_dropdown/{id}", "ProductController@getToggleDropdown");

            Route::post('products/check-sku-unique', 'ProductController@checkSkuUnique');

            Route::get('/products/recalculate-product-cost/{variation_id}', 'ProductController@recalculateProductCost');
            Route::get('/products/get-recalculate-cost', 'ProductController@getRecalculateCost');
            Route::post('/products/get-recalculate-cost', 'ProductController@postRecalculateCost');
    
            Route::resource('products', 'ProductController');

            // Materials
            Route::get('/products/getMaterialsData', 'ProductController@getMaterialsData');
            Route::get('/products/materialHasSuppliers/{id}', 'ProductController@materialHasSuppliers');

            // Sale price scales
            Route::get('/get_sale_price_scale/{id}', 'ProductController@getSalePriceScale');
            Route::post('/store_sale_price_scale', 'ProductController@storeSalePriceScale');
            Route::delete('/destroy_sale_price_scale/{id}', 'ProductController@destroySalePriceScale');
            Route::post('/edit_sale_price_scale/{id}', 'ProductController@editSalePriceScale');

            // Name images routes
            Route::get('/name-images', 'ProductController@getNameImages')->name('products.name_images');
            Route::post('/name-images', 'ProductController@postNameImages');

            // Price list import
            Route::get('/import-price-list', 'ProductController@getPriceList');
            Route::post('/import-price-list/import', 'ProductController@postPriceList')->name('import-price-lists');

            Route::get('lab-orders/products/list_for_orders', 'ProductController@getProductsforOrders');

            // Expenses
            Route::get('/expenses/get_suppliers', 'ExpenseController@getSuppliers');
            Route::get('/expenses/get_contacts', 'ExpenseController@getAccount');
            Route::get('/expenses/update-taxes', 'ExpenseController@updateTaxes');
            Route::get('/expenses/set-final-total-from-expenses', 'ExpenseController@setFinalTotalFromExpenses');
            Route::resource('expenses', 'ExpenseController');
            Route::get('/expenses/get_add_expenses/{bank_transaction_id?}', 'ExpenseController@getAddExpenses');
            Route::post('/expenses/post_add_expenses', 'ExpenseController@postAddExpenses');
            Route::get('/expenses/get_add_expense', 'ExpenseController@getAddExpense');
            Route::get('/expenses/get-purchases-expenses', 'ExpenseController@getPurchasesExpenses');
            Route::get('/expenses/get_expense_details/{expense_id}', 'ExpenseController@getExpenseDetails');

            // Expense categories
            Route::resource('expense-categories', 'ExpenseCategoryController');
        }
    });

    // Lab orders
    Route::get('/lab-order-reports/transfer-sheet', 'ReportController@getTransferSheet');
    Route::post('/lab-order-reports/transfer-sheet', 'ReportController@postTransferSheet');
    Route::get('/lab-order-reports/errors-report', 'ReportController@getLabErrorsReport');
    Route::get('/lab-order-reports/external-labs-report', 'ReportController@getExternalLabsReport');

    // Customer and patient
    Route::get('customer-and-patient/create/{name?}', 'CustomerController@createCustomerAndPatient');
    Route::post('customer-and-patient/store', 'CustomerController@storeCustomerAndPatient');

    // Reservations
    Route::resource('reservations', 'ReservationController');
    Route::get('pos/reservations/get_reservations', 'ReservationController@getReservations');
    Route::get("reservations/get_product_row/{quote_id}/{variation_id}/{location_id}/{row_count}", "ReservationController@getProductRow");
    Route::get("reservations/get_payment_row/{removable}/{row_index}/{payment_id}", "ReservationController@getPaymentRow");
    Route::get('/payments/add_payment-to-quote/{quote_id}', 'TransactionPaymentController@addPaymentToQuote');
    Route::get('/payments/show-to-quote/{quote_id}', 'TransactionPaymentController@showToQuote');
    Route::post('/payments/quote', 'TransactionPaymentController@storeToQuote');
    Route::get('/pos/{id}/edit/quote', 'ReservationController@edit');

    // Payment note report
    Route::get('/reports/payment-note-report', 'ReportController@getPaymentNoteReport');
    Route::post('/reports/payment-note-report', 'ReportController@postPaymentNoteReport');

    // Lab orders report
    Route::get('/reports/lab-orders-report', 'ReportController@getLabOrdersReport');
    Route::post('/reports/lab-orders-report', 'ReportController@postLabOrdersReport');

    // POS validations
    Route::get('/correlative/get-final-correlative', 'SellPosController@getFinalCorrelative');

    // Fix transfer
    Route::get('/fix-transfer/{transaction_id}', 'StockTransferController@fixTransfer');

    // Products report
    Route::post('/products/products-report', 'ReportController@postProductsReport');

    // POS
    Route::get('/pos/get_lab_order/{transaction_id}/{patient_id}', 'SellPosController@getLabOrder');
    Route::get('/pos/get_graduation_card/{transaction_id}', 'SellPosController@getGraduationCard');
    Route::post('/pos/post_lab_order', 'SellPosController@postLabOrder');

    // --- END OPTICS ROUTES ---

    // --- BEGIN WORKSHOP ROUTES ---

    // Customer vehicles routes
    Route::post('/get-vehicle-row', 'CustomerController@getVehicleRow');
    Route::get('/import-customer-vehicles', 'CustomerVehicleController@getImporter');
    Route::post('/import-customer-vehicles', 'CustomerVehicleController@postImporter');
    Route::post('/save-customer-vehicles', 'CustomerVehicleController@import');

    // Quotes
    Route::get('/quotes/get-customer-vehicles/{id}', 'CustomerController@getCustomerVehicles');
    Route::post('/quotes/add-service-block/{id}', 'QuoteController@addServiceBlock');
    Route::post('quotes/add-spare/{variation_id}', 'QuoteController@addSpare');
    Route::post('quotes/add-spare-not-stock/{variation_id}', 'QuoteController@addSpareNotStock');
    Route::get('quotes/get-service-blocks-by-quote/{id}', 'QuoteController@getServiceBlocksByQuote');
    Route::get('quotes/viewQuoteWorkshop/{id}', 'QuoteController@viewQuoteWorkshop');
    Route::post('quote/workshop-data/{quote_id}', 'QuoteController@workshopData');
    Route::post("quote/get-spare-lines", 'QuoteController@getSpareLines'); // Workshop route

    // Orders
    Route::post("orders/get-spare-lines", "OrderController@getSpareLines"); // Workshop route
    Route::post('/orders/add-service-block/{id}', 'OrderController@addServiceBlock');

    // --- END WORKSHOP ROUTES ---
});
