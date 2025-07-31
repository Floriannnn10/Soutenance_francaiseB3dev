<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Emploi du Temps - {{ $etudiant->prenom }} {{ $etudiant->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
            width: 1200px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            color: #666;
            margin: 8px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 2px solid #333;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        .session-info {
            margin: 5px 0;
        }
        .session-matiere {
            font-weight: bold;
            color: #2563eb;
            font-size: 16px;
        }
        .session-enseignant {
            color: #666;
            font-size: 14px;
            margin-top: 4px;
        }
        .session-type {
            color: #059669;
            font-size: 14px;
            margin-top: 4px;
        }
        .session-lieu {
            color: #7c3aed;
            font-size: 14px;
            margin-top: 4px;
        }
        .session-heure {
            color: #dc2626;
            font-weight: bold;
            font-size: 14px;
        }
        .session-date {
            color: #ea580c;
            font-weight: bold;
            font-size: 14px;
        }
        .session-statut {
            color: #059669;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Emploi du Temps</h1>
        <p><strong>👤 Étudiant :</strong> {{ $etudiant->prenom }} {{ $etudiant->nom }}</p>
        <p><strong>🏫 Classe :</strong> {{ $etudiant->classe->nom ?? 'Non assignée' }}</p>
        <p><strong>📅 Année académique :</strong> {{ \App\Models\AnneeAcademique::getActive()->nom ?? 'Non définie' }}</p>
        <p><strong>📋 Date d'export :</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>📖 Matière</th>
                <th>👨‍🏫 Enseignant</th>
                <th>📅 Date</th>
                <th>⏰ Heure</th>
                <th>🎯 Type</th>
                <th>📊 Statut</th>
                <th>📍 Lieu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $session)
                <tr>
                    <td>
                        <div class="session-info">
                            <div class="session-matiere">{{ $session->matiere->nom ?? 'Non défini' }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="session-info">
                            <div class="session-enseignant">
                                {{ ($session->enseignant->prenom ?? '') }} {{ ($session->enseignant->nom ?? '') }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="session-date">{{ $session->start_time->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div class="session-heure">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="session-type">{{ $session->typeCours->nom ?? 'Non défini' }}</div>
                    </td>
                    <td>
                        <div class="session-statut">{{ $session->statutSession->nom ?? 'Non défini' }}</div>
                    </td>
                    <td>
                        <div class="session-lieu">{{ $session->location ?? 'Non spécifié' }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #666; font-style: italic;">
                        📝 Aucune session de cours programmée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>📄 Document généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>📊 Total des sessions : {{ $sessions->count() }}</p>
    </div>
</body>
</html>
