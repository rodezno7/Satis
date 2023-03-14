<?php

use App\Business;
use App\BusinessLocation;
use App\DocumentType;
use App\PrintFormat;
use Illuminate\Database\Seeder;

class PrintFormatSeeder extends Seeder
{
    /**
     * Create default print formats.
     *
     * @return void
     */
    public function run()
    {
        $print_formats = [
            'FCF'   => 'invoice',
            'CCF'   => 'fiscal_credit',
            'Ticket'    => 'ticket',
            'NCR'    => 'credit_note'
        ];

        $business = Business::pluck('id');

        foreach ($business as $b) {
            $locations = BusinessLocation::where('business_id', $b)->pluck('id');

            foreach ($locations as $l) {
                $document_types = DocumentType::where('business_id', $b)
                    ->select('id', 'short_name as doc')
                    ->get();
                
                $file = '';
                foreach ($document_types as $dt) {
                    $file = isset($print_formats[$dt->doc]) ? $print_formats[$dt->doc] : 'invoice';

                    PrintFormat::firstOrCreate(
                        [
                            'business_id'   => $b,
                            'location_id'   => $l,
                            'document_type_id'  => $dt->id
                        ],
                        [ 'format' => $file ]
                    );
                }
            }
        }
    }
}
