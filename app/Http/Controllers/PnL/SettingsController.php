<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $settings = PnlSettings::getOrCreate($userId);
        
        return view('pnl.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_prefix' => 'required|string|max:10|alpha_num',
            'send_email_on_payment_created' => 'boolean',
            'send_email_on_payment_paid' => 'boolean',
            'send_email_on_payment_scheduled' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_vat_number' => 'nullable|string|max:50',
        ]);

        $userId = auth()->id();
        $settings = PnlSettings::getOrCreate($userId);

        $validated['send_email_on_payment_created'] = $request->boolean('send_email_on_payment_created');
        $validated['send_email_on_payment_paid'] = $request->boolean('send_email_on_payment_paid');
        $validated['send_email_on_payment_scheduled'] = $request->boolean('send_email_on_payment_scheduled');

        $settings->update($validated);

        return redirect()
            ->route('pnl.settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    public function resetInvoiceSequence(Request $request)
    {
        $validated = $request->validate([
            'start_number' => 'required|integer|min:1',
        ]);

        $userId = auth()->id();
        $settings = PnlSettings::getOrCreate($userId);
        $settings->resetInvoiceSequence($validated['start_number']);

        return redirect()
            ->route('pnl.settings.index')
            ->with('success', 'Invoice sequence reset to ' . $validated['start_number']);
    }

    /**
     * Dismiss the walkthrough modal
     */
    public function dismissWalkthrough(Request $request)
    {
        $dontShow = $request->input('dont_show', false);
        
        if ($dontShow) {
            // Store permanently in user settings
            $userId = auth()->id();
            $settings = PnlSettings::getOrCreate($userId);
            $settings->update(['walkthrough_dismissed' => true]);
        }
        
        // Also set session
        session(['pnl_walkthrough_seen' => true]);
        
        return response()->json(['success' => true]);
    }
}
