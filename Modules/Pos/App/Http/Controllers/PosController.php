<?php

namespace Modules\Pos\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\invApp\SalesInvoice;
use Mpdf\Mpdf;

class PosController extends Controller
{
    /**
     * Display the device selection screen.
     */
    public function selectDevice()
    {
        $devices = \Modules\Pos\App\Models\PosDevice::all();
        return view('pos::auth.select_device', compact('devices'));
    }

    /**
     * Display the POS Vue Screen.
     */
    public function index(Request $request, $uuid)
    {
        $device = \Modules\Pos\App\Models\PosDevice::with(['paymentMethods' => function($q) {
            $q->where('is_active', true);
        }])->where('uuid', $uuid)->first();
        if (!$device) {
            abort(404, 'POS Device not found');
        }
    

        return view('pos::index', compact('device'));
    }

    /**
     * Print thermal receipt for a specific invoice.
     */
    public function print($id)
    {
        $invoice = SalesInvoice::with(['items.product.units', 'items.unitname', 'customer', 'store', 'branch', 'payments', 'createdBy'])->findOrFail($id);

        // Use the thermal template from the application's Template system
        $html = \App\Services\TemplateRenderingService::renderDocument($invoice, 'SalesInvoice', 'thermal');

        return view('pos::print_thermal', [
            'html' => $html,
            'title' => 'Receipt ' . $invoice->invoice_number
        ]);
    }

    /**
     * Print thermal Z-Report for a specific session.
     */
    public function printSession($id)
    {
        $session = \Modules\Pos\App\Models\PosSession::with(['device.store', 'device.branch', 'cashier', 'transactions'])->findOrFail($id);

        $html = view('pos::session_receipt', compact('session'))->render();

        // Append auto-print and close scripts for Z-Report specifically
        $script = '<script>window.onload = function() { window.print(); setTimeout(() => window.close(), 1500); }</script>';

        return response($html . $script)->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pos::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $salesInvoice = SalesInvoice::with(['items.product.units', 'items.unitname', 'payments', 'customer', 'branch', 'createdBy'])->findOrFail($id);

        $renderedTemplate = \App\Services\TemplateRenderingService::renderDocument($salesInvoice, 'SalesInvoice', 'A4');

        return view('invoices::sales_invoices.show', compact('salesInvoice', 'renderedTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('pos::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
