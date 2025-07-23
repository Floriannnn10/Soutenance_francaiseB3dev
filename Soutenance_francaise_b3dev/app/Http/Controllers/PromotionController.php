<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromotionController extends Controller
{
    public function index(): View
    {
        $promotions = Promotion::all();
        return view('promotions.index', compact('promotions'));
    }

    public function create(): View
    {
        return view('promotions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:promotions,nom',
        ]);
        Promotion::create(['nom' => $request->nom]);
        return redirect()->route('promotions.index')->with('success', 'Promotion créée avec succès.');
    }

    public function edit(Promotion $promotion): View
    {
        return view('promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:promotions,nom,' . $promotion->id,
        ]);
        $promotion->update(['nom' => $request->nom]);
        return redirect()->route('promotions.index')->with('success', 'Promotion modifiée avec succès.');
    }

    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Promotion supprimée avec succès.');
    }
} 