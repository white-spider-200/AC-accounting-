<?php

namespace App\Http\Controllers;

use App\Models\VatRate;
use Illuminate\Http\Request;

class VatRateController extends Controller
{
    public function index()
    {
        $vatRates = VatRate::query()
            ->orderBy('sort_order')
            ->orderBy('rate')
            ->get();

        return view('admin.accounting.vat-rates', compact('vatRates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        VatRate::create([
            'name' => $data['name'],
            'rate' => $data['rate'],
            'is_active' => (bool) ($data['is_active'] ?? true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('accounting.gl-management.vat-rates')
            ->with('success', __('VAT option added successfully'));
    }

    public function update(Request $request, VatRate $vatRate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $vatRate->update([
            'name' => $data['name'],
            'rate' => $data['rate'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return redirect()->route('accounting.gl-management.vat-rates')
            ->with('success', __('VAT option updated successfully'));
    }

    public function destroy(VatRate $vatRate)
    {
        $vatRate->delete();

        return redirect()->route('accounting.gl-management.vat-rates')
            ->with('success', __('VAT option deleted successfully'));
    }
}

