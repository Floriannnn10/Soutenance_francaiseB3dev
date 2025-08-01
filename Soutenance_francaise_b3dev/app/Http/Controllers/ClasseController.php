<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Promotion;
use App\Traits\DaisyUINotifier;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    use DaisyUINotifier;
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

        return $this->successNotification('Classe créée avec succès !', 'classes.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classe $class)
    {
        $class->load(['etudiants', 'sessionsDeCours.matiere', 'sessionsDeCours.enseignant.user', 'sessionsDeCours.statutSession']);
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

        return $this->warningNotification('Classe mise à jour avec succès !', 'classes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classe $class)
    {
        $class->delete();

        return $this->errorNotification('Classe supprimée avec succès !', 'classes.index');
    }
}
