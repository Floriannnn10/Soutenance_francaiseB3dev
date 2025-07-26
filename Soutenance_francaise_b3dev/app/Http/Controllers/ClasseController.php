<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Promotion;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classe::with('promotion')->get();
        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $promotions = Promotion::all();
        return view('classes.create', compact('promotions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'promotion_id' => 'required|exists:promotions,id',
        ]);

        Classe::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Classe créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classe $class)
    {
        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classe $class)
    {
        $promotions = Promotion::all();
        return view('classes.edit', compact('class', 'promotions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classe $class)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'promotion_id' => 'required|exists:promotions,id',
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Classe mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classe $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Classe supprimée avec succès.');
    }
}
