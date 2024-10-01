<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\PdfGenerationProgress;
use App\Http\Controllers\Controller;
use App\Traits\PdfTrait;

class PDFCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PdfTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $invoice_id;
    public function __construct($id)
    {
        $this->invoice_id = $id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $this->invoice_id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();
        $options = [
            'format' => 'A5',
            'default_font_size' => '11',
            'orientation'   => 'L',
            'encoding'      => 'UTF-8',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,

            'title' => $invoice->ftth_id,
            'fontDir'          => array_merge($fontDirs, [base_path('resources/fonts/')]),
            'fontdata'         => $fontData + [
                'notosanthai' => [
                    'R' => 'NotoSansThai-Regular.ttf'
                ],
                'notoserifmyanmar' => [
                    'R' => 'NotoSerifMyanmar-Regular.ttf'
                ],
                'pyidaungsu' => [
                    'R' => 'Pyidaungsu-2.5.3_Regular.ttf'
                ],
                'serif' => [
                    'R' => 'NotoSerif-Regular.ttf',
                    'B' => 'NotoSerif-Bold.ttf'
                ]
            ],
        ];
        $name = date("ymdHis") . '-' . $invoice->bill_number . ".pdf";
        $path = $invoice->ftth_id . '/' . $name;
        $pdf = $this->createPDF($options, 'invoice_compact', $invoice, $name, $path);

        if ($pdf['status'] == 'success') {

            // Successfully stored. Return the full path.
            $invoice->invoice_file =  $pdf['disk_path'];
            $invoice->invoice_url = $pdf['shortURL'];
            $invoice->update();
            $app_url = getenv('APP_URL', 'http://localhost:8000');

            $all_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                ->count();
            $file_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                ->whereNotNull('invoice_file')
                ->count();
            $progress = round(($file_invoice / $all_invoice) * 100);
            event(new PdfGenerationProgress($progress, $this->invoice_id, $pdf['disk_path'], $pdf['shortURL']));
        }

        //Testing Block
        // $invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
        //     ->join('packages', 'customers.package_id', 'packages.id')
        //     ->where('invoices.id', '=', $this->invoice_id)
        //     ->select('invoices.*', 'packages.type as service_type')
        //     ->first();
        // if ($invoice) {
        //     $invoice->invoice_file = "Test";
        //     $invoice->invoice_url = "Test";
        //     if ($invoice->update()) {

        //         $all_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
        //             ->count();
        //         $file_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
        //             ->whereNotNull('invoice_file')
        //             ->count();
        //         $progress = round(($file_invoice / $all_invoice) * 100);

        //         //PdfGenerationProgress::dispatchSync($progress);

        //         //event(new PdfGenerationProgress($progress));
        //         event(new PdfGenerationProgress($progress));
        //         sleep(1);
        //     }
        // }
    }
}
