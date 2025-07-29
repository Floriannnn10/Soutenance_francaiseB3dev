# Comptes de Test - Système Académique IFRAN

## 🔐 Informations de Connexion

**Mot de passe par défaut pour tous les comptes :** `password`

## 👨‍💼 Administrateurs

| Email | Nom | Prénom | Rôle |
|-------|-----|--------|------|
| `admin@ifran.ci` | Admin | Système | Administrateur |

## 👨‍🏫 Enseignants

| Email | Nom | Prénom | Rôle |
|-------|-----|--------|------|
| `florian@ifran.ci` | Banga | Florian | Enseignant |
| `jean.dupont@ifran.ci` | Dupont | Jean | Enseignant |
| `marie.martin@ifran.ci` | Martin | Marie | Enseignant |
| `pierre.bernard@ifran.ci` | Bernard | Pierre | Enseignant |

## 👨‍🎓 Étudiants

| Email | Nom | Prénom | Classe |
|-------|-----|--------|--------|
| `miyah.konan@ifran.ci` | Konan | Miyah | B3 DEV A |
| `aissatou.bamba@ifran.ci` | Bamba | Aissatou | B3 DEV B |
| `fatou.kouassi@ifran.ci` | Kouassi | Fatou | B3 CYBER A |
| `moussa.traore@ifran.ci` | Traore | Moussa | B3 CYBER B |
| `aminata.diabate@ifran.ci` | Diabate | Aminata | M1 DEV A |

## 👨‍👩‍👧‍👦 Parents

| Email | Nom | Prénom | Étudiant Associé |
|-------|-----|--------|------------------|
| `parent.miyah.konan@ifran.ci` | Konan | Parent de Miyah | Miyah Konan |
| `parent.aissatou.bamba@ifran.ci` | Bamba | Parent d'Aissatou | Aissatou Bamba |
| `parent.fatou.kouassi@ifran.ci` | Kouassi | Parent de Fatou | Fatou Kouassi |

## 👨‍💻 Coordinateurs

| Email | Nom | Prénom | Promotion |
|-------|-----|--------|-----------|
| `sophie.bernard@ifran.ci` | Bernard | Sophie | B3 DEV |
| `michel.dubois@ifran.ci` | Dubois | Michel | B3 CYBER |
| `claire.moreau@ifran.ci` | Moreau | Claire | M1 DEV |

## 🧪 Données de Test

### Sessions de Cours
- **Développement Web** (Présentiel) - Programmée
- **Base de données** (Présentiel) - En cours
- **Sécurité informatique** (E-learning) - Terminée
- **Intelligence artificielle** (Workshop) - Planifiée

### Présences
- Présences aléatoires créées pour chaque session
- Statuts : Présent, Absent, Justifié, Retard

## 🚀 Instructions d'Utilisation

1. **Lancer les migrations et seeders :**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Se connecter avec n'importe quel compte ci-dessus**

3. **Tester les différentes fonctionnalités selon le rôle :**
   - **Admin :** Gestion complète du système
   - **Enseignant :** Gestion des sessions et présences
   - **Étudiant :** Consultation des cours et présences
   - **Parent :** Suivi des enfants
   - **Coordinateur :** Gestion de la promotion

## 📝 Notes Importantes

- Tous les comptes utilisent le mot de passe : `password`
- Les données sont réinitialisées à chaque `migrate:fresh --seed`
- Les relations entre utilisateurs et profils sont automatiquement créées
- Les sessions et présences sont générées avec des données réalistes
