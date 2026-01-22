<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlSettings;
use App\Models\PnL\PnlCurrencyRate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $settings = PnlSettings::getOrCreate($userId);
        $currencies = PnlSettings::getCurrencies();
        $currencyRates = PnlCurrencyRate::getAllForUser($userId);
        
        return view('pnl.settings.index', compact('settings', 'currencies', 'currencyRates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'default_currency' => 'required|string|max:3',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_prefix' => 'required|string|max:10|alpha_num',
            'invoice_next_number' => 'nullable|integer|min:1',
            'send_email_on_payment_created' => 'boolean',
            'send_email_on_payment_paid' => 'boolean',
            'send_email_on_payment_scheduled' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_vat_number' => 'nullable|string|max:50',
            'rates' => 'nullable|array',
            'rates.*.from_currency' => 'required_with:rates|string|max:3',
            'rates.*.to_currency' => 'required_with:rates|string|max:3',
            'rates.*.rate' => 'required_with:rates|numeric|min:0',
        ]);

        $userId = auth()->id();
        $settings = PnlSettings::getOrCreate($userId);

        $validated['send_email_on_payment_created'] = $request->boolean('send_email_on_payment_created');
        $validated['send_email_on_payment_paid'] = $request->boolean('send_email_on_payment_paid');
        $validated['send_email_on_payment_scheduled'] = $request->boolean('send_email_on_payment_scheduled');

        // Remove rates from validated array before updating settings
        $rates = $validated['rates'] ?? [];
        unset($validated['rates']);

        $settings->update($validated);

        // Handle exchange rates
        // First, delete all existing rates for this user
        PnlCurrencyRate::where('user_id', $userId)->delete();
        
        // Then, create new rates
        foreach ($rates as $rateData) {
            if (!empty($rateData['from_currency']) && !empty($rateData['to_currency']) && isset($rateData['rate'])) {
                // Don't save if from and to are the same
                if ($rateData['from_currency'] !== $rateData['to_currency']) {
                    PnlCurrencyRate::create([
                        'user_id' => $userId,
                        'from_currency' => $rateData['from_currency'],
                        'to_currency' => $rateData['to_currency'],
                        'rate' => $rateData['rate'],
                    ]);
                }
            }
        }

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
