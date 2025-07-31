<?php

namespace App\Services;

use App\Models\Etudiant;
use App\Models\EtudiantMatiereDropped;
use App\Models\Matiere;
use App\Models\AnneeAcademique;
use App\Models\Semestre;
use App\Models\Presence;
use App\Models\SessionDeCours;
use App\Models\ParentEtudiant;
use App\Models\Coordinateur;
use App\Models\Enseignant;
use App\Models\User;
use App\Models\CustomNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DropNotificationService
{
    /**
     * Vérifier et traiter automatiquement les drops basés sur le taux de présence
     */
    public function processAutomaticDrops()
    {
        Log::info('Début du traitement automatique des drops');

        $anneeActive = AnneeAcademique::getActive();
        if (!$anneeActive) {
            Log::warning('Aucune année académique active trouvée');
            return;
        }

        $semestreActif = Semestre::where('actif', true)->first();
        if (!$semestreActif) {
            Log::warning('Aucun semestre actif trouvé');
            return;
        }

        $etudiants = Etudiant::with(['classe', 'presences.sessionDeCours.matiere', 'parents'])->get();
        $dropsCrees = 0;

        foreach ($etudiants as $etudiant) {
            $this->processStudentDrops($etudiant, $anneeActive, $semestreActif, $dropsCrees);
        }

        Log::info("Traitement terminé. {$dropsCrees} drops automatiques créés.");
    }

    /**
     * Traiter les drops pour un étudiant spécifique
     */
    private function processStudentDrops(Etudiant $etudiant, AnneeAcademique $anneeAcademique, Semestre $semestre, &$dropsCrees)
    {
        // Récupérer toutes les matières de l'étudiant
        $matieres = $this->getMatieresForStudent($etudiant, $anneeAcademique, $semestre);

        foreach ($matieres as $matiere) {
            $tauxPresence = $this->calculatePresenceRate($etudiant, $matiere, $anneeAcademique, $semestre);

            // Si le taux de présence est <= 30%, créer un drop automatique
            if ($tauxPresence <= 30 && $tauxPresence > 0) {
                $dropExistant = EtudiantMatiereDropped::where([
                    'etudiant_id' => $etudiant->id,
                    'matiere_id' => $matiere->id,
                    'annee_academique_id' => $anneeAcademique->id,
                    'semestre_id' => $semestre->id,
                ])->first();

                if (!$dropExistant) {
                    $this->createAutomaticDrop($etudiant, $matiere, $anneeAcademique, $semestre, $tauxPresence);
                    $dropsCrees++;
                }
            }
        }
    }

    /**
     * Récupérer les matières pour un étudiant
     */
    private function getMatieresForStudent(Etudiant $etudiant, AnneeAcademique $anneeAcademique, Semestre $semestre)
    {
        return SessionDeCours::where('classe_id', $etudiant->classe_id)
            ->where('annee_academique_id', $anneeAcademique->id)
            ->where('semester_id', $semestre->id)
            ->with('matiere')
            ->get()
            ->pluck('matiere')
            ->unique('id');
    }

    /**
     * Calculer le taux de présence pour un étudiant dans une matière
     */
    private function calculatePresenceRate(Etudiant $etudiant, Matiere $matiere, AnneeAcademique $anneeAcademique, Semestre $semestre)
    {
        $sessions = SessionDeCours::where('classe_id', $etudiant->classe_id)
            ->where('matiere_id', $matiere->id)
            ->where('annee_academique_id', $anneeAcademique->id)
            ->where('semester_id', $semestre->id)
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalPresences = Presence::where('etudiant_id', $etudiant->id)
            ->whereIn('course_session_id', $sessions->pluck('id'))
            ->count();

        if ($totalPresences === 0) {
            return 0;
        }

        $presencesPresent = Presence::where('etudiant_id', $etudiant->id)
            ->whereIn('course_session_id', $sessions->pluck('id'))
            ->whereHas('statutPresence', function($query) {
                $query->where('nom', 'Présent');
            })
            ->count();

        return round(($presencesPresent / $totalPresences) * 100, 1);
    }

    /**
     * Créer un drop automatique et envoyer les notifications
     */
    private function createAutomaticDrop(Etudiant $etudiant, Matiere $matiere, AnneeAcademique $anneeAcademique, Semestre $semestre, $tauxPresence)
    {
        try {
            DB::beginTransaction();

            // Créer le drop
            $drop = EtudiantMatiereDropped::create([
                'etudiant_id' => $etudiant->id,
                'matiere_id' => $matiere->id,
                'annee_academique_id' => $anneeAcademique->id,
                'semestre_id' => $semestre->id,
                'raison_drop' => "Drop automatique - Taux de présence: {$tauxPresence}% (≤ 30%)",
                'date_drop' => now(),
                'dropped_by' => null, // Drop automatique
            ]);

            // Envoyer les notifications
            $this->sendDropNotifications($drop);

            DB::commit();
            Log::info("Drop automatique créé pour {$etudiant->prenom} {$etudiant->nom} dans {$matiere->nom}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la création du drop automatique: " . $e->getMessage());
        }
    }

    /**
     * Envoyer les notifications de drop
     */
    public function sendDropNotifications(EtudiantMatiereDropped $drop)
    {
        $message = $this->formatDropMessage($drop);
        $dateHeure = now()->format('d/m/Y à H:i');

        // Notification pour l'étudiant
        if ($drop->etudiant->user) {
            $this->createNotification($drop->etudiant->user, $message, 'warning');
        }

        // Notifications pour les parents
        foreach ($drop->etudiant->parents as $parent) {
            if ($parent->user) {
                $this->createNotification($parent->user, $message, 'warning');
            }
        }

        // Notification pour le coordinateur
        $coordinateur = $this->getCoordinateurForStudent($drop->etudiant);
        if ($coordinateur && $coordinateur->user) {
            $this->createNotification($coordinateur->user, $message, 'warning');
        }

        // Notification pour l'enseignant de la matière
        $enseignant = $this->getEnseignantForMatiere($drop->matiere, $drop->etudiant->classe);
        if ($enseignant && $enseignant->user) {
            $this->createNotification($enseignant->user, $message, 'warning');
        }
    }

    /**
     * Formater le message de drop
     */
    private function formatDropMessage(EtudiantMatiereDropped $drop)
    {
        $dateHeure = $drop->date_drop->format('d/m/Y à H:i');
        return "Vous avez été droppé de la matière \"{$drop->matiere->nom}\" le {$dateHeure}. Vous devez reprendre ce cours l'année prochaine.";
    }

    /**
     * Créer une notification pour un utilisateur
     */
    private function createNotification(User $user, string $message, string $type = 'warning')
    {
        $notification = CustomNotification::create([
            'message' => $message,
            'type' => $type,
        ]);

        // Associer la notification à l'utilisateur
        $notification->utilisateurs()->attach($user->id, ['lu_a' => false]);
    }

    /**
     * Récupérer le coordinateur pour un étudiant
     */
    private function getCoordinateurForStudent(Etudiant $etudiant)
    {
        if (!$etudiant->classe || !$etudiant->classe->promotion) {
            return null;
        }

        return Coordinateur::where('promotion_id', $etudiant->classe->promotion_id)
            ->where('est_actif', true)
            ->first();
    }

    /**
     * Récupérer l'enseignant pour une matière et une classe
     */
    private function getEnseignantForMatiere(Matiere $matiere, $classe)
    {
        if (!$classe) {
            return null;
        }

        // Récupérer l'enseignant qui enseigne cette matière dans cette classe
        $session = SessionDeCours::where('matiere_id', $matiere->id)
            ->where('classe_id', $classe->id)
            ->first();

        return $session ? $session->enseignant : null;
    }

    /**
     * Vérifier les drops existants et envoyer les notifications manquantes
     */
    public function checkAndSendMissingNotifications()
    {
        $dropsSansNotification = EtudiantMatiereDropped::with([
            'etudiant.user',
            'etudiant.parents.user',
            'matiere',
            'anneeAcademique',
            'semestre'
        ])->where('created_at', '>=', now()->subDays(7))->get();

        foreach ($dropsSansNotification as $drop) {
            $this->sendDropNotifications($drop);
        }
    }
}
