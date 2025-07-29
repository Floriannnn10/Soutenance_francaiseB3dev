# Structure des Données du Projet

## 📚 Années Académiques
- **2023-2024** : Année passée
- **2024-2025** : Année en cours

## 🎓 Promotions
- **B3 DEV** : Bac+3 Développement
- **B3 CYBER** : Bac+3 Cybersécurité  
- **M1 DEV** : Master 1 Développement
- **M1 CYBER** : Master 1 Cybersécurité

## 🏫 Classes
Chaque promotion a 2 classes (A et B) :
- B3 DEV A, B3 DEV B
- B3 CYBER A, B3 CYBER B
- M1 DEV A, M1 DEV B
- M1 CYBER A, M1 CYBER B

## 📖 Matières
1. **Développement Web PHP** : PHP et Laravel
2. **JavaScript et Frameworks** : React, Vue.js
3. **Programmation Java** : Java OOP
4. **Python pour le Data Science** : pandas, numpy, matplotlib
5. **Sécurité des applications** : Sécurité web
6. **DevOps et CI/CD** : Docker, Kubernetes, Jenkins
7. **Bases de données** : MySQL, PostgreSQL, MongoDB
8. **Architecture logicielle** : Design patterns, SOLID
9. **Intelligence artificielle** : Machine Learning, Deep Learning
10. **Cloud Computing** : AWS, Azure, Google Cloud

## 👥 Utilisateurs

### 🔐 Admin
- **Email** : `admin@ifran.ci`
- **Mot de passe** : `password`

### 👨‍🏫 Enseignants
- **Florian Banga** : `florian@ifran.ci` (password)
- **Jean Dupont** : `jean.dupont@ifran.ci` (password)
- **Marie Martin** : `marie.martin@ifran.ci` (password)
- **Pierre Bernard** : `pierre.bernard@ifran.ci` (password)
- **Sophie Dubois** : `sophie.dubois@ifran.ci` (password)
- **Claire Moreau** : `claire.moreau@ifran.ci` (password)

### 🎓 Étudiants
- **Miyah Konan** : `miyah.konan@ifran.ci` (password)
- **Aissatou Bamba** : `aissatou.bamba@ifran.ci` (password)
- **Fatou Kouassi** : `fatou.kouassi@ifran.ci` (password)
- **Moussa Traore** : `moussa.traore@ifran.ci` (password)
- **Aminata Diabate** : `aminata.diabate@ifran.ci` (password)
- **Kadidja Ouattara** : `kadidja.ouattara@ifran.ci` (password)
- **Kouassi Yao** : `kouassi.yao@ifran.ci` (password)
- **Fatima Kone** : `fatima.kone@ifran.ci` (password)

### 👨‍👩‍👧‍👦 Parents
- **Parent de Miyah** : `parent.miyah.konan@ifran.ci` (password)
- **Parent d'Aissatou** : `parent.aissatou.bamba@ifran.ci` (password)
- **Parent de Fatou** : `parent.fatou.kouassi@ifran.ci` (password)
- **Parent de Moussa** : `parent.moussa.traore@ifran.ci` (password)
- **Parent d'Aminata** : `parent.aminata.diabate@ifran.ci` (password)
- **Parent de Kadidja** : `parent.kadidja.ouattara@ifran.ci` (password)
- **Parent de Kouassi** : `parent.kouassi.yao@ifran.ci` (password)
- **Parent de Fatima** : `parent.fatima.kone@ifran.ci` (password)

### 👨‍💼 Coordinateurs
- **Sophie Bernard** : `sophie.bernard@ifran.ci` (password) - Promotion: B3 DEV
- **Michel Dubois** : `michel.dubois@ifran.ci` (password) - Promotion: B3 CYBER
- **Claire Moreau** : `claire.moreau@ifran.ci` (password) - Promotion: M1 DEV

## 📅 Sessions de Cours

### Structure des Sessions
Chaque session contient :
- **Matière** : Une des 10 matières disponibles
- **Enseignant** : Un enseignant assigné
- **Classe** : Une classe spécifique
- **Type de cours** : Présentiel, E-learning, Workshop
- **Statut** : Programmée, En cours, Terminée, Annulée
- **Horaires** : Date et heure de début/fin
- **Lieu** : Salle assignée
- **Notes** : Description de la session

### Répartition
- **3-5 sessions** par classe par semestre
- **2 semestres** par année académique
- **Sessions réparties** sur toute l'année académique

## 📝 Présences

### Statuts de Présence
- **Présent** : Étudiant présent
- **Absent** : Étudiant absent
- **Retard** : Étudiant en retard
- **Justifié** : Absence justifiée

### Enregistrement
- Chaque session a des présences pour tous les étudiants de la classe
- Les présences sont enregistrées par l'admin
- Statut aléatoire pour la démonstration

## 🔗 Associations

### Enseignant-Matière
- Chaque enseignant est associé à **2-4 matières**
- Associations logiques selon les compétences

### Étudiant-Classe
- Chaque étudiant est assigné à une classe spécifique
- Répartition équitable entre les classes

### Parent-Étudiant
- Chaque étudiant a un parent associé
- Email du parent basé sur l'email de l'étudiant

## 🎯 Logique Métier

### Hiérarchie
1. **Année Académique** → **Semestres**
2. **Promotion** → **Classes**
3. **Classe** → **Étudiants**
4. **Matière** → **Enseignants**
5. **Session** → **Présences**

### Contraintes
- Un étudiant ne peut être que dans une classe
- Un enseignant peut enseigner plusieurs matières
- Une session appartient à un semestre spécifique
- Les présences sont liées aux sessions et étudiants

## 🚀 Utilisation

### Connexion
```bash
# Admin
Email: admin@ifran.ci
Mot de passe: password

# Enseignant (exemple)
Email: florian@ifran.ci
Mot de passe: password

# Étudiant (exemple)
Email: miyah.konan@ifran.ci
Mot de passe: password
```

### Fonctionnalités Testées
- ✅ Gestion des utilisateurs et rôles
- ✅ Création et gestion des sessions
- ✅ Enregistrement des présences
- ✅ Associations enseignant-matière
- ✅ Navigation selon les rôles
- ✅ Dashboard personnalisé par rôle

## 📊 Statistiques

### Données Créées
- **2 années académiques**
- **4 promotions**
- **8 classes**
- **10 matières**
- **6 enseignants**
- **8 étudiants**
- **8 parents**
- **3 coordinateurs**
- **~200 sessions de cours**
- **~800 présences**

Cette structure permet de tester toutes les fonctionnalités du système de gestion de présence académique.
