<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Emploi du temps - {{ $etudiant->prenom }} {{ $etudiant->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .student-info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .session-type {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .presentiel { background-color: #d4edda; color: #155724; }
        .workshop { background-color: #d1ecf1; color: #0c5460; }
        .elearning { background-color: #fff3cd; color: #856404; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Emploi du temps</h1>
        <h2>{{ $etudiant->prenom }} {{ $etudiant->nom }}</h2>
        <p>Classe: {{ $etudiant->classe->nom ?? 'Non définie' }}</p>
        <p>Généré le: {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="student-info">
        <h3>Informations de l'étudiant</h3>
        <p><strong>Nom:</strong> {{ $etudiant->nom }}</p>
        <p><strong>Prénom:</strong> {{ $etudiant->prenom }}</p>
        <p><strong>Email:</strong> {{ $etudiant->email ?? 'Non renseigné' }}</p>
        <p><strong>Classe:</strong> {{ $etudiant->classe->nom ?? 'Non définie' }}</p>
    </div>

    <h3>Sessions de cours</h3>
    @if($sessions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Matière</th>
                    <th>Enseignant</th>
                    <th>Type</th>
                    <th>Lieu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                    <tr>
                        <td>{{ $session->start_time->format('d/m/Y') }}</td>
                        <td>{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</td>
                        <td>{{ $session->matiere->nom }}</td>
                        <td>{{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</td>
                        <td>
                            <span class="session-type {{ strtolower($session->typeCours->code ?? 'presentiel') }}">
                                {{ $session->typeCours->nom ?? 'Présentiel' }}
                            </span>
                        </td>
                        <td>{{ $session->location ?? 'Non spécifié' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <h4>Statistiques</h4>
            <p><strong>Total des sessions:</strong> {{ $sessions->count() }}</p>
            <p><strong>Matières uniques:</strong> {{ $sessions->pluck('matiere.nom')->unique()->count() }}</p>
            <p><strong>Enseignants:</strong> {{ $sessions->pluck('enseignant.nom')->unique()->count() }}</p>
        </div>
    @else
        <p>Aucune session de cours trouvée.</p>
    @endif

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion académique</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>
