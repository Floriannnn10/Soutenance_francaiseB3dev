?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JustificationAbsence;
use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class JustificationAbsenceController extends Controller
{
    /**
     * Affiche la page de justification d'absence
     */
    public function index()
    {
        $anneeActive = AnneeAcademique::getActive();
        $semestreActif = $anneeActive ? Semestre::getActiveForYear($anneeActive->id) : null;

        $absences = Presence::with(['etudiant', 'sessionDeCours.matiere', 'sessionDeCours.enseignant', 'justification'])
            ->whereHas('statutPresence', function($query) {
                $query->where('code', 'absent');
            })
            ->where('academic_year_id', $anneeActive?->id)
            ->where('semester_id', $semestreActif?->id)
            ->orderBy('enregistre_le', 'desc')
            ->get();

        return view('justifications.index', compact('absences', 'anneeActive', 'semestreActif'));
    }

    /**
     * Affiche le formulaire de justification
     */
    public function create($presenceId)
    {
        $presence = Presence::with(['etudiant', 'sessionDeCours.matiere', 'sessionDeCours.enseignant'])
            ->findOrFail($presenceId);

        return view('justifications.create', compact('presence'));
    }

    /**
     * Enregistre une justification d'absence
     */
    public function store(Request $request, $presenceId)
    {
        $request->validate([
            'motif' => 'required|string|max:500',
            'date_justification' => 'required|date|after_or_equal:today',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $presence = \App\Models\Presence::findOrFail($presenceId);
        $session = $presence->sessionDeCours;
        $semestre = $session ? $session->semestre : null;
        $annee = $semestre ? $semestre->anneeAcademique : null;
        if (!$annee || !$annee->actif || !$semestre || !$semestre->actif) {
            return redirect()->back()->with('error', "Impossible de justifier une absence sur une période non active.");
        }

        // Vérifier que l'absence n'est pas déjà justifiée
        if ($presence->justification) {
            return back()->withErrors(['message' => 'Cette absence est déjà justifiée']);
        }

        // Vérifier que la date de justification est dans la limite de 2 semaines
        $dateAbsence = Carbon::parse($presence->enregistre_le);
        $dateJustification = Carbon::parse($request->date_justification);

        if ($dateJustification->diffInDays($dateAbsence) > 14) {
            return back()->withErrors(['message' => 'La justification doit être faite dans un délai de 2 semaines maximum']);
        }

        $justification = new JustificationAbsence([
            'presence_id' => $presenceId,
            'motif' => $request->motif,
            'date_justification' => $request->date_justification,
            'justifie_par_user_id' => Auth::id(),
        ]);

        // Gérer le fichier joint si fourni
        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('justifications', 'public');
            $justification->piece_jointe = $path;
        }

        $justification->save();

        return redirect()->route('justifications.index')
            ->with('success', 'Absence justifiée avec succès');
    }

    /**
     * Affiche les détails d'une justification
     */
    public function show($justificationId)
    {
        $justification = JustificationAbsence::with([
            'presence.etudiant',
            'presence.sessionDeCours.matiere',
            'presence.sessionDeCours.enseignant',
            'justifiePar'
        ])->findOrFail($justificationId);

        return view('justifications.show', compact('justification'));
    }

    /**
     * Affiche le formulaire de modification d'une justification
     */
    public function edit($justificationId)
    {
        $justification = JustificationAbsence::with([
            'presence.etudiant',
            'presence.sessionDeCours.matiere'
        ])->findOrFail($justificationId);

        return view('justifications.edit', compact('justification'));
    }

    /**
     * Met à jour une justification
     */
    public function update(Request $request, $justificationId)
    {
        $request->validate([
            'motif' => 'required|string|max:500',
            'date_justification' => 'required|date',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $justification = \App\Models\JustificationAbsence::findOrFail($justificationId);
        $presence = $justification->presence;
        $session = $presence ? $presence->sessionDeCours : null;
        $semestre = $session ? $session->semestre : null;
        $annee = $semestre ? $semestre->anneeAcademique : null;
        if (!$annee || !$annee->actif || !$semestre || !$semestre->actif) {
            return redirect()->back()->with('error', "Impossible de modifier une justification sur une période non active.");
        }

        // Vérifier que la modification est dans la limite de 2 semaines
        $dateAbsence = Carbon::parse($justification->presence->enregistre_le);
        $dateJustification = Carbon::parse($request->date_justification);

        if ($dateJustification->diffInDays($dateAbsence) > 14) {
            return back()->withErrors(['message' => 'La modification doit respecter le délai de 2 semaines maximum']);
        }

        $justification->update([
            'motif' => $request->motif,
            'date_justification' => $request->date_justification,
        ]);

        // Gérer le fichier joint si fourni
        if ($request->hasFile('piece_jointe')) {
            // Supprimer l'ancien fichier
            if ($justification->piece_jointe) {
                \Storage::disk('public')->delete($justification->piece_jointe);
            }

            $path = $request->file('piece_jointe')->store('justifications', 'public');
            $justification->piece_jointe = $path;
            $justification->save();
        }

        return redirect()->route('justifications.index')
            ->with('success', 'Justification mise à jour avec succès');
    }

    /**
     * Supprime une justification
     */
    public function destroy($justificationId)
    {
        $justification = \App\Models\JustificationAbsence::findOrFail($justificationId);
        $presence = $justification->presence;
        $session = $presence ? $presence->sessionDeCours : null;
        $semestre = $session ? $session->semestre : null;
        $annee = $semestre ? $semestre->anneeAcademique : null;
        if (!$annee || !$annee->actif || !$semestre || !$semestre->actif) {
            return redirect()->back()->with('error', "Impossible de supprimer une justification sur une période non active.");
        }

        // Supprimer le fichier joint s'il existe
        if ($justification->piece_jointe) {
            \Storage::disk('public')->delete($justification->piece_jointe);
        }

        $justification->delete();

        return redirect()->route('justifications.index')
            ->with('success', 'Justification supprimée avec succès');
    }
}
