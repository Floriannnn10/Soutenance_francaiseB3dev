<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SessionDeCours;
use App\Models\Enseignant;

echo "=== Vérification des sessions de l'enseignant ===\n";

// Trouver l'enseignant Florian Banga
$enseignant = Enseignant::where('nom', 'Banga')->where('prenom', 'Florian')->first();

if (!$enseignant) {
    echo "❌ Enseignant Florian Banga non trouvé\n";
    exit;
}

echo "✅ Enseignant trouvé: {$enseignant->prenom} {$enseignant->nom} (ID: {$enseignant->id})\n";

// Récupérer ses sessions
$sessions = SessionDeCours::with(['matiere', 'classe', 'typeCours'])
    ->where('enseignant_id', $enseignant->id)
    ->orderBy('start_time')
    ->get();

echo "\n=== Sessions de l'enseignant ===\n";
echo "Total: " . $sessions->count() . " sessions\n\n";

foreach ($sessions as $session) {
    echo "📚 Session: {$session->matiere->nom}\n";
    echo "   Classe: {$session->classe->nom}\n";
    echo "   Type: {$session->typeCours->nom}\n";
    echo "   Date: {$session->start_time}\n";
    echo "   Statut: {$session->statutSession->nom}\n";
    echo "---\n";
}

// Vérifier les sessions en présentiel
$sessionsPresentiel = $sessions->where('typeCours.nom', 'Présentiel');
echo "\n=== Sessions en présentiel ===\n";
echo "Total: " . $sessionsPresentiel->count() . " sessions\n\n";

foreach ($sessionsPresentiel as $session) {
    echo "🎯 Présentiel: {$session->matiere->nom} - {$session->classe->nom} - {$session->start_time}\n";
}

echo "\n=== Test de génération d'emploi du temps ===\n";

// Simuler la logique du dashboard
$sessionsPresentiel = SessionDeCours::with(['classe', 'matiere', 'typeCours'])
    ->where('enseignant_id', $enseignant->id)
    ->whereHas('typeCours', function($q) {
        $q->where('nom', 'Présentiel');
    })
    ->orderBy('start_time')
    ->get();

echo "Sessions en présentiel trouvées: " . $sessionsPresentiel->count() . "\n";

if ($sessionsPresentiel->count() > 0) {
    echo "Première session: " . $sessionsPresentiel->first()->matiere->nom . " - " . $sessionsPresentiel->first()->start_time . "\n";
} else {
    echo "❌ Aucune session en présentiel trouvée\n";
}
