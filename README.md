# Système de Gestion de Présence Académique

## 📝 Description
Ce projet est une application web développée avec Laravel pour gérer la présence des étudiants dans un contexte académique. Il permet de suivre les présences, gérer les cours, les enseignants, les étudiants et les notifications en temps réel.

## 🚀 Fonctionnalités Principales

### Gestion des Utilisateurs
- Multi-rôles : Administrateurs, Enseignants, Coordinateurs, Étudiants, Parents
- Authentification sécurisée
- Gestion des profils utilisateurs

### Gestion Académique
- Suivi des années académiques
- Organisation par semestres
- Gestion des promotions et des classes
- Suivi des matières et des cours

### Gestion des Présences
- Enregistrement des présences en temps réel
- Justification des absences
- Historique des présences
- Notifications automatiques

### Fonctionnalités Avancées
- Système de notification personnalisé
- Interface utilisateur intuitive avec DaisyUI
- Gestion des sessions de cours
- Suivi des étudiants ayant abandonné des matières

## 🛠 Technologies Utilisées

- **Backend:** Laravel 10.x
- **Frontend:** 
  - Tailwind CSS
  - DaisyUI
  - Vite
- **Base de données:** MySQL
- **Authentification:** Laravel Breeze

## 📋 Prérequis

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL

## ⚙️ Installation

1. Cloner le repository
```bash
git clone https://github.com/Floriannnn10/Soutenance_francaiseB3dev.git
cd Soutenance_francaiseB3dev
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Installer les dépendances JavaScript
```bash
npm install
```

4. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurer la base de données dans le fichier .env

6. Migrer la base de données
```bash
php artisan migrate --seed
```

7. Compiler les assets
```bash
npm run dev
```

8. Démarrer le serveur
```bash
php artisan serve
```

## 📚 Structure du Projet

### Models
- `User.php` - Gestion des utilisateurs
- `AnneeAcademique.php` - Années académiques
- `Classe.php` - Classes d'étudiants
- `Matiere.php` - Matières enseignées
- `Presence.php` - Enregistrement des présences
- `SessionDeCours.php` - Sessions de cours
- Et plus...

### Controllers
Situés dans `app/Http/Controllers/`

### Migrations
Situées dans `database/migrations/`

### Vues
Situées dans `resources/views/`

## 🔒 Sécurité

- Validation des emails avec règles personnalisées
- Middleware d'authentification
- Protection CSRF
- Validation des données

## 👥 Rôles Utilisateurs

1. **Administrateur**
   - Gestion complète du système
   - Configuration des années académiques
   - Gestion des utilisateurs

2. **Coordinateur**
   - Supervision des cours
   - Gestion des emplois du temps
   - Suivi des présences

3. **Enseignant**
   - Gestion des présences
   - Création des sessions de cours
   - Suivi des étudiants

4. **Étudiant**
   - Consultation des présences
   - Soumission des justifications d'absence
   - Réception des notifications

5. **Parent**
   - Suivi des présences de leur(s) enfant(s)
   - Réception des notifications

## 📊 Monitoring et Notifications

- Système de notification en temps réel
- Suivi des absences
- Alertes automatiques aux parents

## 📝 License

Ce projet est sous license privée.

## 👥 Équipe

- BANGA ADOU GEORGES EMMANUEL
- FLORIAN
- [Autres membres de l'équipe]

## 🤝 Contribution

Pour contribuer à ce projet :
1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📞 Support

Pour toute question ou support, veuillez contacter l'équipe de développement.

