<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use \stdClass;
use Validator;
use DataTables;
use App\Business;
use App\Customer;
use App\Catalogue;
use App\Warehouse;
use App\FiscalYear;
use App\Transaction;
use App\DocumentType;
use App\KitHasProduct;
use App\CashierClosure;
use App\AccountingEntrie;
use App\BusinessLocation;
use App\TypeBankTransaction;
use App\TransactionKitSellLine;
use App\AccountingEntriesDetail;
use App\Exports\AnnexExport;
use App\Utils\TaxUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;

use App\Exports\KardexExport;
use App\Exports\IvaBookExport;
use App\Exports\BookTaxpayerExport;
use App\Exports\EntrieReportExport;
use App\Exports\LedgerReportExport;
use App\Exports\ResultStatusExport;
use App\Exports\PurchasesBookExport;
use App\Exports\ExpenseSummaryReportExport;
use App\Exports\SalesSummaryBySeller;
use App\Exports\AuxiliarReportExport;
use App\Exports\GeneralBalanceExport;
use App\Exports\HistoryPurchaseExport;
use App\Exports\BookFinalConsumerExport;
use App\Exports\EntrieSingleReportExport;
use App\Exports\ComprobationBalanceExport;
use App\Exports\BankTransactionsReportExport;
use App\Exports\SalesBySeller;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ReporterController extends Controller
{

	protected $taxUtil;
	protected $BusinessUtil;
	protected $transactionUtil;

	public function __construct(TaxUtil $taxUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil) {
		$this->middleware('auth');
		$this->taxUtil = $taxUtil;
		$this->businessUtil = $businessUtil;
		$this->transactionUtil = $transactionUtil;
	}

	public function allEntries(Request $request) {


		$date_from = Carbon::parse($request->input('from'));
		$date_to = Carbon::parse($request->input('to'));
		$size = $request->input('size');


		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];
		$from_date = $date_from->format('d') .' '.__('accounting.of').' '. $month_from .' '.__('accounting.of').' '. $date_from->format('Y');

		$month_to = $months[($date_to->format('n')) - 1];
		$to_date = $date_to->format('d') .' '.__('accounting.of').' '. $month_to .' '.__('accounting.of').' '. $date_to->format('Y');
		
		$header2 = "".__('accounting.from_date')." ".$from_date." ".__('accounting.to_date')." ".$to_date;
		$header3 = "".__('accounting.from_date')." ".$date_from->format('d/m/Y')." ".__('accounting.to_date')." ".$date_to->format('d/m/Y');
		
		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->business_full_name);
		$accountant = mb_strtoupper($business->accountant);
		$enable_description_line = $business->enable_description_line_entries_report;
		$digits = $business->ledger_digits;

		$inicio = Carbon::parse($request->input('from'))->startOfDay();
		$final = Carbon::parse($request->input('to'))->endOfDay();
		$numero = $request->input('numero');
		$numero = $numero - 1;
		
		$entries = DB::table('accounting_entries as ae')
		->leftJoin('type_entries as te', 'ae.type_entrie_id', 'te.id')
		->select('ae.id', 'ae.correlative', 'ae.date', 'ae.description', 'te.name as type_entrie', 'ae.short_name')
		->where('ae.business_id', $business_id)
		->whereBetween('ae.date', [$inicio, $final])
		->orderBy('ae.correlative', 'asc')->get();
		
		$entrie_details = DB::table('accounting_entries_details as detalle')
		->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')
		->join('accounting_entries as partida', 'partida.id', '=', 'detalle.entrie_id')
		->select('detalle.entrie_id', 'detalle.account_id', 'detalle.debit', 'detalle.credit', 'detalle.description', 'cuenta.code', 'cuenta.name')
		->where('partida.business_id', $business_id)
		->whereBetween('partida.date', [$inicio, $final])
		->orderBy('cuenta.code', 'asc')
		->get();

		$grupos = array();
		$elementos = array();
		$detalles = array();
		
		foreach ($entrie_details as $detail) {

			$mayor = substr($detail->code, 0, $digits);
			$id_partida = $detail->entrie_id;

			if($detail->debit != 0) {

				$columna = "D";
			}

			if($detail->credit != 0) {

				$columna = "H";
			}

			$elemento_grupos = $columna.'.'.$id_partida.'.'.$mayor;

			if (!in_array($elemento_grupos, $grupos)) {

				array_push($grupos, $elemento_grupos);
				$debe = 0;
				$haber = 0;
				
				$cuenta = DB::table('catalogues')
				->select('name')
				->where('business_id', $business_id)
				->where('code', $mayor)
				->first();
				
				if ($cuenta) {
					
					$nombre = $cuenta->name;

				} else {

					$nombre = 'Sin Mayor';

				}
				
				if (($id_partida == $detail->entrie_id)) {

					$valor = $this->obtenerSaldoPartidaMayor($id_partida, $mayor);
					$debe = $valor->debe;
					$haber = $valor->haber;
				}

				$item_elemento = array
				(
					'partida' => $id_partida,
					'mayor' => $mayor,
					'nombre' => $nombre,
					'columna' => $columna,
					'debe' => $debe,
					'haber' => $haber,
				);
				
				array_push($elementos, $item_elemento);
			}
			
			$item_detalle = array
			(
				'entrie_id' => $detail->entrie_id,
				'debe' => $detail->debit,
				'haber' => $detail->credit,
				'mayor' => $mayor,
				'code' => $detail->code,
				'name' => $detail->name,
				'description' => $detail->description,
			);
			array_push($detalles, $item_detalle);
		}
		
		$elements = json_decode(json_encode ($elementos), FALSE);
		$details = json_decode(json_encode ($detalles), FALSE);
		$partidas = array();
		
		foreach($entries as $entrie) {

			$grupos_debe = array();
			$grupos_haber = array();
			$total_debe = 0;
			$total_haber = 0;
			
			foreach($elements as $elemento) {

				$items_debe = array();
				$items_haber = array();
				
				foreach($details as $detalle) {

					if(($entrie->id == $detalle->entrie_id) && ($elemento->partida == $detalle->entrie_id) && ($elemento->mayor == $detalle->mayor)) {

						if($detalle->debe != 0 && $elemento->columna == "D") {

							$elemento_items_debe = array
							(
								'code' => $detalle->code,
								'name' => $detalle->name,
								'valor' => $detalle->debe,
								'description_line' => $detalle->description,
							);
							array_push($items_debe, $elemento_items_debe);
						}

						if($detalle->haber != 0 && $elemento->columna == "H") {

							$elemento_items_haber = array
							(
								'code' => $detalle->code,
								'name' => $detalle->name,
								'valor' => $detalle->haber,
								'description_line' => $detalle->description,
							);
							array_push($items_haber, $elemento_items_haber);
						}
					}
				}

				if(($entrie->id == $elemento->partida) && ($elemento->columna == "D")) {

					$elemento_grupo_debe = array
					(
						'mayor' => $elemento->mayor,
						'nombre' => $elemento->nombre,
						'debe' => $elemento->debe,
						'items' => $items_debe,
					);
					array_push($grupos_debe, $elemento_grupo_debe);
					$total_debe = $total_debe + $elemento->debe;
				}
				
				if(($entrie->id == $elemento->partida) && ($elemento->columna == "H")) {

					$elemento_grupo_haber = array
					(
						'mayor' => $elemento->mayor,
						'nombre' => $elemento->nombre,
						'haber' => $elemento->haber,
						'items' => $items_haber,
					);
					
					array_push($grupos_haber, $elemento_grupo_haber);
					$total_haber = $total_haber + $elemento->haber;
				}
			}
			
			$elemento_partidas = array
			(
				'id' => $entrie->id, 
				'correlative' => $entrie->short_name, 
				'date' => $entrie->date,
				'total_debe' => $total_debe,
				'total_haber' => $total_haber,
				'description' => $entrie->description,
				'grupos_debe' => $grupos_debe,
				'grupos_haber' => $grupos_haber,
				'accountant' => $accountant,
				'type_entrie' => $entrie->type_entrie,
			);
			
			array_push($partidas, $elemento_partidas);
		}

		$datos = json_decode(json_encode ($partidas), FALSE);
		$report_type = $request->input('report-type');
		
		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.entries_pdf', compact('enable_description_line', 'datos', 'numero', 'business_name', 'header3', 'size'));
			return $pdf->stream('Entrie.pdf');
		} else {
			return Excel::download(new EntrieReportExport($enable_description_line, $datos, $numero, $business_name, $header3), 'Entrie.xlsx');
		}
	}

	public function singleEntrie($id, $type){

		
		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->name);
		$accountant = mb_strtoupper($business->accountant);
		$enable_description_line = $business->enable_description_line_entries_report;
		$digits = $business->ledger_digits;
		
		$numero = 0;

		$entries = DB::table('accounting_entries as ae')
		->leftJoin('type_entries as te', 'ae.type_entrie_id', 'te.id')
		->select('ae.id', 'ae.correlative', 'ae.date', 'ae.description', 'te.name as type_entrie', 'ae.short_name')
		->where('ae.id', $id)
		->orderBy('ae.correlative', 'asc')
		->get();

		$entrie_details = DB::table('accounting_entries_details as detalle')
		->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')
		->join('accounting_entries as partida', 'partida.id', '=', 'detalle.entrie_id')
		->select('detalle.entrie_id', 'detalle.account_id', 'detalle.debit', 'detalle.credit', 'detalle.description', 'cuenta.code', 'cuenta.name')
		->where('partida.id', $id)
		->orderBy('cuenta.code', 'asc')
		->get();

		$grupos = array();
		$elementos = array();
		$detalles = array();
		
		foreach ($entrie_details as $detail) {

			$mayor = substr($detail->code, 0, $digits);
			$id_partida = $detail->entrie_id;
			
			if($detail->debit != 0) {

				$columna = "D";
			}

			if($detail->credit != 0) {

				$columna = "H";
			}

			$elemento_grupos = $columna.'.'.$id_partida.'.'.$mayor;
			
			if (!in_array($elemento_grupos, $grupos)) {

				array_push($grupos, $elemento_grupos);

				$debe = 0;
				$haber = 0;
				
				$cuenta = DB::table('catalogues')
				->select('name')
				->where('business_id', $business_id)
				->where('code', $mayor)
				->first();

				if ($cuenta) {

					$nombre = $cuenta->name;

				} else {
					
					$nombre = 'Sin Mayor';

				}
				
				$nombre = $cuenta->name;
				
				if (($id_partida == $detail->entrie_id)) {

					$valor = $this->obtenerSaldoPartidaMayor($id_partida, $mayor);
					$debe = $valor->debe;
					$haber = $valor->haber;
				}
				
				$item_elemento = array
				(
					'partida' => $id_partida,
					'mayor' => $mayor,
					'nombre' => $nombre,
					'columna' => $columna,
					'debe' => $debe,
					'haber' => $haber,
				);
				
				array_push($elementos, $item_elemento);
			}
			
			$item_detalle = array
			(
				'entrie_id' => $detail->entrie_id,
				'debe' => $detail->debit,
				'haber' => $detail->credit,
				'mayor' => $mayor,
				'code' => $detail->code,
				'name' => $detail->name,
				'description' => $detail->description,
			);
			array_push($detalles, $item_detalle);
		}
		
		$elements = json_decode(json_encode ($elementos), FALSE);
		$details = json_decode(json_encode ($detalles), FALSE);
		$partidas = array();

		foreach($entries as $entrie) {

			$grupos_debe = array();
			$grupos_haber = array();
			$total_debe = 0;
			$total_haber = 0;

			foreach($elements as $elemento) {

				$items_debe = array();
				$items_haber = array();

				foreach($details as $detalle) {

					if(($entrie->id == $detalle->entrie_id) && ($elemento->partida == $detalle->entrie_id) && ($elemento->mayor == $detalle->mayor)) {

						if($detalle->debe != 0 && $elemento->columna == "D") {

							$elemento_items_debe = array
							(
								'code' => $detalle->code,
								'name' => $detalle->name,
								'valor' => $detalle->debe,
								'description_line' => $detalle->description,
							);
							array_push($items_debe, $elemento_items_debe);
						}

						if($detalle->haber != 0 && $elemento->columna == "H") {

							$elemento_items_haber = array
							(
								'code' => $detalle->code,
								'name' => $detalle->name,
								'valor' => $detalle->haber,
								'description_line' => $detalle->description,
							);
							array_push($items_haber, $elemento_items_haber);
						}
					}
				}

				if(($entrie->id == $elemento->partida) && ($elemento->columna == "D")) {

					$elemento_grupo_debe = array
					(
						'mayor' => $elemento->mayor,
						'nombre' => $elemento->nombre,
						'debe' => $elemento->debe,
						'items' => $items_debe,
					);

					array_push($grupos_debe, $elemento_grupo_debe);
					$total_debe = $total_debe + $elemento->debe;
				}

				if(($entrie->id == $elemento->partida) && ($elemento->columna == "H")) {

					$elemento_grupo_haber = array
					(
						'mayor' => $elemento->mayor,
						'nombre' => $elemento->nombre,
						'haber' => $elemento->haber,
						'items' => $items_haber,
					);
					array_push($grupos_haber, $elemento_grupo_haber);
					$total_haber = $total_haber + $elemento->haber;
				}
			}

			$elemento_partidas = array
			(
				'id' => $entrie->id, 
				'correlative' => $entrie->short_name,
				'date' => $entrie->date,
				'total_debe' => $total_debe,
				'total_haber' => $total_haber,
				'description' => $entrie->description,
				'grupos_debe' => $grupos_debe,
				'grupos_haber' => $grupos_haber,
				'accountant' => $accountant,
				'type_entrie' => $entrie->type_entrie,
			);
			array_push($partidas, $elemento_partidas);
		}

		$datos = json_decode(json_encode ($partidas), FALSE);

		$report_type = $type;

		if ($report_type == 'pdf') {

			$pdf = \PDF::loadView('reports.entrie_pdf', compact('enable_description_line', 'datos', 'numero', 'business_name'));
			
			return $pdf->stream('Entries.pdf');

		} else {
			
			return Excel::download(new EntrieSingleReportExport($enable_description_line, $datos, $numero, $business_name), 'Entrie.xlsx');
		}
		
	}
	
	protected function obtenerSaldoPartidaMayor($id, $code) {

		$business_id = request()->session()->get('user.business_id');

		$valor = DB::table('accounting_entries_details as detalle')
		->join('catalogues as cuenta', 'detalle.account_id', '=', 'cuenta.id')
		->select(DB::raw('SUM(detalle.debit) debe, SUM(detalle.credit) haber'))
		->where('cuenta.business_id', $business_id)
		->where('detalle.entrie_id', $id)
		->where('cuenta.code', 'like', ''.$code.'%')
		->first();

		return $valor;
	}

	public function auxiliars() {

		if(!auth()->user()->can('auxiliars')) {
			return redirect('home');
		}

		$business_id = request()->session()->get('user.business_id');

		$accounts = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->where('status', 1)
		->whereNOTIn('id', [DB::raw("select parent from catalogues where parent IS NOT NULL")])
		->orderBy('code', 'asc')
		->get();

		$clasifications = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->where('status', 1)
		->where('parent', 0)
		->orderBy('code', 'asc')
		->get();

		return view('auxiliars.index', compact('accounts', 'clasifications'));
	}

	public function getLedger($id) {

		$cuenta = Catalogue::where('id', $id)->first();
		$mayor = substr($cuenta->code, 0, 4);
		$business_id = request()->session()->get('user.business_id');
		$cuenta_mayor = Catalogue::where('code', $mayor)
		->where('business_id', $business_id)
		->first();
		
		return response()->json($cuenta_mayor);

	}

	/**
	 * Displays subledger by account and date range.
	 * 
	 * @param  int  $id
	 * @param  date  $from
	 * @param  date  $to
	 * @return json
	 */
	public function getAuxiliarDetail($id, $from, $to) {

		$account = DB::table('catalogues')
		->where('id', $id)
		->first();

		$auxiliars = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'detail.entrie_id', '=', 'entrie.id')
		->select(
			DB::raw('DATE_FORMAT(entrie.date, "%d/%m/%Y") as date, entrie.id as entrie_id'),
			'entrie.description',
			'entrie.correlative',
			'detail.account_id',
			'detail.debit',
			'detail.credit',
			'detail.description as detail_description'
		)
		->where('detail.account_id', $id)
		->whereBetween('entrie.date', [$from, $to])
		->where('entrie.status', 1)
		->orderBy('entrie.date', 'asc')
		->orderBy('entrie.correlative', 'asc')
		->get();

		$initial_balance_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'detail.entrie_id', '=', 'entrie.id')
		->select(DB::raw('SUM(detail.debit) debit, SUM(detail.credit) credit'))
		->where('entrie.status', 1)
		->where('detail.account_id', $id)
		->where('entrie.date', '<', $from)
		->first();
		
		if ($account->type == 'debtor') {
			
			$balance = $initial_balance_q->debit - $initial_balance_q->credit;

		} else {

			$balance = $initial_balance_q->credit - $initial_balance_q->debit;
		
		}
		
		$lines = array();
		$cont = 1;
		
		$item_init = array(
			'cont' => $cont,
			'date' => '',
			'description' => ' ' . $account->code . ' ' . $account->name . '...... ' . __('accounting.initial_balance') . '',
			'correlative' => ' ',
			'debit' => ' ',
			'credit' => ' ',
			'balance' => number_format($balance, 2),
		);

		array_push($lines, $item_init);
		
		foreach ($auxiliars as $auxiliar) {

			if ($account->type == 'debtor') {

				$balance = $balance + $auxiliar->debit - $auxiliar->credit;

			} else {

				
				$balance = $balance + $auxiliar->credit - $auxiliar->debit;
			}
			
			$cont = $cont + 1;

			$item = array(
				'cont' => $cont,
				'date' => $auxiliar->date,
				'description' => $auxiliar->detail_description,
				'correlative' => $auxiliar->correlative,
				'debit' =>  number_format($auxiliar->debit, 2),
				'credit' => number_format($auxiliar->credit, 2),
				'balance' => number_format($balance, 2),
			);

			array_push($lines, $item);
		}

		return DataTables::of($lines)->toJson();
	}

	public function getAuxiliarDetails() {

		$business_id = request()->session()->get('user.business_id');

		$accounts = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->where('status', 1)
		->whereNOTIn('id', [DB::raw("select parent from catalogues where parent IS NOT NULL")])
		->orderBy('code', 'asc')
		->get();
		
		return response()->json($accounts);
	}

	public function getAuxiliarRange(Catalogue $start, Catalogue $end) {

		$from = $start->code;
		$to = $end->code;
		$business_id = request()->session()->get('user.business_id');

		if($from == $to) {

			$accounts = Catalogue::select('id', 'code', 'name')
			->where('status', 1)
			->where('business_id', $business_id)
			->whereNOTIn('id', [DB::raw("select parent from catalogues where parent IS NOT NULL")])
			->where('code', 'like', ''.$from.'%')
			->orderBy('code', 'asc')
			->get();

			$data = $accounts;

		} else {

			$data = new Collection();
			
			for($i = $from; $i <= $to; $i++) {

				$accounts = Catalogue::select('id', 'code', 'name')
				->where('business_id', $business_id)
				->where('status', 1)
				->whereNOTIn('id', [DB::raw("select parent from catalogues where parent IS NOT NULL")])
				->where('code', 'like', ''.$i.'%')
				->orderBy('code', 'asc')
				->get();

				$data = $data->merge($accounts);
			}
		}

		return response()->json($data);

	}

	public function getLedgerRange(Catalogue $start, Catalogue $end) {

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$digits = $business->ledger_digits;

		$from = $start->code;
		$to = $end->code;

		if($from == $to) {

			$accounts = Catalogue::select('id', 'code', 'name')
			->where('business_id', $business_id)
			->where('status', 1)
			->where('code', 'like', ''.$from.'%')			
			->whereRaw('LENGTH(code) = '.$digits.'')
			->orderBy('code', 'asc')
			->get();

			$data = $accounts;

		} else {

			$data = new Collection();
			
			for($i = $from; $i <= $to; $i++) {

				$accounts = Catalogue::select('id', 'code', 'name')
				->where('business_id', $business_id)
				->where('status', 1)
				->where('code', 'like', ''.$i.'%')
				->whereRaw('LENGTH(code) = '.$digits.'')
				->orderBy('code', 'asc')
				->get();

				$data = $data->merge($accounts);
			}
		}

		return response()->json($data);
	}

	/**
	 * Generate subledger in PDF or Excel format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function getAllAuxiliarReport(Request $request) {

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->name);

		$date_from = Carbon::parse($request->input('from'));
		$date_to = Carbon::parse($request->input('to'));
		$size = $request->input('size');

		$months = array(
			__('accounting.january'),
			__('accounting.february'),
			__('accounting.march'),
			__('accounting.april'),
			__('accounting.may'),
			__('accounting.june'),
			__('accounting.july'),
			__('accounting.august'),
			__('accounting.september'),
			__('accounting.october'),
			__('accounting.november'),
			__('accounting.december')
		);

		$month_from = $months[($date_from->format('n')) - 1];
		$month_to = $months[($date_to->format('n')) - 1];
		$report_name = __('accounting.auxiliar_report');

		if ($date_from->format('Y') == $date_to->format('Y')) {

			if ($month_from == $month_to) {

				$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');

			} else {

				$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.of') . " " . $month_from . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');

			}

			
		} else {

			$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.of') . " " . $month_from . " " . __('accounting.of') . " " . $date_from->format('Y') . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');
		}
		

		

		$ids = $request->input('id');

		$accounts = DB::table('catalogues')
		->where('business_id', $business_id)
		->select('id', 'code', 'name')
		->whereIn('id', $ids)			
		->orderBy('code', 'asc')
		->get();

		$accounts_q = collect(DB::select('CALL getAuxiliarAccounts(?, ?, ?)', [$date_from, $date_to, $business_id]));

		$accounts = $accounts_q
		->whereIn('id', $ids);

		$details = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'entrie.id', '=', 'detail.entrie_id')
		->select(
			'detail.*',
			'entrie.date as date',
			'entrie.short_name as short_name',
			'entrie.description as entrie_description',
			'entrie.correlative as correlative',
		)
		->where('entrie.business_id', $business_id)
		->where('entrie.date', '>=', $date_from)
		->where('entrie.date', '<=', $date_to)
		->where('entrie.status', 1)
		->whereIn('detail.account_id', $ids)
		->orderBy('entrie.date', 'asc')
		->orderBy('entrie.correlative', 'asc')
		->get();

		$report_type = $request->input('report-type');

		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.auxiliars_pdf', compact('accounts', 'details', 'report_name', 'date_range', 'business_name', 'size'));
			return $pdf->stream('auxiliars.pdf');

		} else {
			return Excel::download(new AuxiliarReportExport($accounts, $details, $report_name, $date_range, $business_name), 'Auxiliar.xlsx');
		}
	}

	public function getHighers() {

		if(!auth()->user()->can('ledgers')) {
			return redirect('home');
		}

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$digits = $business->ledger_digits;
		
		$accounts = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->where('status', 1)
		->whereRaw('LENGTH(code) = '.$digits.'')
		->orderBy('code', 'asc')
		->get();

		$clasifications = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->where('status', 1)
		->where('parent', 0)
		->orderBy('code', 'asc')
		->get();
		
		return view('ledgers.index', compact('accounts', 'clasifications'));
	}

	public function getHigherDetails($id, $from, $to) {

		$account = DB::table('catalogues')
		->where('id', $id)
		->first();

		$business_id = request()->session()->get('user.business_id');

		$ledgers = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'detail.entrie_id', '=', 'entrie.id')
		->join('catalogues as catalogue', 'detail.account_id', '=', 'catalogue.id')
		->select(DB::raw('DATE_FORMAT(date, "%d/%m/%Y") as date, SUM(detail.debit) debit, SUM(detail.credit) credit, entrie.correlative'), 'entrie.description')
		->where('entrie.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$account->code.'%')
		->whereBetween('entrie.date', [$from, $to])
		->where('entrie.status', 1)
		->groupBy('entrie.date')
		->orderBy('entrie.date', 'asc')
		->orderBy('entrie.correlative', 'asc')
		->get();

		$initial_balance_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'detail.entrie_id', '=', 'entrie.id')
		->join('catalogues as catalogue', 'detail.account_id', '=', 'catalogue.id')
		->select(DB::raw('SUM(detail.debit) debit, SUM(detail.credit) credit'))
		->where('entrie.business_id', $business_id)
		->where('entrie.status', 1)
		->where('catalogue.code', 'like', ''.$account->code.'%')
		->where('entrie.date', '<', $from)
		->first();

		if ($account->type == 'debtor') {
			
			$balance = $initial_balance_q->debit - $initial_balance_q->credit;
		
		} else {

			$balance = $initial_balance_q->credit - $initial_balance_q->debit;
		
		}

		$lines = array();
		$cont = 1;
		
		$item_init = array(

			'cont' => $cont,
			'date' => '',
			'description' => ' '.$account->code.' '.$account->name.'...... '.__('accounting.initial_balance').'',
			'correlative' => ' ',
			'debit' => ' ',
			'credit' => ' ',
			'balance' => number_format($balance, 2),
		);
		
		array_push($lines, $item_init);

		foreach ($ledgers as $ledger) {

			if($account->type == 'debtor') {

				$balance = $balance + $ledger->debit - $ledger->credit;
			
			} else {

				$balance = $balance + $ledger->credit - $ledger->debit;
			
			}

			$cont = $cont + 1;

			$item = array(

				'cont' => $cont,
				'date' => $ledger->date,
				'description' => mb_strtoupper(__('accounting.movements_day')),
				'correlative' => $ledger->correlative,
				'debit' =>  number_format($ledger->debit, 2),
				'credit' => number_format($ledger->credit, 2),
				'balance' => number_format($balance, 2),
			);

			array_push($lines, $item);
		}

		return DataTables::of($lines)->toJson();
	}

	public function getHigherReport(Request $request) {

		$date_from = Carbon::parse($request->input('from'));
		$date_to = Carbon::parse($request->input('to'));
		$size = $request->input('size');

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->business_full_name);
		$report_type = $request->input('report-type');
		$digits = $business->ledger_digits;

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];
		$month_to = $months[($date_to->format('n')) - 1];		
		$report_name = __('accounting.ledger_report');

		if ($date_from->format('Y') == $date_to->format('Y')) {

			if ($month_from == $month_to) {

				$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');

			} else {

				$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.of') . " " . $month_from . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');

			}

			
		} else {

			$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.of') . " " . $month_from . " " . __('accounting.of') . " " . $date_from->format('Y') . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');
		}

		$ids = $request->input('id');

		$accounts = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->whereIn('id', $ids)			
		->orderBy('code', 'asc')
		->get();

		$accounts_q = collect(DB::select('CALL getLedgerAccounts(?, ?, ?)', [$date_from, $date_to, $business_id]));
		$accounts = $accounts_q->whereIn('id', $ids);

		$details = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'detail.entrie_id', '=', 'entrie.id')
		->join('catalogues as catalogue', 'detail.account_id', '=', 'catalogue.id')
		->select(DB::raw('DATE_FORMAT(entrie.date, "%d/%m/%Y") as date, SUM(detail.debit) debit, SUM(detail.credit) credit, entrie.correlative'), 'entrie.description', 'catalogue.code', 'detail.entrie_id', 'entrie.date as date_raw', DB::raw("SUBSTR(catalogue.code, 1, ". $digits .") as parent"))
		->where('entrie.business_id', $business_id)
		->whereBetween('entrie.date', [$date_from, $date_to])
		->where('entrie.status', 1)
		->groupBy('detail.id')
		->orderBy('entrie.date', 'asc')
		->get();
		
		$entries = DB::table('accounting_entries')
		->select('date')
		->where('business_id', $business_id)
		->whereBetween('date', [$date_from, $date_to])
		->orderBy('date', 'asc')
		->distinct()
		->get();
		

		$items = array();

		foreach ($accounts as $account) {
			
			foreach ($entries as $entrie) {

				$total_debit = 0.00;
				$total_credit = 0.00;

				foreach($details as $detail) {

					if (($entrie->date == $detail->date_raw) && ($account->code == $detail->parent)) {

						$total_debit = $total_debit + $detail->debit;
						$total_credit = $total_credit + $detail->credit;
					}
				}

				if(($total_debit != 0.00) || ($total_credit != 0.00)) {

					$item = array(
						'code' => $account->code,
						'date' => $entrie->date,
						'debit' => $total_debit,
						'credit' => $total_credit
					);
					array_push($items, $item);
				}
			}
		}

		$lines = json_decode(json_encode ($items), FALSE);

		if ($report_type == 'pdf') {

			$pdf = \PDF::loadView('reports.ledgers_pdf', compact('accounts', 'lines', 'report_name', 'date_range', 'business_name', 'size'));
			return $pdf->stream('ledgers.pdf');

		} else {

			return Excel::download(new LedgerReportExport($accounts, $lines, $report_name, $date_range, $business_name), 'Ledger.xlsx');
		}
		
	}

	public function getHigherAccounts() {

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$digits = $business->ledger_digits;

		$accounts = DB::table('catalogues')
		->where('business_id', $business_id)
		->select('id', 'code', 'name')
		->where('status', 1)
		->whereRaw('LENGTH(code) = '.$digits.'')
		->orderBy('code', 'asc')
		->get();
		return response()->json($accounts);
	}

	public function getBalances() {

		if(!auth()->user()->can('balances')) {
			return redirect('home');
		}

		$business_id = request()->session()->get('user.business_id');

		$clasifications = DB::table('catalogues')
		->select('id', 'code', 'name')
		->where('business_id', $business_id)
		->where('status', 1)
		->where('parent', 0)
		->orderBy('code', 'asc')
		->get();

		return view('balances.index', compact('clasifications', 'business_id'));
	}

	public function getIvaBooks()
	{
		if(!auth()->user()->can('balances')) {
			return redirect('home');
		}

		$business_id = request()->session()->get('user.business_id');

		$business_locations = BusinessLocation::select('name', 'id')->where('business_id', $business_id)->get();

		return view('iva_books.index', compact('business_locations'));
	}

	public function getBalanceReport(Request $request) {

		$date = Carbon::parse($request->input('to'));
		$size = $request->input('size_general');
		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->business_full_name);
		$enable_foot_page = !empty($request->input('enable_foot_page')) ? 'active' : 'inactive';

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));			

		$month = $months[($date->format('n')) - 1];

		$date_to = $date->format('d') . ' '.__('accounting.of').' ' . $month . ' '.__('accounting.of').' ' . $date->format('Y');

		$header = " ".__('accounting.balance_report')." ".__('accounting.to_date')." ".$date_to."";

		$accounts_debit = DB::table('catalogues as account')
		->leftJoin('accounting_entries_details as detail', 'detail.account_id', '=', 'account.id')
		->select(
			
			DB::raw("CONCAT(account.code, '%') AS code_query"),
			'account.id',
			'account.name',
			'account.code',
			'account.level',
			DB::raw(

				"(select (SUM(debit) - SUM(credit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = ".$business_id." and accounting_entries.date <= '".$date."') as balance"
			)
		)
		->where('account.business_id', $business_id)
		->where('account.type', 'debtor')
		->where('account.level', '<=', 4)
		->where(DB::raw('substr(account.code, 1, 1)'), '!=' , 4)
		->where(DB::raw('substr(account.code, 1, 1)'), '!=' , 6)
		->where(DB::raw('substr(account.code, 1, 1)'), '!=' , 7)
		->orderBy('account.code', 'asc')
		->groupBy('account.id')
		->get();


		$accounts_credit = DB::table('catalogues as account')
		->leftJoin('accounting_entries_details as detail', 'detail.account_id', '=', 'account.id')
		->select(
			DB::raw("CONCAT(account.code, '%') AS code_query"),
			'account.id',
			'account.name',
			'account.code',
			'account.level',
			DB::raw(

				"(select (SUM(credit) - SUM(debit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = ".$business_id." and accounting_entries.date <= '".$date."') as balance"
			)
		)
		->where('account.business_id', $business_id)
		->where('account.type', 'creditor')
		->where('account.level', '<=', 4)
		->where(DB::raw('substr(account.code, 1, 1)'), '!=' , 5)
		->where(DB::raw('substr(account.code, 1, 1)'), '!=' , 6)
		->where(DB::raw('substr(account.code, 1, 1)'), '!=' , 7)
		->orderBy('account.code', 'asc')
		->groupBy('account.id')
		->get();

		$owner = $business->legal_representative;
		$accountant = $business->accountant;
		$auditor = $business->auditor;

		$report_type = $request->input('report-type-balance');
		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.balance_pdf', compact('header', 'accounts_debit', 'accounts_credit', 'owner', 'accountant', 'auditor', 'business_name', 'enable_foot_page', 'business', 'size'));
			$pdf->setPaper('letter', 'landscape');
			return $pdf->stream();
		} else {
			return Excel::download(new GeneralBalanceExport($header, $accounts_debit, $accounts_credit, $owner, $accountant, $auditor, $business_name, $enable_foot_page, $business), 'General.xlsx');
		}
	}

	public function getBalanceComprobation(Request $request) {

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();


		$account_from = $request->input('account_from');
		$account_to = $request->input('account_to');
		$level = $request->input('level');
		$size = $request->input('size_comprobation');
		$date_from = Carbon::parse($request->input('comprobation_from'));
		$date_to = Carbon::parse($request->input('comprobation_to'));
		$business_name = mb_strtoupper($business->business_full_name);
		$enable_empty_values = !empty($request->input('enable_empty_values')) ? 'active' : 'inactive';


		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];		
		$month_to = $months[($date_to->format('n')) - 1];		

		$report_name = __('accounting.comprobation_balance');
		if ($date_from->format('Y') == $date_to->format('Y')) {

			if ($month_from == $month_to) {

				$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');

			} else {

				$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.of') . " " . $month_from . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');

			}

			
		} else {

			$date_range = __('accounting.from_date') . "  " . $date_from->format('d') . " " . __('accounting.of') . " " . $month_from . " " . __('accounting.of') . " " . $date_from->format('Y') . " " . __('accounting.to_date') . " " . $date_to->format('d') . " " . __('accounting.of') . " " . $month_to . " " . __('accounting.of') . " " . $date_to->format('Y');
		}

		$accounts_debit = collect(DB::select('CALL getDebitAccounts(?, ?, ?, ?)', [$date_from, $date_to, $level, $business_id]));
		$accounts_credit = collect(DB::select('CALL getCreditAccounts(?, ?, ?, ?)', [$date_from, $date_to, $level, $business_id]));
		$report_type = $request->input('report-type');

		if ($report_type == 'pdf') {

			$pdf = \PDF::loadView('reports.balance_comprobation_pdf', compact('report_name', 'date_range', 'accounts_debit', 'accounts_credit', 'account_from', 'account_to', 'business_name', 'enable_empty_values', 'size'));
			$pdf->setPaper('letter', 'landscape');
			return $pdf->stream();

		} else {

			return Excel::download(new ComprobationBalanceExport($report_name, $date_range, $accounts_debit, $accounts_credit, $account_from, $account_to, $business_name, $enable_empty_values), 'Balance.xlsx');
		}
	}

	public function getIvaBooksReport(Request $request)
	{

		ini_set('memory_limit', '5G');
		$type = $request->input('type');
		$business_location = $request->input('business-location-id');
		$date_from = Carbon::parse($request->input('from'))->startOfDay();
		$date_to = Carbon::parse($request->input('to'))->endOfDay();
		$type_format = $request->input('type-format');
		$size = $request->input('size');

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];
		$from_date = $date_from->format('d') .' '.__('accounting.of').' '. $month_from .' '.__('accounting.of').' '. $date_from->format('Y');

		$month_to = $months[($date_to->format('n')) - 1];
		$to_date = $date_to->format('d') .' '.__('accounting.of').' '. $month_to .' '.__('accounting.of').' '. $date_to->format('Y');

		$month = $months[($date_to->format('n')) - 1];
		$year = $date_to->format('Y');

		$header_date = "".__('accounting.from_date')." ".$from_date." ".__('accounting.to_date')." ".$to_date;

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();

		$transactions = DB::table('transactions as transaction')
		->where('transaction_date', '>=', $date_from)
		->where('transaction_date', '<=', $date_to);

		if($business_location != 0) {
			$transactions->where('location_id', $business_location);
		}

		if($type == 'sells') {

			$header = __('accounting.header_sells');

			$transactions->leftJoin('customers as customer', 'customer.id', '=', 'transaction.customer_id')
			->where('transaction.type', 'sell')
			->where('transaction.document_types_id', 1);

			$lines_q = $transactions->select(DB::raw('DATE_FORMAT(transaction.transaction_date, "%Y-%m-%d") as transaction_date'), DB::raw('DATE_FORMAT(transaction.transaction_date, "%d/%m/%Y") as date_transaction'), DB::raw('SUM(transaction.total_before_tax) as total_before_tax, SUM(transaction.tax_amount) as tax_amount, SUM(final_total) as final_total'))
			->groupBy('date_transaction')
			->get();

			$lines_array = array();

			foreach ($lines_q as $line) {

				//$tax_amount = $this->taxUtil->getTaxAmount($line->id, 'sell');

				$start_correlative  = DB::table('transactions')
				->select(DB::raw('MIN(CONVERT(correlative, SIGNED)) as start'))
				->whereDate('transaction_date', $line->transaction_date)
				->first();

				$end_correlative  = DB::table('transactions')
				->select(DB::raw('MAX(CONVERT(correlative, SIGNED)) as end'))
				->whereDate('transaction_date', $line->transaction_date)
				->first();

				$item = array(
					'transaction_date' => $line->transaction_date,
					'date_transaction' => $line->date_transaction,
					//'exempt_amount' => $line->exempt_amount,
					'total_before_tax' => $line->total_before_tax,
					'tax_amount' => $line->tax_amount,
					//'perc_ret_amount' => $line->perc_ret_amount,
					'final_total' => $line->final_total,
					'start' => $start_correlative->start,
					'end' => $end_correlative->end,
				);

				array_push($lines_array, $item);
			}
			$lines = json_decode(json_encode ($lines_array), FALSE);
			
		}

		if($type == 'sells_taxpayer') {

			$header = __('accounting.header_sells_taxpayer');

			$transactions->leftJoin('customers as customer', 'customer.id', '=', 'transaction.customer_id')
			->where('transaction.type', 'sell')
			->where('transaction.document_types_id', 2);
			

			$lines_q = $transactions->select(DB::raw('DATE_FORMAT(transaction.transaction_date, "%Y-%m-%d") as date'), 'transaction.status', 'transaction.transaction_date', 'transaction.id', 'transaction.correlative as document', 'customer.reg_number as nrc', 'customer.name as customer_name', /*'transaction.exempt_amount',*/ 'transaction.total_before_tax', 'transaction.tax_amount', /*'transaction.perc_ret_amount',*/ 'transaction.final_total')
			->orderBy('date', 'ASC')
			->orderBy('document', 'ASC')
			->get();

			$lines_array = array();

			foreach ($lines_q as $line) {

				$tax_amount = $this->taxUtil->getTaxAmount($line->id, 'sell');

				$item = array(
					'transaction_date' => $line->transaction_date,
					'document' => $line->document,
					'nrc' => $line->nrc,
					'customer_name' => $line->customer_name,
					//'exempt_amount' => $line->exempt_amount,
					'total_before_tax' => $line->total_before_tax,
					'tax_amount' => $tax_amount,
					//'perc_ret_amount' => $line->perc_ret_amount,
					'final_total' => $line->final_total,
					'status' => $line->status,
				);

				array_push($lines_array, $item);
			}
			$lines = json_decode(json_encode ($lines_array), FALSE);
		}

		if($type == 'sells_exports') {

			$header = __('accounting.header_sells_exports');

			$transactions->leftJoin('customers as customer', 'customer.id', '=', 'transaction.customer_id')
			->where('transaction.type', 'sell')
			->where('transaction.document_types_id', 3);
			

			$lines_q = $transactions->select(DB::raw('DATE_FORMAT(transaction.transaction_date, "%Y-%m-%d") as date'), 'transaction.status', 'transaction.transaction_date', 'transaction.id', 'transaction.correlative as document', 'customer.reg_number as nrc', 'customer.tax_number as nit', 'customer.name as customer_name', /*'transaction.exempt_amount',*/ 'transaction.total_before_tax', 'transaction.tax_amount', /*'transaction.perc_ret_amount',*/ 'transaction.final_total')
			->orderBy('date', 'ASC')
			->orderBy('document', 'ASC')
			->get();

			$lines_array = array();

			foreach ($lines_q as $line) {

				//$tax_amount = $this->taxUtil->getTaxAmount($line->id, 'sell');

				$item = array(
					'transaction_date' => $line->transaction_date,
					'document' => $line->document,
					'nrc' => $line->nrc,
					'nit' => $line->nit,
					'customer_name' => $line->customer_name,
					//'exempt_amount' => $line->exempt_amount,
					'total_before_tax' => $line->total_before_tax,
					//'tax_amount' => $tax_amount,
					//'perc_ret_amount' => $line->perc_ret_amount,
					'final_total' => $line->final_total,
					'status' => $line->status,
				);

				array_push($lines_array, $item);
			}
			$lines = json_decode(json_encode ($lines_array), FALSE);


		}

		if($type == 'purchases') {

			$header = __('accounting.header_purchases');

			$transactions->leftJoin('contacts as customer', 'customer.id', '=', 'transaction.contact_id')
			->where('transaction.type', 'expense')
			->orWhere('transaction.type', 'purchase')
			->groupBy('transaction.id');			

			$lines_q = $transactions->select('transaction.id', 'transaction.transaction_date', 'transaction.ref_no as document', 'customer.tax_number as nrc', 'customer.name as customer_name', /*'transaction.exempt_amount',*/ 'transaction.total_before_tax', 'transaction.tax_amount', /*'transaction.perc_ret_amount',*/ 'transaction.final_total')
			->orderBy('transaction.transaction_date')
			->orderBy('transaction.document')
			->get();

			$lines_array = array();

			foreach ($lines_q as $line) {

				$tax_amount = $this->taxUtil->getTaxAmount($line->id, 'purchase');

				$item = array(
					'transaction_date' => $line->transaction_date,
					'document' => $line->document,
					'nrc' => $line->nrc,
					'customer_name' => $line->customer_name,
					//'exempt_amount' => $line->exempt_amount,
					'total_before_tax' => $line->total_before_tax,
					'tax_amount' => $line->tax_amount,
					//'perc_ret_amount' => $line->perc_ret_amount,
					'final_total' => $line->final_total,
				);

				array_push($lines_array, $item);
			}
			$lines = json_decode(json_encode ($lines_array), FALSE);

		}

		
		
		if ($type_format == 'pdf') {
			
			$pdf = \PDF::loadView('reports.iva_pdf', compact('size', 'header', 'header_date', 'business', 'month', 'year', 'type', 'lines'));

			if($type == 'sells') {
				$pdf->setPaper('letter', 'portrait');
			} else {
				$pdf->setPaper('letter', 'landscape');
			}
			
			return $pdf->stream();
			
		} else {
			return Excel::download(new IvaBookExport($size, $header, $header_date, $business, $month, $year, $type, $lines), 'IVA.xlsx');
		}
	}

	public function getSignatures($id)
	{
		$business = Business::select('legal_representative', 'accountant', 'auditor', 'inscription_number_auditor')->where('id', $id)->first();
		return response()->json($business);
	}
	public function setSignatures(Request $request)
	{
		$validateData = $request->validate(
			[
				'legal_representative' => 'required',
				'accountant' => 'required',
				'auditor' => 'required',
				'inscription_number_auditor' => 'required',
			],
			[
				'legal_representative.required' => __('accounting.legal_representative_required'),
				'accountant.required' => __('accounting.accountant_required'),
				'auditor.required' => __('accounting.auditor_required'),
				'inscription_number_auditor.required' => __('accounting.inscription_number_auditor_required'),
			]
		);
		if($request->ajax())
		{
			$id = $request->input('business_id');
			$business = Business::findOrFail($id);
			try {
				$business->legal_representative = $request->input('legal_representative');
				$business->accountant = $request->input('accountant');
				$business->auditor = $request->input('auditor');
				$business->inscription_number_auditor = $request->input('inscription_number_auditor');
				$business->save();
				$output = [
					'success' => true,
					'msg' => __('accounting.updated_successfully')
				];

			} catch(\Exception $e){
				\Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
				$output = [
					'success' => false,
					'msg' => __("messages.something_went_wrong")
				];
			}
			return $output;
		}
	}

	public function getBankTransactions(Request $request) {

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->name);
		
		$date_from = Carbon::parse($request->input('report_from'));
		$date_to = Carbon::parse($request->input('report_to'));
		$size = $request->input('size');


		$report_account = $request->input('report_account');
		$report_type = $request->input('report_type');
		$report_checkbook = $request->input('report_checkbook');
		$report_format = $request->input('report_format');


		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];
		$from_date = $date_from->format('d') .' '.__('accounting.of').' '. $month_from .' '.__('accounting.of').' '. $date_from->format('Y');

		$month_to = $months[($date_to->format('n')) - 1];
		$to_date = $date_to->format('d') .' '.__('accounting.of').' '. $month_to .' '.__('accounting.of').' '. $date_to->format('Y');

		$header1 = __('accounting.report_bank_transactions');
		$header2 = "".__('accounting.from_date')." ".$from_date." ".__('accounting.to_date')." ".$to_date;

		if($report_type != 0) {

			$type_transaction_q = TypeBankTransaction::findOrFail($report_type);

			if ($type_transaction_q->type == 'debit') {

				$type_transaction = 'inflow';

			} else {
				
				$type_transaction = 'outflow';
			}

		} else {

			$type_transaction = 'all';
		}


		$transactions_debit_q = DB::table('bank_transactions as transaction')
		->join('bank_accounts as bank_account', 'bank_account.id', '=', 'transaction.bank_account_id')
		->join('type_bank_transactions as type', 'type.id', '=', 'transaction.type_bank_transaction_id')
		->select(DB::raw('DATE_FORMAT(transaction.date, "%d/%m/%Y") as date_transaction'), 'transaction.*', 'bank_account.name as bank', 'bank_account.number as number_account', 'type.name as type_transaction')
		->where('transaction.business_id', $business_id)
		->where('transaction.date', '>=', $date_from)
		->where('transaction.date', '<=', $date_to)
		->where('type.type', 'debit')
		->where('transaction.status', 1)
		->orderBy('transaction.date', 'asc');
		

		$transactions_credit_q = DB::table('bank_transactions as transaction')
		->join('bank_accounts as bank_account', 'bank_account.id', '=', 'transaction.bank_account_id')
		->join('type_bank_transactions as type', 'type.id', '=', 'transaction.type_bank_transaction_id')
		->select(DB::raw('DATE_FORMAT(transaction.date, "%d/%m/%Y") as date_transaction'), 'transaction.*', 'bank_account.name as bank', 'bank_account.number as number_account', 'type.name as type_transaction')
		->where('transaction.business_id', $business_id)
		->where('transaction.date', '>=', $date_from)
		->where('transaction.date', '<=', $date_to)
		->where('type.type', 'credit')
		->where('transaction.status', 1)
		->orderBy('transaction.date', 'asc');
		

		if ($report_account != 0) {
			$transactions_debit_q->where('bank_account.id', $report_account);
			$transactions_credit_q->where('bank_account.id', $report_account);
		}

		if ($report_type != 0) {
			$transactions_debit_q->where('type.id', $report_type);
			$transactions_credit_q->where('type.id', $report_type);
		}

		if ($report_checkbook != 0) {
			$transactions_debit_q->where('transaction.bank_checkbook_id', $report_checkbook);
			$transactions_credit_q->where('transaction.bank_checkbook_id', $report_checkbook);
		}

		$transactions_debit = $transactions_debit_q->get();
		$transactions_credit = $transactions_credit_q->get();

		
		if ($report_format == 'pdf') {
			$pdf = \PDF::loadView('reports.bank_transactions_pdf', compact('header1', 'header2', 'transactions_debit', 'transactions_credit', 'business_name', 'type_transaction', 'size'));
			$pdf->setPaper('letter', 'landscape');
			return $pdf->stream();
		} else {
			return Excel::download(new BankTransactionsReportExport($header1, $header2, $transactions_debit, $transactions_credit, $business_name, $type_transaction), 'Banks.xlsx');
		}
	}

	public function getResultStatus(Request $request) {
		
		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();
		$business_name = mb_strtoupper($business->business_full_name);
		
		$date_from = Carbon::parse($request->input('from_result'));
		$date_to = Carbon::parse($request->input('to_result'));
		$date_initial_year = Carbon::parse($request->input('from_result'))->startOfYear();
		$size = $request->input('size_result');


		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];
		$from_date = $date_from->format('d') .' '.__('accounting.of').' '. $month_from .' ';

		$month_to = $months[($date_to->format('n')) - 1];
		$to_date = $date_to->format('d') .' '.__('accounting.of').' '. $month_to .' '.__('accounting.of').' '. $date_to->format('Y');

		
		$header = "".__('accounting.from_date')." ".$from_date." ".__('accounting.to_date')." ".$to_date;
		$report_format = $request->input('type_result');

		
		$accounting_ordinary_incomes_id = Catalogue::where('id', $business->accounting_ordinary_incomes_id)->first();
		$accounting_return_sells_id = Catalogue::where('id', $business->accounting_return_sells_id)->first();
		$accounting_sells_cost_id = Catalogue::where('id', $business->accounting_sells_cost_id)->first();
		$accounting_ordinary_expenses_id = Catalogue::where('id', $business->accounting_ordinary_expenses_id)->first();
		$accounting_extra_incomes_id = Catalogue::where('id', $business->accounting_extra_incomes_id)->first();
		$accounting_extra_expenses_id = Catalogue::where('id', $business->accounting_extra_expenses_id)->first();

		$level_childrens_ordynary_incomes = $business->level_childrens_ordynary_incomes;
		$level_childrens_ordynary_expenses = $business->level_childrens_ordynary_expenses;
		$level_childrens_extra_incomes = $business->level_childrens_extra_incomes;
		$level_childrens_extra_expenses = $business->level_childrens_extra_expenses;
		


		$ordinary_income_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'entrie.id', '=', 'detail.entrie_id')
		->join('catalogues as catalogue', 'catalogue.id', '=', 'detail.account_id')
		->select(DB::raw('(SUM(credit) - SUM(debit)) as balance'))
		->where('entrie.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_ordinary_incomes_id->code.'%')
		->where('entrie.status', 1)
		->where('entrie.date', '>=', $date_initial_year)
		->where('entrie.date', '<=', $date_to)
		->first();

		$return_sells_q = DB::table('catalogues as catalogue')
		->select(
			'catalogue.id',
			'catalogue.name',
			'catalogue.code as code_query',
			DB::raw(

				'(select (SUM(credit) - SUM(debit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = '.$business_id.' and accounting_entries.date between "'.$date_initial_year.'" and "'.$date_to.'") as balance'
			)
		)
		->where('catalogue.business_id', $business_id)
		->where('catalogue.id', $accounting_return_sells_id->id)
		->first();

		$ordinary_income = $ordinary_income_q->balance - $return_sells_q->balance;
		$return_sells = $return_sells_q->balance;

		$ordinary_income_accounts = DB::table('catalogues as catalogue')
		->select(
			'catalogue.id',
			'catalogue.name',
			DB::raw('CONCAT(catalogue.code, "%") AS code_query'),
			DB::raw(
				
				'(select (SUM(credit) - SUM(debit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = '.$business_id.' and accounting_entries.date between "'.$date_initial_year.'" and "'.$date_to.'") as balance'
			)
		)
		->where('catalogue.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_ordinary_incomes_id->code.'%')
		->where('catalogue.level', $level_childrens_ordynary_incomes)
		->get();

		$sell_cost_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'entrie.id', '=', 'detail.entrie_id')
		->join('catalogues as catalogue', 'catalogue.id', '=', 'detail.account_id')
		->select(DB::raw('(SUM(debit) - SUM(credit)) as balance'))
		->where('entrie.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_sells_cost_id->code.'%')
		->where('entrie.status', 1)
		->where('entrie.date', '>=', $date_initial_year)
		->where('entrie.date', '<=', $date_to)
		->first();

		$ordinary_expense_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'entrie.id', '=', 'detail.entrie_id')
		->join('catalogues as catalogue', 'catalogue.id', '=', 'detail.account_id')
		->select(DB::raw('(SUM(debit) - SUM(credit)) as balance'))
		->where('entrie.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_ordinary_expenses_id->code.'%')
		->where('entrie.status', 1)
		->where('entrie.date', '>=', $date_initial_year)
		->where('entrie.date', '<=', $date_to)
		->first();

		$ordinary_expense = $ordinary_expense_q->balance;

		$ordinary_expense_accounts = DB::table('catalogues as catalogue')
		->select(
			'catalogue.id',
			'catalogue.name',
			DB::raw('CONCAT(catalogue.code, "%") AS code_query'),
			DB::raw(
				'(select (SUM(debit) - SUM(credit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = '.$business_id.' and accounting_entries.date between "'.$date_initial_year.'" and "'.$date_to.'") as balance'
			)
		)
		->where('catalogue.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_ordinary_expenses_id->code.'%')
		->where('catalogue.level', $level_childrens_ordynary_expenses)
		->where('catalogue.id', '<>', $accounting_sells_cost_id->id)
		->get();

		$extra_income_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'entrie.id', '=', 'detail.entrie_id')
		->join('catalogues as catalogue', 'catalogue.id', '=', 'detail.account_id')
		->select(DB::raw('(SUM(credit) - SUM(debit)) as balance'))
		->where('entrie.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_extra_incomes_id->code.'%')
		->where('entrie.status', 1)
		->where('entrie.date', '>=', $date_initial_year)
		->where('entrie.date', '<=', $date_to)
		->first();

		$extra_income = $extra_income_q->balance;

		$extra_income_accounts = DB::table('catalogues as catalogue')
		->select(
			'catalogue.id',
			'catalogue.name',
			DB::raw('CONCAT(catalogue.code, "%") AS code_query'),
			DB::raw(
				
				'(select (SUM(credit) - SUM(debit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = '.$business_id.' and accounting_entries.date between "'.$date_initial_year.'" and "'.$date_to.'") as balance'
			)
		)
		->where('catalogue.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_extra_incomes_id ->code.'%')
		->where('catalogue.level', $level_childrens_extra_incomes)
		->get();

		$extra_expense_q = DB::table('accounting_entries_details as detail')
		->join('accounting_entries as entrie', 'entrie.id', '=', 'detail.entrie_id')
		->join('catalogues as catalogue', 'catalogue.id', '=', 'detail.account_id')
		->select(DB::raw('(SUM(debit) - SUM(credit)) as balance'))
		->where('entrie.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_extra_expenses_id->code.'%')
		->where('entrie.status', 1)
		->where('entrie.date', '>=', $date_initial_year)
		->where('entrie.date', '<=', $date_to)
		->first();

		$extra_expense = $extra_expense_q->balance;

		$extra_expense_accounts = DB::table('catalogues as catalogue')
		->select(
			'catalogue.id',
			'catalogue.name',
			DB::raw('CONCAT(catalogue.code, "%") AS code_query'),
			DB::raw(

				'(select (SUM(debit) - SUM(credit)) from accounting_entries_details inner join catalogues on accounting_entries_details.account_id = catalogues.id inner join accounting_entries on accounting_entries_details.entrie_id = accounting_entries.id where catalogues.code like code_query and accounting_entries.status = 1 and accounting_entries.business_id = '.$business_id.' and accounting_entries.date between "'.$date_initial_year.'" and "'.$date_to.'") as balance'
			)
		)
		->where('catalogue.business_id', $business_id)
		->where('catalogue.code', 'like', ''.$accounting_extra_expenses_id->code.'%')
		->where('catalogue.level', $level_childrens_extra_expenses)
		->get();
		
		if ($report_format == 'pdf') {
			$pdf = \PDF::loadView('reports.result_status_pdf', compact('header', 'business', 'business_name', 'ordinary_income', 'return_sells_q', 'ordinary_income_accounts', 'sell_cost_q', 'ordinary_expense', 'ordinary_expense_accounts', 'extra_income', 'extra_income_accounts', 'extra_expense', 'extra_expense_accounts', 'size'));
			$pdf->setPaper('letter', 'portrait');
			return $pdf->stream();
		}
		else {
			return Excel::download(new ResultStatusExport($header, $business, $ordinary_income, $return_sells_q, $ordinary_income_accounts, $sell_cost_q, $ordinary_expense, $ordinary_expense_accounts, $extra_income, $extra_income_accounts, $extra_expense, $extra_expense_accounts), 'Result.xlsx');
		}
	}

	public function printPOS() {		

		try
		{

			$connector = new DummyPrintConnector();
			$printer = new Printer($connector);
			$printer ->text("Hello, direct Print From Javascript\n");			
			$printer->feed(3);
			$printer->cut();
			$data = $connector->getData();
			$base64data = base64_encode($data);
			$printer->close();

			$output = [
				'success' => true,
				'msg' => 'success',
				'data' => $base64data
			];

		}
		catch(Exception $e)
		{
			$output = [
				'success' => true,
				'msg' => 'Failed'
			];
		}
		return $output;
	}

	public function getKardex() {

		$business_id = request()->session()->get('user.business_id');

		$products = DB::table('variations as variation')
		->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
		->select('product.name as name_product', 'variation.name as name_variation', 'variation.id', 'variation.sub_sku', 'product.sku', 'product.type as product_type')
		->where('business_id', $business_id)
		->where('product.clasification', '=', 'product')
		->where('product.status', 'active')
		->get();

		$warehouses = DB::table('warehouses as warehouse')
		->select('warehouse.*')
		->where('business_id', $business_id)
		->where('status', 'active')
		->get();

		return view('kardex.old_index', compact('products', 'warehouses'));
	}

	public function getKardexReport(Request $request) {

		ini_set('memory_limit', '5G');

		$date_from = Carbon::parse($request->input('from'))->startOfDay();
		$date_to = Carbon::parse($request->input('to'))->endOfDay();
		$type_format = $request->input('type-format');
		$variation_id = $request->input('products');
		$warehouse_id = $request->input('warehouses');
		$size = $request->input('size');

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));

		$month_from = $months[($date_from->format('n')) - 1];
		$from_date = $date_from->format('d') .' '.__('accounting.of').' '. $month_from .' '.__('accounting.of').' '. $date_from->format('Y');

		$month_to = $months[($date_to->format('n')) - 1];
		$to_date = $date_to->format('d') .' '.__('accounting.of').' '. $month_to .' '.__('accounting.of').' '. $date_to->format('Y');

		$header_date = "".__('accounting.from_date')." ".$from_date." ".__('accounting.to_date')." ".$to_date;

		$business_id = request()->session()->get('user.business_id');
		$business = Business::where('id', $business_id)->first();

		$variation_q = DB::table('variations as variation')
		->leftJoin('products as product', 'product.id', '=', 'variation.product_id')
		->select('product.name as product_name', 'product.type as product_type', 'variation.name as variation_name')
		->where('variation.id', $variation_id)
		->first();

		if($variation_q) {
			if ($variation_q->product_type == 'single') {
				$product = $variation_q->product_name;
			} else {
				$product = $variation_q->product_name.' '.$variation_q->variation_name;
			}
		} else {
			$product = "N/A";
		}

		$warehouse_q = DB::table('warehouses as warehouse')
		->select('warehouse.*')
		->where('id', $warehouse_id)
		->first();

		if($warehouse_q) {
			$warehouse = $warehouse_q->name;
		} else {
			$warehouse = __('kardex.all');
		}

		$inputs_q = DB::table('purchase_lines as purchase')
		->leftJoin('transactions as transaction', 'transaction.id', '=', 'purchase.transaction_id')
		->select(DB::raw('SUM(purchase.quantity) as total_quantity, SUM(purchase.purchase_price) as total_purchase'))
		->where('transaction.transaction_date', '<', $date_from)
		->whereIn('transaction.type', ['opening_stock', 'purchase', 'stock_adjustment'])
		->where('purchase.variation_id', $variation_id)
		->where('transaction.status', '!=', 'annulled');

		$outputs_q = DB::table('transaction_sell_lines as sell')
		->leftJoin('transactions as transaction', 'transaction.id', '=', 'sell.transaction_id')
		->select(DB::raw('SUM(sell.quantity) as total_quantity'))
		->where('transaction.transaction_date', '<', $date_from)
		->whereIn('transaction.type', ['sell', 'stock_adjustment'])
		->where('sell.variation_id', $variation_id)
		->where('transaction.status', '!=', 'annulled');

		if($warehouse_id != 0) {
			$inputs_q->where('transaction.warehouse_id', $warehouse_id);
			$outputs_q->where('transaction.warehouse_id', $warehouse_id);
		}

		$inputs_q = $inputs_q->first();
		$outputs_q = $outputs_q->first();

		$initial_quantity = $inputs_q->total_quantity - $outputs_q->total_quantity;
		if ($initial_quantity != 0) {
			$initial_cost = ($inputs_q->total_purchase * $inputs_q->total_quantity) / $initial_quantity;
		} else {
			$initial_cost = 0.00;
		}
		

		$purchases_q = DB::table('purchase_lines as purchase')
		->leftJoin('transactions as transaction', 'transaction.id', '=', 'purchase.transaction_id')
		->select('transaction.transaction_date as date', 'transaction.type as type', 'transaction.ref_no as document', 'transaction.status as status', 'purchase.quantity', 'purchase.purchase_price')
		->where('transaction.transaction_date', '>=', $date_from)
		->where('transaction.transaction_date', '<=', $date_to)
		->whereIn('transaction.type', ['opening_stock', 'purchase', 'purchase_return', 'purchase_transfer', 'stock_adjustment'])
		->where('purchase.variation_id', $variation_id);

		$sells_q = DB::table('transaction_sell_lines as sell')
		->leftJoin('transactions as transaction', 'transaction.id', '=', 'sell.transaction_id')
		->select('transaction.transaction_date as date', 'transaction.type as type', 'transaction.correlative as document', 'transaction.status as status', 'sell.quantity')
		->where('transaction.transaction_date', '>=', $date_from)
		->where('transaction.transaction_date', '<=', $date_to)
		->whereIn('transaction.type', ['sell', 'sell_return', 'sell_transfer', 'stock_adjustment'])
		->where('sell.variation_id', $variation_id);

		$kits_q = DB::table('transaction_kit_sell_lines as sell')
		->leftJoin('transactions as transaction', 'transaction.id', '=', 'sell.transaction_id')
		->select('transaction.transaction_date as date', 'transaction.type as type', 'transaction.correlative as document', 'transaction.status as status', 'sell.quantity')
		->where('transaction.transaction_date', '>=', $date_from)
		->where('transaction.transaction_date', '<=', $date_to)
		->whereIn('transaction.type', ['sell', 'sell_return', 'sell_transfer', 'stock_adjustment'])
		->where('sell.variation_id', $variation_id);

		if($warehouse_id != 0) {
			$purchases_q->where('transaction.warehouse_id', $warehouse_id);
			$sells_q->where('transaction.warehouse_id', $warehouse_id);
			$kits_q->where('transaction.warehouse_id', $warehouse_id);
		}

		$purchases_q = $purchases_q->get();
		$sells_q = $sells_q->get();
		$kits_q = $kits_q->get();

		$lines_array = array();
		foreach ($purchases_q as $item) {
			if($item->type == 'stock_adjustment') {
				$type = 'ADJUSTMENT_IN';
			} else {
				$type = $item->type;
			}
			$line = array(
				'datetime' => $item->date,
				'date' => Carbon::createFromFormat('Y-m-d H:i:s', $item->date)->format('Y-m-d'),
				'type' => $type,
				'status' => $item->status,
				'document' => $item->document,
				'quantity' => $item->quantity,
				'total' => $item->purchase_price * $item->quantity,
			);
			array_push($lines_array, $line);
		}

		foreach ($sells_q as $item) {
			if($item->type == 'stock_adjustment') {
				$type = 'ADJUSTMENT_OUT';
			} else {
				$type = $item->type;
			}
			$line = array(
				'datetime' => $item->date,
				'date' => Carbon::createFromFormat('Y-m-d H:i:s', $item->date)->format('Y-m-d'),
				'type' => $type,
				'status' => $item->status,
				'document' => $item->document,
				'quantity' => $item->quantity,
				'total' => 0.00,
			);
			array_push($lines_array, $line);
		}

		foreach ($kits_q as $item) {
			$line = array(
				'datetime' => $item->date,
				'date' => Carbon::createFromFormat('Y-m-d H:i:s', $item->date)->format('Y-m-d'),
				'type' => 'kit_out',
				'status' => $item->status,
				'document' => $item->document,
				'quantity' => $item->quantity,
				'total' => 0.00,
			);
			array_push($lines_array, $line);
		}	

		if(!empty($lines_array))
		{
			foreach ($lines_array as $key => $row) {
				$date[$key]  = $row['datetime'];
			}
			array_multisort($date, SORT_ASC, $lines_array);
		}

		$lines = json_decode(json_encode ($lines_array), FALSE);
		
		if ($type_format == 'pdf') {
			
			$pdf = \PDF::loadView('reports.kardex_pdf', compact('warehouse', 'initial_quantity', 'initial_cost', 'lines', 'product', 'size', 'header_date', 'business'));
			$pdf->setPaper('letter', 'landscape');
			
			return $pdf->stream();
			
		} else {
			return Excel::download(new KardexExport($warehouse, $initial_quantity, $initial_cost, $lines, $product, $size, $header_date, $business), 'kardex.xlsx');
			
		}
	}

	public function runData() {

		$kits_selled = DB::table('transaction_sell_lines as sell')
		->join('transactions as transaction', 'transaction.id', '=', 'sell.transaction_id')
		->select('transaction.id as transaction_id', 'sell.product_id', 'sell.quantity as quantity')
		->whereIn('sell.product_id', [DB::raw("select DISTINCT(parent_id) from kit_has_products")])
		->get();

		foreach ($kits_selled as $item) {


			$childrens = KitHasProduct::where('parent_id', $item->product_id)->get();

			foreach ($childrens as $child) {

				$transaction_kit_sell_line_details['transaction_id'] = $item->transaction_id;
				$transaction_kit_sell_line_details['variation_id'] = $child->children_id;
				$transaction_kit_sell_line_details['quantity'] = $child->quantity * $item->quantity;

				$transactionKitSellLine = TransactionKitSellLine::create($transaction_kit_sell_line_details);

			}
		}

		return 'success';
	}

	/**
	 * View sales book to final consumer index.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function viewBookFinalConsumer()
	{
		if(!auth()->user()->can('iva_book.book_final_consumer')) {
			abort(403, "Unauthorized action.");
		}

		$business_id = request()->session()->get('user.business_id');

		$locations = BusinessLocation::forDropdown($business_id);

		return view('book_final_consumer.index', compact('locations'));
	}

	/**
	 * Get sales book to final consumer report.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function getBookFinalConsumer(Request $request)
	{
		if(!auth()->user()->can('iva_book.book_final_consumer')) {
			abort(403, "Unauthorized action.");
		}

		// Data form
		$business_id = request()->session()->get('user.business_id');
		$initial_date = Carbon::parse($request->input('initial_date'));
		$final_date = Carbon::parse($request->input('final_date'));
		$location = $request->input('location');

		$size = $request->input('size');

		// Query
		$lines = DB::select(
			'CALL get_sales_book_to_final_consumer(?, ?, ?, ?)',
			array($initial_date, $final_date, $location, $business_id)
		);

		// Header info
		$business = Business::where('id', $business_id)->first();

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));
		$initial_month = $months[($initial_date->format('n')) - 1];
		$final_month = $months[($final_date->format('n')) - 1];
		$initial_year = $initial_date->format('Y');
		$final_year = $final_date->format('Y');

		$report_type = $request->input('report_type');
		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.book_final_consumer_pdf',
				compact('lines', 'size', 'business', 'initial_month', 'final_month', 'initial_year', 'final_year'));
			$pdf->setPaper('letter', 'landscape');
			return $pdf->stream('book_final_consumer.pdf');
		} else {
			return Excel::download(new BookFinalConsumerExport($lines, $business, $initial_month, $final_month, $initial_year, $final_year), 'book_final_consumer.xlsx');
		}
	}

	/**
	 * View sales book to taxpayer index.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function viewBookTaxpayer()
	{
		if(!auth()->user()->can('iva_book.book_taxpayer')) {
			abort(403, "Unauthorized action.");
		}

		$business_id = request()->session()->get('user.business_id');

		$locations = BusinessLocation::forDropdown($business_id);

		return view('book_taxpayer.index', compact('locations'));
	}

	/**
	 * Get sales book to taxpayer report.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function getBookTaxpayer(Request $request)
	{
		if(!auth()->user()->can('iva_book.book_taxpayer')) {
			abort(403, "Unauthorized action.");
		}

		// Data form
		$business_id = request()->session()->get('user.business_id');
		$initial_date = Carbon::parse($request->input('initial_date'));
		$final_date = Carbon::parse($request->input('final_date'));
		$location = $request->input('location');

		$size = $request->input('size');

		// Query
		$lines = DB::select(
			'CALL get_sales_book_to_taxpayer(?, ?, ?, ?)',
			array($initial_date, $final_date, $location, $business_id)
		);

		// Header info
		$business = Business::where('id', $business_id)->first();

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));
		$initial_month = $months[($initial_date->format('n')) - 1];
		$final_month = $months[($final_date->format('n')) - 1];
		$initial_year = $initial_date->format('Y');
		$final_year = $final_date->format('Y');

		$report_type = $request->input('report_type');
		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.book_taxpayer_pdf',
				compact('lines', 'size', 'business', 'initial_month', 'final_month', 'initial_year', 'final_year'));
			$pdf->setPaper('A3', 'landscape');
			return $pdf->stream('book_taxpayer.pdf');
		} else {
			return Excel::download(new BookTaxpayerExport($lines, $business, $initial_month, $final_month, $initial_year, $final_year), 'book_taxpayer.xlsx');
		}
	}

	/**
	 * View purchases book index.
	 * 
	 * @return \Illuminate\Http\Response
	 */
	public function viewPurchasesBook()
	{
		if(!auth()->user()->can('iva_book.purchases_book')) {
			abort(403, "Unauthorized action.");
		}

		return view('purchases_book.index');
	}

	/**
	 * Get purchases book report.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function getPurchasesBook(Request $request)
	{
		if(!auth()->user()->can('iva_book.purchases_book')) {
			abort(403, "Unauthorized action.");
		}

		// Data form
		$business_id = request()->session()->get('user.business_id');
		$initial_date = $this->transactionUtil->uf_date($request->input('initial_date'));
		$final_date = $this->transactionUtil->uf_date($request->input('final_date'));

		$size = $request->input('size');

		// Query
		$lines = DB::select(
			'CALL get_purchases_book(?, ?, ?)',
			array($initial_date, $final_date, $business_id)
		);

		// Header info
		$business = Business::where('id', $business_id)->first();

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));
		
		$initial_date = Carbon::parse($initial_date);
		$final_date = Carbon::parse($final_date);

		$initial_month = $months[($initial_date->format('n')) - 1];
		$final_month = $months[($final_date->format('n')) - 1];
		$initial_year = $initial_date->format('Y');
		$final_year = $final_date->format('Y');

		$report_type = $request->input('report_type');
		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView('reports.purchases_book_pdf',
				compact('lines', 'size', 'business', 'initial_month', 'final_month', 'initial_year', 'final_year'));
			$pdf->setPaper('A3', 'landscape');
			return $pdf->stream('purchases_book.pdf');
		} else {
			return Excel::download(new PurchasesBookExport($lines, $business, $initial_month, $final_month, $initial_year, $final_year), 'purchases_book.xlsx');
		}
	}

	/**
	 * Get Cash Register Report
	 * @param null
	 * @return \PDF
 	 */
	public function getCashRegisterReport(){
		if(!auth()->user()->can('cash_register_report.view')) {
			abort(403, "Unauthorized action.");
		}

		$business_id = request()->session()->get('user.business_id');
		$business_name =
		Business::where('id', $business_id)
		->first()
		->name;

		$transaction_date = $this->taxUtil->uf_date(request()->input('trans_date'));
		$cashier_id = request()->input('cashier_id');

		$transactions = DB::select('CALL getCashRegisterReport(?, ?, ?)', [$business_id, $cashier_id, $transaction_date]);
		
		$cash_register_pdf = \PDF::loadView('reports.cash_register_report',
			compact('transactions', 'transaction_date', 'business_name'));
		$cash_register_pdf->setPaper('letter', 'landscape');

		return $cash_register_pdf->stream(__('report.cash_register_report') . '.pdf');
	}

		/**
	 * Get Cash Register Report 2.0
	 * @param null
	 * @return \PDF
 	 */
		public function getNewCashRegisterReport(){
			if(!auth()->user()->can('cash_register_report.view')) {
				abort(403, "Unauthorized action.");
			}
			$business_id = request()->session()->get('user.business_id');

			$cashier_closure_id = request()->input('cashier_closure_id');
			$transaction_date = CashierClosure::find($cashier_closure_id)->close_date;
			$business = Business::find($business_id);

			$business_name = $business->business_full_name;
			$show_expenses_on_sales_report = $business->show_expenses_on_sales_report;

			$location =
			CashierClosure::join('cashiers as c', 'cashier_closures.cashier_id', 'c.id')
			->join('business_locations as bl', '.business_location_id', 'bl.id')
			->where('cashier_closures.id', $cashier_closure_id)
			->select('bl.name', 'bl.id')
			->first();

			$location_name = $location->name;

			$transactions = collect(DB::select('CALL getNewCashRegisterReport(?)', [$cashier_closure_id]));
			$fcf = $transactions->where('doc_type', 'FCF')->toArray();
			$ccf = $transactions->where('doc_type', 'CCF')->toArray();
			$ticket = $transactions->where('doc_type', 'Ticket')->toArray();
			$exp = $transactions->where('doc_type', 'EXP')->toArray();

			$expenses = collect();
			if($show_expenses_on_sales_report) {
				$expenses = Transaction::join('expense_categories as ec', 'transactions.expense_category_id', 'ec.id')
				->where('type', 'expense')
				->where('location_id', $location->id)
				->whereRaw('DATE(transactions.transaction_date) = DATE(?)', [$transaction_date])
				->select(
					'ec.name as category',
					DB::Raw('SUM(transactions.final_total) as total')
				)->groupBY('ec.id')
				->get();
			}

			$cash_register_pdf = \PDF::loadView('reports.new_cash_register_report',
				compact('fcf', 'ccf', 'ticket', 'exp', 'transaction_date', 'business_name', 'location_name', 'expenses', 'show_expenses_on_sales_report'));
			$cash_register_pdf->setPaper('letter', 'landscape');

			return $cash_register_pdf->stream(__('report.cash_register_report') . '.pdf');
		}

	/**
	 * Get audit tape report
	 * @param int $cashier_closure_id
	 * @return \PDF
	 */
	public function getAuditTapeReport($cashier_closure_id){
		if(!auth()->user()->can('audit_tape.view')) {
			abort(403, "Unauthorized action.");
		}

		$business_id = request()->user()->business_id;

		$cashier_closure =
		CashierClosure::join("cashiers as c", "cashier_closures.cashier_id", "c.id")
		->where("cashier_closures.id", $cashier_closure_id)
		->select(
			"c.business_location_id as location_id",
			"cashier_closures.*"
		)->first();

		$business_details = $this->businessUtil->getDetails($business_id);
		$location_details = BusinessLocation::find($cashier_closure->location_id);
		$invoice_layout = $this->businessUtil->invoiceLayout($business_id, $cashier_closure->location_id, $location_details->invoice_layout_id);

		$sales =
		Transaction::where('cashier_closure_id', $cashier_closure_id)
		->join('document_types as dt', 'transactions.document_types_id', 'dt.id')
		->where('dt.short_name', 'Ticket')
		->select('transactions.id', 'transactions.type', 'transactions.correlative')
		->get();
		$sale_return =
		Transaction::join("transactions as rt", "transactions.id", "rt.return_parent_id")
		->join('document_types as dt', 'rt.document_types_id', 'dt.id')
		->where('dt.short_name', 'Ticket')
		->where('rt.cashier_closure_id', $cashier_closure_id)
		->select('rt.id', 'rt.type', 'rt.correlative')
		->get();

		$unsorted = $sales->union($sale_return);
		$transactions = $unsorted->sortBy('correlative');

		$tickets = [];
		foreach($transactions as $t){
			$ticket['ticket'] = $this->transactionUtil->getTicketDetails($t->id, $invoice_layout, $business_id, $location_details);
			$ticket['type'] = $t->type;

			array_push($tickets, $ticket);
		}
		
		$audit_tape_report_pdf =
		\PDF::loadView('reports.audit_tape_report_pdf', compact('tickets'));
		$audit_tape_report_pdf->setPaper([0, 0, 250, 450], 'portrait');

		return $audit_tape_report_pdf->stream(__('report.audit_tape_report') . '.pdf');
	}

	public function getHistoryPurchaseClients(){
		if(!auth()->user()->can('cash_register_report.view')) {
			abort(403, "Unauthorized action.");
		}

		$business_id = auth()->user()->business_id;
		$warehouses = Warehouse::forDropdown($business_id);
		$customers = Customer::where('business_id', $business_id)->pluck('name', 'id');
		return view('report.history_purchase_clients', compact('business_id', 'warehouses', 'customers'));
	}

	/**
	 * Post Sale summary by seller
	 * 
	 */
	public function getSalesSummarySellerReport() {
		if(!auth()->user()->can('sales_summary_by_seller.view')) {
			abort(403, "Unauthorized action.");
		}
		if(request()->ajax()){
			$start_date = request()->input('start_date') ? $this->transactionUtil->uf_date(request()->input('start_date')) : "";
			$end_date = request()->input('end_date') ? $this->transactionUtil->uf_date(request()->input('end_date')) : "";
			$location_id = request()->input('location_id') ? request()->input('location_id') : 0;

			$transactions = collect(DB::select('CALL get_sales_summary_by_seller(?, ?, ?)', [$start_date, $end_date, $location_id]));
			
			return Datatables::of($transactions)
			->editColumn('total_sale', '<span class="display_currency total_sale" data-currency_symbol="true" data-orig-value="{{ $total_sale }}">{{ $total_sale }}</span>')
			->removeColumn('sub_category', 'brand_name', 'unit_price', 'employee_id', 'status', 'cost', 'total_cost', 'utility')
			->rawColumns(['total_sale'])
			->toJson();

		}
		$business_id = auth()->user()->business_id;
		$locations = BusinessLocation::forDropdown($business_id, true);

		return view('reports.sales_summary_employee_report', compact('locations'));
	}

	/**
	 * Post Sale summary by seller
	 * 
	 */
	public function postSalesSummarySellerReport(){
		if(!auth()->user()->can('sales_summary_by_seller.view')) {
			abort(403, "Unauthorized action.");
		}

		$report_format = request()->input('report_format');
		$start_date = $this->transactionUtil->uf_date(request()->input('start_date'));
		$end_date = $this->transactionUtil->uf_date(request()->input('end_date'));
		$location_id = request()->input('location_id');
		
		$transactions = collect(DB::select('CALL get_sales_summary_by_seller(?, ?, ?)', [$start_date, $end_date, $location_id]));

		$location_name = BusinessLocation::where('id', $location_id)->value('name');
		if ($report_format == 'pdf') {
			$pdf = \PDF::loadView('reports.sales_summary_employee_report_pdf',
				compact('transactions', 'start_date', 'end_date', 'location_name'));
			$pdf->setPaper('A3', 'landscape');
			return $pdf->stream(__('report.sales_summary_seller_report') . '.pdf');
		} else {
			return Excel::download(new SalesSummaryBySeller($transactions), 'sales_summary_employee_report.xlsx');
		}
	}

	/**
	 * Get sales by seller report
	 * @author Arquímides Martínez
	 */
	public function getSalesBySellerReport(){
		if(!auth()->user()->can('sales_by_seller_report.view')) {
			abort(403, "Unauthorized action.");
		}
		$business_id = request()->user()->business_id;

		if(request()->ajax()){
			$start_date = $this->transactionUtil->uf_date(request()->input('start_date'));
			$end_date = $this->transactionUtil->uf_date(request()->input('end_date'));
			$location_id = request()->input('location_id') ? request()->input('location_id') : 0;

			$transactions = collect(DB::select('CALL get_sales_by_seller(?, ?, ?)', [$start_date, $end_date, $location_id]));
			
			return Datatables::of($transactions)
			->editColumn('total_before_tax', '<span class="display_currency total-before-tax" data-currency_symbol="true" data-orig-value="{{ $total_before_tax }}">{{ $total_before_tax }}</span>')
			->editColumn('total_amount', '<span class="display_currency total-amount" data-currency_symbol="true" data-orig-value="{{ $total_amount }}">{{ $total_amount }}</span>')
			->removeColumn('location_id')
			->rawColumns(['total_before_tax', 'total_amount'])
			->toJson();

		}

		$locations = BusinessLocation::forDropdown($business_id, true);
		return view('reports.sales_by_seller_report',
			compact('locations'));
	}

	/**
	 * Post sales by seller report
	 * @author Arquímides Martínez
	 */
	public function postSalesBySellerReport(){
		if(!auth()->user()->can('sales_by_seller_report.view')) {
			abort(403, "Unauthorized action.");
		}

		$format = request()->input('report_format');
		$start_date = $this->transactionUtil->uf_date(request()->input('start_date'));
		$end_date = $this->transactionUtil->uf_date(request()->input('end_date'));
		$location_id = request()->input('location_id') ? request()->input('location_id') : 0;

		$transactions = collect(DB::select('CALL get_sales_by_seller(?, ?, ?)', [$start_date, $end_date, $location_id]));
		$business_name = Business::where('id', request()->user()->business_id)->value('business_full_name');

		if ($format == 'pdf') {
			$pdf = \PDF::loadView('reports.sales_by_seller_report_pdf',
				compact('transactions', 'start_date', 'end_date', 'business_name'));
			$pdf->setPaper('letter', 'portrait');
			return $pdf->stream(__('report.sales_by_seller_report') . '.pdf');
		} else {
			$start_date = $this->transactionUtil->format_date($start_date);
			$end_date = $this->transactionUtil->format_date($end_date);

			return Excel::download(new SalesBySeller($transactions, $start_date, $end_date, $business_name), 'sales_by_seller_report.xlsx');
		}
	}

	/** get expense purchase report */
	public function getExpensePurchaseReport(){
		if(!auth()->user()->can('expense_purchase_report.view')) {
			abort(403, "Unauthorized action.");
		}
		
		$business_id = request()->user()->business_id;

		if(request()->ajax()){
			$year = request()->input('year');
			$location = request()->input('location') ? request()->input('location') : 0;

			$expense_summary = collect(DB::select('CALL get_expense_summary_report(?, ?)', [$year, $location]));

			return Datatables::of($expense_summary)
			->editColumn('jan', '<span class="display_currency jan" data-currency_symbol="true" data-orig-value="{{ $jan }}"> {{ $jan }}</span>')
			->editColumn('feb', '<span class="display_currency feb" data-currency_symbol="true" data-orig-value="{{ $feb }}"> {{ $feb }}</span>')
			->editColumn('mar', '<span class="display_currency mar" data-currency_symbol="true" data-orig-value="{{ $mar }}"> {{ $mar }}</span>')
			->editColumn('apr', '<span class="display_currency apr" data-currency_symbol="true" data-orig-value="{{ $apr }}"> {{ $apr }}</span>')
			->editColumn('may', '<span class="display_currency may" data-currency_symbol="true" data-orig-value="{{ $may }}"> {{ $may }}</span>')
			->editColumn('jun', '<span class="display_currency jun" data-currency_symbol="true" data-orig-value="{{ $jun }}"> {{ $jun }}</span>')
			->editColumn('jul', '<span class="display_currency jul" data-currency_symbol="true" data-orig-value="{{ $jul }}"> {{ $jul }}</span>')
			->editColumn('aug', '<span class="display_currency aug" data-currency_symbol="true" data-orig-value="{{ $aug }}"> {{ $aug }}</span>')
			->editColumn('sep', '<span class="display_currency sep" data-currency_symbol="true" data-orig-value="{{ $sep }}"> {{ $sep }}</span>')
			->editColumn('oct', '<span class="display_currency oct" data-currency_symbol="true" data-orig-value="{{ $oct }}"> {{ $oct }}</span>')
			->editColumn('nov', '<span class="display_currency nov" data-currency_symbol="true" data-orig-value="{{ $nov }}"> {{ $nov }}</span>')
			->editColumn('dec', '<span class="display_currency dec" data-currency_symbol="true" data-orig-value="{{ $dec }}"> {{ $dec }}</span>')
			->editColumn('total', '<span class="display_currency total" data-currency_symbol="true" data-orig-value="{{ $total }}"> {{ $total }}</span>')
			->rawColumns(['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'total'])
			->make(true);

			
		}

		$years = FiscalYear::pluck('year', 'year');
		
		$locations = BusinessLocation::forDropdown($business_id, true);



		return view('report.expense_purchase_report', compact('years', 'locations'));
	}

	/** pos expense purchase report */
	public function postExpensePurchaseReport(){
		if(!auth()->user()->can('expense_purchase_report.view')) {
			abort(403, "Unauthorized action.");
		}
		$business_id = request()->user()->business_id;
		$business_name = Business::find($business_id)->business_full_name;
		
		$year = request()->input('year');
		$location_id = request()->input('location') ? request()->input('location') : 0;
		$location_name = "";
		
		if($location_id > 0){
			$location = BusinessLocation::find($location_id);
			$location_name = __("report.expense_summary") . " - " . $location->name . " - " . $year;
		} else {
			$location_name = __("report.expense_summary") . " - " . __("report.all_locations") . " - " . $year;
		}

		$expenses = collect(DB::select('CALL get_expense_summary_report(?, ?)', [$year, $location_id]));

		return Excel::download(new ExpenseSummaryReportExport($expenses, $business_name, $location_name), 'expense_purchase_report.xlsx');
	}

	public function getHistoryPurchaseClientsReport(Request $request)
	{
		if(!auth()->user()->can('cash_register_report.view')) {
			abort(403, "Unauthorized action.");
		} 

		// dd($request);
		// Data form
		$business_id = auth()->user()->business_id;
		$initial_date = Carbon::parse($this->taxUtil->uf_date($request->input('initial_date')));
		$final_date = Carbon::parse($this->taxUtil->uf_date($request->input('final_date')));
		$product_id = $request->product_id;
		$customer_id = $request->customer_id;
		
		if($initial_date->gt($final_date)){
			$output = ['success'=> 0,'msg' => 'La fecha inicial no puede ser mayor a la fecha final'];
			return redirect('/reports/history_purchase_clients')->with('status', $output);
		}elseif(empty($product_id) || is_null($product_id)){
			$output = ['success'=> 0,'msg' => 'El producto es requerido'];
			return redirect('/reports/history_purchase_clients')->with('status', $output);
		}

		// storage procedures
		$lines = DB::select('CALL get_purchases_clients(?,?,?,?,?)', array($initial_date, $final_date, $business_id, $product_id, $customer_id));
		// Header info
		$business = Business::where('id', $business_id)->first();

		$months = array(__('accounting.january'), __('accounting.february'), __('accounting.march'), __('accounting.april'), __('accounting.may'), __('accounting.june'), __('accounting.july'), __('accounting.august'), __('accounting.september'), __('accounting.october'), __('accounting.november'), __('accounting.december'));
		$initial_month = $months[($initial_date->format('n')) - 1];
		$final_month = $months[($final_date->format('n')) - 1];
		$initial_year = $initial_date->format('Y');
		$final_year = $final_date->format('Y');

		$report_type = $request->input('report_type');
		if ($report_type == 'pdf') {
			$pdf = \PDF::loadView(
				'reports.history_purchases_pdf',
				compact(
					'lines',
					'business',
					'initial_month',
					'initial_date',
					'final_date',
					'final_month',
					'initial_year',
					'final_year'
				)
			);
			$pdf->setPaper('letter', 'portrait');
			return $pdf->stream('history_purchase_clients_report.pdf');
		} else {
			return Excel::download(new HistoryPurchaseExport($lines, $business, $initial_date, $final_date), 'history_purchase_clients_report.xlsx');
		}
	}

	/**
     * Shows treasury annexes forms.
     * 
     * @return \Illuminate\Http\Response
     */
	public function getTreasuryAnnexes()
	{
		if (! auth()->user()->can('treasury_annexes.view')) {
			abort(403, 'Unauthorized action.');
		}

		$business_id = request()->session()->get('user.business_id');
		$locations = BusinessLocation::forDropdown($business_id, true);
		$month = $this->taxUtil->first_last_month_day(now());

		return view('report.treasury_annexes', compact('locations', 'month'));
	}

	/**
	 * Get annex 1, detail of sales to taxpayers, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex1(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_1')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_1(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->transaction_date ?? '';
			$d['B'] = $line->document_class ?? '';
			$d['C'] = $line->document_type ?? '';
			$d['D'] = $line->resolution ?? '';
			$d['E'] = $line->serie ?? '';
			$d['F'] = $line->no_correlative ?? '';
			$d['G'] = $line->document_class == 2 ? $line->no_id : $line->no_correlative;
			$d['H'] = $line->nit ?? $line->nrc;
			$d['I'] = $line->name ?? $line->supplier_business_name;
			$d['J'] = number_format($line->exempt_sales, 2, '.', '');
			$d['K'] = '0.00';
			$d['L'] = number_format($line->taxed_sales, 2, '.', '');
			$d['M'] = number_format($line->fiscal_debit, 2, '.', '');
			$d['N'] = '0.00';
			$d['O'] = '0.00';
			$d['P'] = number_format($line->total, 2, '.', '');
			$d['Q'] = $line->no_annex ?? 1;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_1')), __('report.annex_1') . '.' . $report_type);
	}

	/**
	 * Get annex 2, detail of sales to final consumer, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex2(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_2')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_2(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$lines_sort = [];

		foreach ($lines as $line) {
			$key = $line->location_id . '-' . $line->document_types_id . '-' . $line->document_correlative_id;

			if (! array_key_exists($key, $lines_sort)) {
				$lines_sort[$key] = [];
			}

			array_push($lines_sort[$key], $line);
		}

		$data = [];

		foreach ($lines_sort as $lines) {
			foreach ($lines as $line) {
				$d = [];

				$d['A'] = $line->transaction_date ?? '';
				$d['B'] = $line->document_class ?? '';
				$d['C'] = $line->document_type ?? '';
				$d['D'] = $line->resolution ?? '';
				$d['E'] = $line->serie ?? '';
				$d['F'] = $line->document_class == 2 ? $line->initial_id : $line->initial_correlative;
				$d['G'] = $line->document_class == 2 ? $line->final_id : $line->final_correlative;
				$d['H'] = $line->initial_correlative ?? '';
				$d['I'] = $line->final_correlative ?? '';
				$d['J'] = '';
				$d['K'] = number_format($line->exempt_sales, 2, '.', '');
				$d['L'] = '0.00';
				$d['M'] = '0.00';
				$d['N'] = number_format($line->taxed_sales, 2, '.', '');
				$d['O'] = '0.00';
				$d['P'] = '0.00';
				$d['Q'] = number_format($line->exports, 2, '.', '');
				$d['R'] = '0.00';
				$d['S'] = '0.00';
				$d['T'] = number_format($line->total, 2, '.', '');
				$d['U'] = $line->no_annex ?? 2;

				$data[] = $d;
			}
		}

		return Excel::download(new AnnexExport($data, __('report.annex_2')), __('report.annex_2') . '.' . $report_type);
	}

	/**
	 * Get annex 3, Purchases detail, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex3(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_3')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_3(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->transaction_date ?? '';
			$d['B'] = $line->document_class ?? '';
			$d['C'] = $line->document_type ?? '';
			$d['D'] = $line->no_document ?? '';
			$d['E'] = $line->nit ?? $line->nrc;
			$d['F'] = $line->name ?? $line->supplier_business_name;
			$d['G'] = number_format($line->exempt_internals, 2, '.', '');
			$d['H'] = '0.00';
			$d['I'] = '0.00';
			$d['J'] = number_format($line->taxed_internals, 2, '.', '');
			$d['K'] = '0.00';
			$d['L'] = number_format($line->taxed_imports, 2, '.', '');
			$d['M'] = '0.00';
			$d['N'] = number_format($line->fiscal_credit, 2, '.', '');
			$d['O'] = number_format($line->total, 2, '.', '');
			$d['P'] = $line->no_annex ?? 3;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_3')), __('report.annex_3') . '.' . $report_type);
	}

	/**
	 * Get annex 5, purchases from excluded subjects, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex5(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_5')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_5(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->document_type ?? '';
			$d['B'] = $line->nit ?? $line->nrc;
			$d['C'] = $line->name ?? $line->supplier_business_name;
			$d['D'] = $line->transaction_date ?? '';
			$d['E'] = $line->serie ?? '';
			$d['F'] = $line->no_document ?? '';
			$d['G'] = number_format($line->total, 2, '.', '');
			$d['H'] = number_format($line->tax_amount, 2, '.', '');
			$d['I'] = $line->no_annex ?? 5;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_5')), __('report.annex_5') . '.' . $report_type);
	}

	/**
	 * Get annex 6, advance on account VAT of 2% made to the declarant, in csv
	 * format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex6(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_6')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_6(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->nit ?? '';
			$d['B'] = $line->transaction_date ?? '';
			$d['C'] = $line->serie ?? '';
			$d['D'] = $line->no_document ?? '';
			$d['E'] = number_format($line->total, 2, '.', '');
			$d['F'] = number_format($line->percent, 2, '.', '');
			$d['G'] = $line->no_annex ?? 6;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_6')), __('report.annex_6') . '.' . $report_type);
	}

	/**
	 * Get annex 7, 1% VAT withholding made to the declarant, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex7(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_7')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_7(?, ?, ?, ?)',
			array($initial_date, $final_date, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->nit ?? '';
			$d['B'] = $line->transaction_date ?? '';
			$d['C'] = $line->document_type ?? '';
			$d['D'] = $line->serie ?? '';
			$d['E'] = $line->no_document ?? '';
			$d['F'] = number_format($line->amount, 2, '.', '');
			$d['G'] = number_format($line->withheld, 2, '.', '');
			$d['H'] = $line->no_annex ?? 7;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_7')), __('report.annex_7') . '.' . $report_type);
	}

	/**
	 * Get annex 8, 1% VAT perception made to the declarant, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex8(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_8')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_8(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->nit ?? '';
			$d['B'] = $line->transaction_date ?? '';
			$d['C'] = $line->document_type ?? '';
			$d['D'] = $line->serie ?? '';
			$d['E'] = $line->no_document ?? '';
			$d['F'] = number_format($line->amount, 2, '.', '');
			$d['G'] = number_format($line->perception, 2, '.', '');
			$d['H'] = $line->no_annex ?? 8;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_8')), __('report.annex_8') . '.' . $report_type);
	}

	/**
	 * Get annex 9, 1% VAT perception made by the declarant, in csv format.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function exportAnnex9(Request $request)
	{
		if (! auth()->user()->can('treasury_annexes.annex_9')) {
			abort(403, 'Unauthorized action.');
		}

		// Params
		$initial_date = $this->taxUtil->uf_date($request->input('initial_date'));
		$final_date = $this->taxUtil->uf_date($request->input('final_date'));
		$location_id = ! empty($request->input('location_id')) ? $request->input('location_id') : 0;
		$business_id = request()->session()->get('user.business_id');
		$annex_number = $request->input('annex_number');
		$report_type = $request->input('report_type');

		// Query
		$lines = DB::select(
			'CALL annex_9(?, ?, ?, ?, ?)',
			array($initial_date, $final_date, $location_id, $business_id, $annex_number)
		);

		// Data
		$data = [];

		foreach ($lines as $line) {
			$d = [];

			$d['A'] = $line->nit ?? '';
			$d['B'] = $line->transaction_date ?? '';
			$d['C'] = $line->document_type ?? '';
			$d['D'] = $line->resolution ?? '';
			$d['E'] = $line->serie ?? '';
			$d['F'] = $line->no_document ?? '';
			$d['G'] = number_format($line->amount, 2, '.', '');
			$d['H'] = number_format($line->perception, 2, '.', '');
			$d['I'] = $line->no_annex ?? 9;

			$data[] = $d;
		}

		return Excel::download(new AnnexExport($data, __('report.annex_9')), __('report.annex_9') . '.' . $report_type);
	}
}
