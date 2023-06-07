<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class BookFinalConsumerExport implements WithEvents, WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $lines;
    private $business;
    private $initial_month;
    private $final_month;
    private $initial_year;
    private $final_year;
    private $locations;
    private $location;
    private $transactionUtil;

    public function __construct($lines, $business, $initial_month, $final_month, $initial_year, $final_year, $locations, $location, $transactionUtil)
    {
    	$this->lines = $lines;
    	$this->business = $business;
    	$this->initial_month = $initial_month;
    	$this->final_month = $final_month;
        $this->initial_year = $initial_year;
        $this->final_year = $final_year;
        $this->locations = $locations;
        $this->location = $location;
        $this->transactionUtil = $transactionUtil;
    }

    public function title(): string
    {
    	return __('accounting.book_sales_final_consumer');
    }

    public function registerEvents(): array
    {
    	return [            
    		AfterSheet::class => function(AfterSheet $event) {
                /** General setup */
    			$event->sheet->setOrientation("portrait");
                $event->sheet->setShowGridlines(false);
                $row = 1; 

                /** Header */
                $event->sheet->rowHeight('1', 20);
                $event->sheet->setBold('A'. $row .':K'. $row);
                $event->sheet->mergeCells('A'. $row .':K'. $row);
                $event->sheet->setFontSize('A'. $row .':K'. $row, 14);
                $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                $event->sheet->horizontalAlign('A'. $row .':K'. $row, "center");
                $event->sheet->setCellValue('A'. $row, mb_strtoupper($this->business->business_full_name));

                /** Set report name if there is one only location */
                if ($this->location > 0) {
                    $row ++;

                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->mergeCells('A'. $row .':K'. $row);
                    $event->sheet->setFontSize('A'. $row .':K'. $row, 12);
                    $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->horizontalAlign('A'. $row .':K'. $row, "center");
                    $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('accounting.book_sales_final_consumer')));
                }

                $row += 2; // Increment two rows

                $event->sheet->columnWidth('A', 10);
                $event->sheet->columnWidth('B', 10);
                $event->sheet->columnWidth('C', 10);
                $event->sheet->columnWidth('D', 15);
                $event->sheet->columnWidth('E', 18);
                $event->sheet->columnWidth('F', 20);
                $event->sheet->columnWidth('G', 12);
                $event->sheet->columnWidth('H', 16);
                $event->sheet->columnWidth('I', 13);
                $event->sheet->columnWidth('J', 15);
                $event->sheet->columnWidth('K', 15);

                $general_totals = collect();
                foreach ($this->locations as $k => $l) {
                    /** Month */
                    $event->sheet->mergeCells('A'. $row .':C'. $row);
                    if ($this->initial_month == $this->final_month) {
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('accounting.month')). ': '. $this->initial_month);
                    }
                    else {
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('accounting.month'). ': '. $this->initial_month ." - ". $this->final_month));
                    }

                    /** Set location name if there is more than one location */
                    if ($this->location == 0) {

                        $event->sheet->setBold('D'. $row .':I'. $row);
                        $event->sheet->mergeCells('D'. $row .':I'. $row);
                        $event->sheet->horizontalAlign('D'. $row, 'center');
                        $event->sheet->setFontSize('D'. $row .':I'. $row, 17);
                        $event->sheet->verticalAlign('D'. $row .':I'. $row, 'center');
                        $event->sheet->setCellValue('D'. $row, strtoupper($l));
                    }

                    /** Registration number */
                    $event->sheet->mergeCells('J'. $row .':K'. $row);
                    $event->sheet->horizontalAlign('J'. $row, 'right');
                    $event->sheet->setCellValue('J'. $row, mb_strtoupper(__('accounting.record_no')) .': '. $this->business->nrc);

                    $row ++; // Increment row

                    /** Year */
                    $event->sheet->mergeCells('A'. $row .':C'. $row);
                    if ($this->initial_year == $this->final_year) {
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('accounting.year')) .': '. $this->initial_year);
                    
                    } else {
                        $event->sheet->setCellValue('A'. $row, mb_strtoupper(__('accounting.year')) .': '. $this->initial_year .' - '. $this->final_year);
                    }

                    /** Set report name if there is more than one location */
                    if ($this->location == 0) {

                        $event->sheet->setBold('D'. $row .':I'. $row);
                        $event->sheet->mergeCells('D'. $row .':I'. $row);
                        $event->sheet->horizontalAlign('D'. $row, 'center');
                        $event->sheet->verticalAlign('D'. $row .':I'. $row, 'center');
                        $event->sheet->setCellValue('D'. $row, mb_strtoupper(__('accounting.book_sales_final_consumer')));
                    }

                    /** Tax number */
                    $event->sheet->mergeCells('J'. $row .':K'. $row);
                    $event->sheet->horizontalAlign('J'. $row, 'right');
                    $event->sheet->setCellValue('J'. $row, mb_strtoupper(__('accounting.nit_no')) .': '. $this->business->nit);

                    $row ++; // Increment row
                    $event->sheet->setAllBorders('A'. $row .':K'. $row, 'thin');

                    /** Table head */
                    /** First row */
                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->horizontalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->mergeCells('A'. $row .':A'. ($row + 1));
                    $event->sheet->wrapText('A'. $row .':A'. ($row + 1));
                    $event->sheet->setCellValue('A'. $row, 'Fecha de emisión');
                    $event->sheet->mergeCells('B'. $row .':E'. $row);
                    $event->sheet->setCellValue('B'. $row, 'Documentos emitidos');
                    $event->sheet->mergeCells('F'. $row .':F'. ($row + 1));
                    $event->sheet->wrapText('F'. $row .':F'. ($row + 1));
                    $event->sheet->setCellValue('F'. $row, 'No. de caja o sistema computarizado');
                    $event->sheet->mergeCells('G'. $row .':I'. $row);
                    $event->sheet->setCellValue('G'. $row, 'Ventas');
                    $event->sheet->mergeCells('J'. $row .':J'. ($row + 1));
                    $event->sheet->wrapText('J'. $row .':J'. ($row + 1));
                    $event->sheet->setCellValue('J'. $row, 'Total ventas diarias propias');
                    $event->sheet->mergeCells('K'. $row .':K'. ($row + 1));
                    $event->sheet->wrapText('K'. $row .':K'. ($row + 1));
                    $event->sheet->setCellValue('K'. $row, 'Ventas a cuenta de terceros');

                    $row ++; // Increment row
                    $event->sheet->setAllBorders('A'. $row .':K'. $row, 'thin');

                    /** Second row */
                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->horizontalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->setCellValue('B'. $row, 'Del No.');
                    $event->sheet->setCellValue('C'. $row, 'Al No.');
                    $event->sheet->setCellValue('D'. $row, 'Serie');
                    $event->sheet->setCellValue('E'. $row, 'Resolución');
                    $event->sheet->setCellValue('G'. $row, 'Exentas');
                    $event->sheet->setCellValue('H'. $row, 'Internas grabadas');
                    $event->sheet->setCellValue('I'. $row, 'Exportaciones');

                    $row ++; // Increment row

                    $data = $this->lines->where('location_id', $k);
                    $total_fcf = 0;
                    $total_ticket = 0;
                    $total_exports = 0;

                    foreach($data as $d) {
                        $event->sheet->setAllBorders('A'. $row .':K'. $row, 'thin');
                        $event->sheet->setCellValue('A'. $row, $this->transactionUtil->format_date($d->transaction_date));
                        $event->sheet->setFormat('B'. $row .':E'. $row, '@');
                        $event->sheet->setCellValue('B'. $row, $d->initial_correlative);
                        $event->sheet->setCellValue('C'. $row, $d->final_correlative);
                        $event->sheet->setCellValue('D'. $row, $d->serie);
                        $event->sheet->setCellValue('E'. $row, $d->resolution);
                        $event->sheet->setCellValue('F'. $row, '');
                        $event->sheet->setCellValue('G'. $row, '');
                        $event->sheet->setFormat('H'. $row .':J'. $row, '$ #,##0.00_-');
                        $event->sheet->setCellValue('H'. $row, $d->taxed_sales);
                        $event->sheet->setCellValue('I'. $row, $d->exports);
                        $event->sheet->setCellValue('J'. $row, ($d->taxed_sales + $d->exports));
                        $event->sheet->setCellValue('K'. $row, '');

                        $row ++; // Increment row

                        if (config('app.business') == 'optics') {
                            if ($d->short_name == 'FACTURA') {
                                $total_fcf += $d->taxed_sales;
                            } else if ($d->short_name == 'Ticket') {
                                $total_ticket += $d->taxed_sales;
                            }
                        } else {
                            if ($d->short_name == 'FCF') {
                                $total_fcf += $d->taxed_sales;
                            } else if ($d->short_name == 'Ticket') {
                                $total_ticket += $d->taxed_sales;
                            }
                        }
                        $total_exports += $d->exports;
                    }
                    /** Totals */
                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setAllBorders('A'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->horizontalAlign('A'. $row .':F'. $row, 'center');
                    $event->sheet->setCellValue('A'. $row, 'TOTALES');
                    $event->sheet->setCellValue('G'. $row, '');
                    $event->sheet->setCellValue('H'. $row, ($total_fcf + $total_ticket));
                    $event->sheet->setCellValue('I'. $row, $total_exports);
                    $event->sheet->setCellValue('J'. $row, ($total_fcf + $total_ticket + $total_exports));
                    $event->sheet->setCellValue('K'. $row, '');

                    $row += 2; // Increment two rows

                    /** Total summary by location */
                    /** First row */
                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->horizontalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->mergeCells('A'. $row .':F'. ($row + 1));
                    $event->sheet->setCellValue('A'. $row, 'TOTAL '. strtoupper($l));
                    $event->sheet->mergeCells('G'. $row .':H'. $row);
                    $event->sheet->setCellValue('G'. $row, 'PROPIAS');
                    $event->sheet->mergeCells('I'. $row .':J'. $row);
                    $event->sheet->setCellValue('I'. $row, 'A CUENTA DE TERCERAS');
                    $event->sheet->mergeCells('K'. $row .':K'. ($row + 1));
                    $event->sheet->setCellValue('K'. $row, 'TOTAL');

                    $row ++; // Increment row

                    /** Second row */
                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->horizontalAlign('A'. $row .':K'. $row, 'center');
                    $event->sheet->setCellValue('G'. $row, 'VALOR NETO');
                    $event->sheet->setCellValue('H'. $row, 'DÉBITO FISCAL');
                    $event->sheet->setCellValue('I'. $row, 'VALOR NETO');
                    $event->sheet->setCellValue('J'. $row, 'DÉBITO FISCAL');

                    $row ++; // Increment row

                    /** Start calc summary totals */
                    $total_fcf_exc_tax = $total_fcf / 1.13;
                    $total_fcf_tax = $total_fcf - ($total_fcf / 1.13);

                    $general_totals->push(
                        collect([
                            "order" => 1,
                            "description"  => "VENTAS INTERNAS GRAVADAS CONSUMIDOR FINAL ". strtoupper($l),
                            "excluded_tax"   => $total_fcf_exc_tax,
                            "taxes"   => $total_fcf_tax,
                            "final_total" => $total_fcf
                        ])
                    );
                    if (config('app.business') != 'optics') {
                        $general_totals->push(
                            collect([
                                "order" => 2,
                                "description"  => "VENTAS INTERNAS EXENTAS CONSUMIDOR ". strtoupper($l),
                                "excluded_tax"   => "0",
                                "taxes"   => "0",
                                "final_total" => "0"
                            ])
                        );
                    }

                    $total_ticket_exc_tax = $total_ticket / 1.13;
                    $total_ticket_tax = $total_ticket - ($total_ticket / 1.13);
                    
                    if (config('app.business') != 'optics') {
                        $general_totals->push(
                            collect([
                                "order" => 3,
                                "description"  => "VENTAS INTERNAS GRAVADAS CONSUMIDOR TICKET ". strtoupper($l),
                                "excluded_tax"   => $total_ticket_exc_tax,
                                "taxes"   => $total_ticket_tax,
                                "final_total" => $total_ticket
                            ])
                        );
                    }

                    $total_exports_exc_tax = $total_exports;
                    $total_exports_tax = 0;

                    if (config('app.business') != 'optics') {
                        $general_totals->push(
                            collect([
                                "order" => 4,
                                "description"  => "EXPORTACIONES SEGÚN FACTURAS ". strtoupper($l),
                                "excluded_tax"   => $total_exports_exc_tax,
                                "taxes"   => $total_exports_tax,
                                "final_total" => $total_exports
                            ])
                        );
                    }
                    /** End calc summary totals */

                    /** Table body */
                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setCellValue('A'. $row, 'VENTAS INTERNAS GRAVADAS CONSUMIDOR');
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->setCellValue('G'. $row, $total_fcf_exc_tax);
                    $event->sheet->setCellValue('H'. $row, $total_fcf_tax);
                    $event->sheet->setCellValue('I'. $row, '');
                    $event->sheet->setCellValue('J'. $row, '');
                    $event->sheet->setCellValue('K'. $row, $total_fcf);
                    $row ++; // Increment row

                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setCellValue('A'. $row, 'VENTAS INTERNAS EXENTAS CONSUMIDOR');
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->setCellValue('G'. $row, '0');
                    $event->sheet->setCellValue('H'. $row, '0');
                    $event->sheet->setCellValue('I'. $row, '');
                    $event->sheet->setCellValue('J'. $row, '');
                    $event->sheet->setCellValue('K'. $row, '0');
                    $row ++; // Increment row

                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setCellValue('A'. $row, 'VENTAS INTERNAS GRAVADAS CONSUMIDOR TICKET');
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->setCellValue('G'. $row, $total_ticket_exc_tax);
                    $event->sheet->setCellValue('H'. $row, $total_ticket_tax);
                    $event->sheet->setCellValue('I'. $row, '');
                    $event->sheet->setCellValue('J'. $row, '');
                    $event->sheet->setCellValue('K'. $row, $total_ticket);
                    $row ++; // Increment row
                    
                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setCellValue('A'. $row, 'EXPORTACIONES SEGÚN FACTURAS');
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->setCellValue('G'. $row, $total_exports_exc_tax);
                    $event->sheet->setCellValue('H'. $row, $total_exports_tax);
                    $event->sheet->setCellValue('I'. $row, '');
                    $event->sheet->setCellValue('J'. $row, '');
                    $event->sheet->setCellValue('K'. $row, $total_exports);
                    $row ++; // Increment row

                    $event->sheet->setBold('A'. $row .':K'. $row);
                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setCellValue('A'. $row, 'TOTALES');
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->setCellValue('G'. $row, ($total_fcf_exc_tax + $total_ticket_exc_tax + $total_exports_exc_tax));
                    $event->sheet->setCellValue('H'. $row, ($total_fcf_tax + $total_ticket_tax + $total_exports_tax));
                    $event->sheet->setCellValue('I'. $row, '');
                    $event->sheet->setCellValue('J'. $row, '');
                    $event->sheet->setCellValue('K'. $row, ($total_fcf + $total_ticket + $total_exports));
                    $row += 2; // Increment two rows
                }

                /** General totals for each location */
                $total_excluded_tax = 0;
                $total_taxes = 0;
                $total_final_total = 0;

                /** Table head */
                /** First row */
                $event->sheet->setBold('A'. $row .':K'. $row);
                $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                $event->sheet->horizontalAlign('A'. $row .':K'. $row, 'center');
                $event->sheet->mergeCells('A'. $row .':F'. ($row + 1));
                $event->sheet->setCellValue('A'. $row, 'TOTAL GENERAL');
                $event->sheet->mergeCells('G'. $row .':H'. $row);
                $event->sheet->setCellValue('G'. $row, 'PROPIAS');
                $event->sheet->mergeCells('I'. $row .':J'. $row);
                $event->sheet->setCellValue('I'. $row, 'A CUENTA DE TERCERAS');
                $event->sheet->mergeCells('K'. $row .':K'. ($row + 1));
                $event->sheet->setCellValue('K'. $row, 'TOTAL');

                $row ++; // Increment row

                /** Second row */
                $event->sheet->setBold('A'. $row .':K'. $row);
                $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                $event->sheet->verticalAlign('A'. $row .':K'. $row, 'center');
                $event->sheet->horizontalAlign('A'. $row .':K'. $row, 'center');
                $event->sheet->setCellValue('G'. $row, 'VALOR NETO');
                $event->sheet->setCellValue('H'. $row, 'DÉBITO FISCAL');
                $event->sheet->setCellValue('I'. $row, 'VALOR NETO');
                $event->sheet->setCellValue('J'. $row, 'DÉBITO FISCAL');
                $row ++; // Increment row

                $general_totals = $general_totals->sortBy('order');
                
                /** Table body */
                foreach($general_totals as $gt) {
                    $total_excluded_tax += $gt["excluded_tax"];
                    $total_taxes += $gt["taxes"];
                    $total_final_total += $gt["final_total"];

                    $event->sheet->mergeCells('A'. $row .':F'. $row);
                    $event->sheet->setCellValue('A'. $row, $gt['description']);
                    $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                    $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                    $event->sheet->setCellValue('G'. $row, $gt['excluded_tax']);
                    $event->sheet->setCellValue('H'. $row, $gt['taxes']);
                    $event->sheet->setCellValue('I'. $row, '');
                    $event->sheet->setCellValue('J'. $row, '');
                    $event->sheet->setCellValue('K'. $row, $gt['final_total']);

                    $row ++; // Increment row
                }

                /** Table foot */
                $event->sheet->setBold('A'. $row .':K'. $row);
                $event->sheet->mergeCells('A'. $row .':F'. $row);
                $event->sheet->setCellValue('A'. $row, 'TOTALES');
                $event->sheet->setAllBorders('G'. $row .':K'. $row, 'thin');
                $event->sheet->setFormat('G'. $row .':K'. $row, '$ #,##0.00_-');
                $event->sheet->setCellValue('G'. $row, $total_excluded_tax);
                $event->sheet->setCellValue('H'. $row, $total_taxes);
                $event->sheet->setCellValue('I'. $row, '');
                $event->sheet->setCellValue('J'. $row, '');
                $event->sheet->setCellValue('K'. $row, $total_final_total);
                
                $row += 4; // Increment two rows
                
                /** Accountant signature */
                $event->sheet->setBold('A'. $row .':C'. $row);
                $event->sheet->setBorderBottom('A'. $row .':C'. $row, 'thin');
                $event->sheet->setCellValue('A'. $row, 'F.');

                $row ++;
                $event->sheet->setBold('A'. $row .':C'. $row);
                $event->sheet->mergeCells('A'. $row .':C'. $row);
                $event->sheet->horizontalAlign('A'. $row .':C'. $row, 'center');
                $event->sheet->setCellValue('A'. $row, 'Contador');
                /** End accountant signature */

                /** Set font size and family */
                if ($this->location == 0) {
                    $event->sheet->setFontSize('A2:K'. $row, 10);
                } else {
                    $event->sheet->setFontSize('A3:K'. $row, 10);
                }
                $event->sheet->setFontFamily('A1:K'. $row, 'Calibri');
            },
        ];
    }
}