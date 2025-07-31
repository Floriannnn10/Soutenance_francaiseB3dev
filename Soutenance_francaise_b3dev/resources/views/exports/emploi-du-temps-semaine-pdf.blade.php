!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Emploi du temps de la semaine - {{ $etudiant->prenom }} {{ $etudiant->nom }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 10px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #2563eb;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #374151;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #6b7280;
        }
        .week-info {
            background-color: #eff6ff;
            padding: 8px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            border-left: 3px solid #2563eb;
        }
        .week-info h3 {
            margin: 0;
            font-size: 12px;
            color: #1e40af;
        }
        .week-info p {
            margin: 2px 0;
            font-size: 10px;
            color: #374151;
        }
        .student-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            margin-bottom: 15px;
        }
        .student-info h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #374151;
        }
        .student-info p {
            margin: 2px 0;
            font-size: 9px;
            color: #6b7280;
        }
        .stats {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            text-align: right;
        }
        .stats h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #374151;
        }
        .stat-item {
            display: inline-block;
            margin: 0 5px;
            text-align: center;
            background-color: #eff6ff;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #dbeafe;
        }
        .stat-number {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            display: block;
        }
        .stat-label {
            font-size: 8px;
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #374151;
            font-size: 8px;
        }
        .session-type {
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 7px;
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
            font-size: 8px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
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
            font-size: 8px;
        }
        .location {
            color: #9ca3af;
            font-size: 7px;
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

    <div class="student-info">
        <h3>Informations étudiant</h3>
        <p><strong>Nom:</strong> {{ $etudiant->nom }}</p>
        <p><strong>Prénom:</strong> {{ $etudiant->prenom }}</p>
        <p><strong>Email:</strong> {{ $etudiant->email ?? 'Non renseigné' }}</p>
        <p><strong>Classe:</strong> {{ $etudiant->classe->nom ?? 'Non définie' }}</p>
    </div>

    <div class="stats">
        <h3>Statistiques</h3>
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
        <div style="text-align: center; padding: 20px; color: #6b7280;">
            <h3 style="margin: 0; font-size: 12px;">Aucune session cette semaine</h3>
            <p style="margin: 5px 0; font-size: 9px;">Aucune session de cours n'est programmée pour cette semaine.</p>
        </div>
    @endif

    <div class="footer">
        <p>Document généré automatiquement par le système de gestion académique | © {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>
