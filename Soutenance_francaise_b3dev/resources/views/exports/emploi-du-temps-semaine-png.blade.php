!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Emploi du temps de la semaine - {{ $etudiant->prenom }} {{ $etudiant->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
            color: #333;
            font-size: 12px;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2563eb;
        }
        .header h2 {
            margin: 8px 0;
            font-size: 18px;
            color: #374151;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #6b7280;
        }
        .week-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #2563eb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .week-info h3 {
            margin: 0;
            font-size: 16px;
            color: #1e40af;
        }
        .week-info p {
            margin: 5px 0;
            font-size: 12px;
            color: #374151;
        }
        .info-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .student-info {
            width: 48%;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .student-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #374151;
        }
        .student-info p {
            margin: 5px 0;
            font-size: 11px;
            color: #6b7280;
        }
        .stats {
            width: 48%;
            background-color: #f0f9ff;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #bae6fd;
        }
        .stats h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #374151;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .stat-item {
            text-align: center;
            padding: 8px;
            background-color: #eff6ff;
            border-radius: 6px;
            border: 1px solid #dbeafe;
        }
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            display: block;
        }
        .stat-label {
            font-size: 10px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            font-weight: bold;
            color: #374151;
            font-size: 10px;
        }
        .session-type {
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .presentiel { background-color: #d1fae5; color: #065f46; }
        .workshop { background-color: #dbeafe; color: #1e40af; }
        .elearning { background-color: #fef3c7; color: #92400e; }
        .empty-cell {
            text-align: center;
            color: #9ca3af;
            font-style: italic;
            font-size: 10px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 2px solid #e5e7eb;
            padding-top: 10px;
        }
        .time-slot {
            font-weight: bold;
            color: #2563eb;
        }
        .matiere-name {
            font-weight: bold;
            color: #1f2937;
        }
        .enseignant-name {
            color: #6b7280;
            font-size: 10px;
        }
        .location {
            color: #9ca3af;
            font-size: 9px;
        }
        .no-sessions {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 2px dashed #d1d5db;
        }
        .no-sessions h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #374151;
        }
        .no-sessions p {
            margin: 0;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Emploi du temps de la semaine</h1>
        <h2>{{ $etudiant->prenom }} {{ $etudiant->nom }}</h2>
        <p>Classe: {{ $etudiant->classe->nom ?? 'Non définie' }} | Généré le: {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="week-info">
        <h3>Semaine du {{ $debutSemaine->format('d/m/Y') }} au {{ $finSemaine->format('d/m/Y') }}</h3>
        <p><strong>{{ $sessions->count() }}</strong> session(s) programmée(s) cette semaine</p>
    </div>

    <div class="info-container">
        <div class="student-info">
            <h3>Informations étudiant</h3>
            <p><strong>Nom:</strong> {{ $etudiant->nom }}</p>
            <p><strong>Prénom:</strong> {{ $etudiant->prenom }}</p>
            <p><strong>Email:</strong> {{ $etudiant->email ?? 'Non renseigné' }}</p>
            <p><strong>Classe:</strong> {{ $etudiant->classe->nom ?? 'Non définie' }}</p>
        </div>

        <div class="stats">
            <h3>Statistiques</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">{{ $sessions->count() }}</span>
                    <span class="stat-label">Sessions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $sessions->pluck('matiere.nom')->unique()->count() }}</span>
                    <span class="stat-label">Matières</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $sessions->pluck('enseignant.nom')->unique()->count() }}</span>
                    <span class="stat-label">Enseignants</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $sessions->pluck('start_time')->map(function($date) { return $date->format('Y-m-d'); })->unique()->count() }}</span>
                    <span class="stat-label">Jours</span>
                </div>
            </div>
        </div>
    </div>

    @if($sessions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 12%">Date</th>
                    <th style="width: 15%">Horaire</th>
                    <th style="width: 25%">Matière</th>
                    <th style="width: 20%">Enseignant</th>
                    <th style="width: 10%">Type</th>
                    <th style="width: 18%">Lieu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                    <tr>
                        <td>{{ $session->start_time->format('d/m/Y') }}</td>
                        <td class="time-slot">{{ $session->start_time->format('H:i') }} - {{ $session->end_time->format('H:i') }}</td>
                        <td class="matiere-name">{{ $session->matiere->nom }}</td>
                        <td class="enseignant-name">{{ $session->enseignant->prenom }} {{ $session->enseignant->nom }}</td>
                        <td>
                            <span class="session-type {{ strtolower($session->typeCours->code ?? 'presentiel') }}">
                                {{ $session->typeCours->nom ?? 'Présentiel' }}
                            </span>
                        </td>
                        <td class="location">{{ $session->location ?? 'Non spécifié' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-sessions">
            <h3>Aucune session cette semaine</h3>
            <p>Aucune session de cours n'est programmée pour cette semaine.</p>
        </div>
    @endif

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion académique | © {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>
