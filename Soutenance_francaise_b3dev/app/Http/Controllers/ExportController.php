<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\SessionDeCours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function exportEmploiDuTemps(Request $request)
    {
        try {
            $user = Auth::user();
            $etudiant = $user->etudiant;

            if (!$etudiant) {
                return response()->json(['success' => false, 'message' => 'Étudiant non trouvé'], 404);
            }

            $anneeActive = AnneeAcademique::getActive();
            if (!$anneeActive) {
                return response()->json(['success' => false, 'message' => 'Aucune année académique active'], 404);
            }

            // Récupérer les sessions de l'étudiant
            $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
                ->where('classe_id', $etudiant->classe_id)
                ->where('annee_academique_id', $anneeActive->id)
                ->orderBy('start_time')
                ->get();

            $format = $request->get('format', 'pdf');
            $filename = 'emploi_du_temps_' . $etudiant->prenom . '_' . $etudiant->nom . '_' . date('Y-m-d') . '.' . $format;

            if ($format === 'png') {
                return $this->generatePNG($sessions, $etudiant, $filename);
            } else {
                return $this->generatePDF($sessions, $etudiant, $filename);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }

    // Nouvelle méthode pour exporter uniquement l'emploi du temps de la semaine
    public function exportEmploiDuTempsSemaine(Request $request)
    {
        try {
            $user = Auth::user();
            $etudiant = $user->etudiant;

            if (!$etudiant) {
                return response()->json(['success' => false, 'message' => 'Étudiant non trouvé'], 404);
            }

            $anneeActive = AnneeAcademique::getActive();
            if (!$anneeActive) {
                return response()->json(['success' => false, 'message' => 'Aucune année académique active'], 404);
            }

            // Déterminer la semaine à exporter
            $weekParam = $request->get('week');
            if ($weekParam) {
                $debutSemaine = Carbon::parse($weekParam)->startOfWeek();
            } else {
                $debutSemaine = Carbon::now()->startOfWeek();
            }
            $finSemaine = $debutSemaine->copy()->endOfWeek();

            // Récupérer uniquement les sessions de la semaine
            $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
                ->where('classe_id', $etudiant->classe_id)
                ->where('annee_academique_id', $anneeActive->id)
                ->whereBetween('start_time', [$debutSemaine, $finSemaine])
                ->orderBy('start_time')
                ->get();

            $format = $request->get('format', 'pdf');
            $filename = 'emploi_du_temps_semaine_' . $debutSemaine->format('d-m-Y') . '_' . $etudiant->prenom . '_' . $etudiant->nom . '.' . $format;

            if ($format === 'png') {
                return $this->generatePNGSemaine($sessions, $etudiant, $debutSemaine, $finSemaine, $filename);
            } else {
                return $this->generatePDFSemaine($sessions, $etudiant, $debutSemaine, $finSemaine, $filename);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'export: ' . $e->getMessage()], 500);
        }
    }

    private function generatePDF($sessions, $etudiant, $filename)
    {
        try {
            $html = view('exports.emploi-du-temps-pdf', compact('sessions', 'etudiant'))->render();

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                return Pdf::loadHTML($html)->stream($filename);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'DomPDF n\'est pas installé.',
                    'html' => $html
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
            ]);
        }
    }

    private function generatePNG($sessions, $etudiant, $filename)
    {
        try {
            // Créer le contenu HTML optimisé pour l'image
            $html = view('exports.emploi-du-temps-png', compact('sessions', 'etudiant'))->render();

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                // Utiliser DomPDF avec des paramètres optimisés pour l'image
                $pdf = Pdf::loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => false,
                    'defaultFont' => 'Arial',
                    'dpi' => 300, // Très haute résolution pour une image de qualité
                    'defaultMediaType' => 'screen'
                ]);

                // Retourner le PDF avec le nom PNG
                return $pdf->stream($filename);
            } else {
                // Fallback : retourner l'HTML pour conversion côté client
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'filename' => $filename,
                    'message' => 'DomPDF non disponible. Utilisez l\'HTML pour conversion manuelle.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PNG: ' . $e->getMessage()
            ]);
        }
    }

    // Nouvelles méthodes pour l'export de la semaine
    private function generatePDFSemaine($sessions, $etudiant, $debutSemaine, $finSemaine, $filename)
    {
        try {
            $html = view('exports.emploi-du-temps-semaine-pdf', compact('sessions', 'etudiant', 'debutSemaine', 'finSemaine'))->render();

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                return Pdf::loadHTML($html)->stream($filename);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'DomPDF n\'est pas installé.',
                    'html' => $html
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()
            ]);
        }
    }

    private function generatePNGSemaine($sessions, $etudiant, $debutSemaine, $finSemaine, $filename)
    {
        try {
            $html = view('exports.emploi-du-temps-semaine-png', compact('sessions', 'etudiant', 'debutSemaine', 'finSemaine'))->render();

            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = Pdf::loadHTML($html);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isPhpEnabled' => false,
                    'defaultFont' => 'Arial',
                    'dpi' => 300,
                    'defaultMediaType' => 'screen'
                ]);

                return $pdf->stream($filename);
            } else {
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'filename' => $filename,
                    'message' => 'DomPDF non disponible. Utilisez l\'HTML pour conversion manuelle.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du PNG: ' . $e->getMessage()
            ]);
        }
    }

    // Méthode alternative pour générer une vraie image PNG
    public function generateImagePNG(Request $request)
    {
        try {
            $user = Auth::user();
            $etudiant = $user->etudiant;

            if (!$etudiant) {
                return response()->json(['success' => false, 'message' => 'Étudiant non trouvé'], 404);
            }

            $anneeActive = AnneeAcademique::getActive();
            if (!$anneeActive) {
                return response()->json(['success' => false, 'message' => 'Aucune année académique active'], 404);
            }

            $sessions = SessionDeCours::with(['classe', 'matiere', 'enseignant', 'typeCours', 'statutSession'])
                ->where('classe_id', $etudiant->classe_id)
                ->where('annee_academique_id', $anneeActive->id)
                ->orderBy('start_time')
                ->get();

            $filename = 'emploi_du_temps_' . $etudiant->prenom . '_' . $etudiant->nom . '_' . date('Y-m-d') . '.png';

            // Créer une image simple avec GD
            $width = 800;
            $height = 600;
            $image = imagecreate($width, $height);

            // Couleurs
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            $blue = imagecolorallocate($image, 0, 123, 255);
            $gray = imagecolorallocate($image, 128, 128, 128);

            // Fond blanc
            imagefill($image, 0, 0, $white);

            // Titre
            $title = "Emploi du temps - " . $etudiant->prenom . " " . $etudiant->nom;
            imagestring($image, 5, 20, 20, $title, $black);

            // Informations étudiant
            $info = "Classe: " . ($etudiant->classe->nom ?? 'Non définie');
            imagestring($image, 3, 20, 50, $info, $gray);

            $date = "Généré le: " . date('d/m/Y H:i');
            imagestring($image, 3, 20, 70, $date, $gray);

            // Sessions
            $y = 120;
            $lineHeight = 25;

            if ($sessions->count() > 0) {
                imagestring($image, 4, 20, $y, "Sessions de cours:", $blue);
                $y += 30;

                foreach ($sessions->take(15) as $session) { // Limiter à 15 sessions pour l'image
                    $sessionText = $session->start_time->format('d/m/Y H:i') . " - " .
                                 ($session->matiere->nom ?? 'Matière inconnue') . " - " .
                                 ($session->enseignant->prenom ?? '') . " " . ($session->enseignant->nom ?? '');

                    imagestring($image, 2, 20, $y, $sessionText, $black);
                    $y += $lineHeight;

                    if ($y > $height - 50) break; // Éviter de déborder
                }

                if ($sessions->count() > 15) {
                    imagestring($image, 2, 20, $y, "... et " . ($sessions->count() - 15) . " autres sessions", $gray);
                }
            } else {
                imagestring($image, 3, 20, $y, "Aucune session de cours trouvée.", $gray);
            }

            // Statistiques
            $y = $height - 80;
            imagestring($image, 3, 20, $y, "Total sessions: " . $sessions->count(), $blue);
            $y += 20;
            imagestring($image, 3, 20, $y, "Matières: " . $sessions->pluck('matiere.nom')->unique()->count(), $blue);

            // Générer l'image
            ob_start();
            imagepng($image);
            $imageData = ob_get_clean();
            imagedestroy($image);

            return Response::make($imageData, 200, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération de l\'image: ' . $e->getMessage()
            ]);
        }
    }
}
