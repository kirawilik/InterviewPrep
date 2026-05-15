# 🎯 InterviewPrep

> Application Laravel personnelle pour structurer et suivre sa préparation aux entretiens techniques.

---

## 📖 Présentation

**InterviewPrep** est une application web développée avec Laravel, conçue pour aider les développeurs à organiser leurs connaissances techniques avant un entretien. Elle permet de structurer les révisions par domaine, de suivre son niveau de maîtrise concept par concept, et de générer des questions d'entretien réalistes grâce à l'API Groq.

### Contexte

Un développeur web marocain, fraîchement sorti de formation, décroche un entretien technique dans dix jours avec une startup SaaS à Casablanca. Il sait beaucoup de choses — mais de façon éparpillée, sans structure claire. InterviewPrep est l'outil qu'il lui fallait.

---

## ✨ Fonctionnalités

### 🔐 Authentification
- Inscription, connexion et déconnexion sécurisées
- Données isolées par utilisateur

### 🗂️ Gestion des domaines techniques
- Créer des domaines (ex : Laravel ORM, PHP OOP, MySQL, API REST)
- Attribuer une couleur de badge pour identification visuelle
- Voir la progression par domaine : nombre de concepts total vs maîtrisés
- Modifier ou supprimer un domaine

### ✍️ Gestion des concepts
- Créer un concept dans un domaine avec :
  - **Titre** : nom du concept technique (ex : "Eloquent N+1 Problem")
  - **Explication** : rédigée dans ses propres mots
  - **Niveau de difficulté** : Junior / Mid / Senior
  - **Statut** : À revoir / En cours / Maîtrisé
- Filtrer les concepts par statut depuis la liste
- Changer de statut en un clic depuis la liste (sans ouvrir le formulaire)
- Modifier ou supprimer un concept
- Voir le détail complet d'un concept avec ses questions générées

### 🤖 Génération AI de questions d'entretien (via Groq)
- Générer 5 questions d'entretien techniques réalistes par concept
- Basé sur le titre et l'explication du concept
- Historique de toutes les générations passées avec leur date
- Suppression d'un lot de questions inutile

---

## 🛠️ Stack technique

| Couche | Technologie |
|---|---|
| Backend | Laravel 11 |
| Base de données | MySQL |
| Frontend | Blade + Tailwind CSS |
| API AI | [Groq API](https://console.groq.com) |
| Appel HTTP | `Http::` facade native Laravel |
| Auth | Laravel Breeze |

---

## ⚙️ Installation

### Prérequis

- PHP >= 8.2
- Composer
- MySQL
- Node.js & npm
- Une clé API Groq (gratuite sur [console.groq.com](https://console.groq.com))

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/votre-username/interview-prep.git
cd interview-prep

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install && npm run build

# 4. Copier le fichier d'environnement
cp .env.example .env

# 5. Générer la clé applicative
php artisan key:generate

# 6. Configurer la base de données dans .env
# DB_DATABASE=interview_prep
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Ajouter la clé API Groq dans .env
# GROQ_API_KEY=votre_cle_groq
# GROQ_MODEL=llama-3.1-8b-instant

# 8. Lancer les migrations
php artisan migrate

# 9. Démarrer le serveur
php artisan serve
```

L'application est accessible sur `http://localhost:8000`.

---

## 🔑 Configuration de l'API Groq

Dans votre fichier `.env` :

```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxx
GROQ_MODEL=llama-3.1-8b-instant
```

> ⚠️ Ne jamais committer votre clé API. Le fichier `.env` est listé dans `.gitignore`.

**Modèles Groq recommandés :**

| Modèle | Usage |
|---|---|
| `llama-3.1-8b-instant` | Rapide, idéal pour la génération de questions |
| `llama-3.3-70b-versatile` | Plus puissant, réponses plus riches |

---

## 🗄️ Structure de la base de données

```
users
├── id, name, email, password, timestamps

domains
├── id, user_id, name, color, timestamps

concepts
├── id, domain_id, user_id
├── title, explanation
├── difficulty (junior|mid|senior)
├── status (to_review|in_progress|mastered)
├── timestamps

question_generations
├── id, concept_id, user_id
├── questions (JSON — tableau de 5 questions)
├── timestamps
```

---

## 📁 Structure du projet

```
interview-prep/
├── app/
│   ├── Http/Controllers/
│   │   ├── DomainController.php
│   │   ├── ConceptController.php
│   │   └── QuestionGenerationController.php
│   └── Models/
│       ├── Domain.php
│       ├── Concept.php
│       └── QuestionGeneration.php
├── resources/views/
│   ├── domains/
│   ├── concepts/
│   └── generations/
├── specs/                  # Spécifications par feature (workflow AI)
├── AGENTS.md               # Instructions pour les coding agents
├── .env.example
└── README.md
```
## 🗄️ Base de données

### Modèle Conceptuel (MCD)

![MCD](docs/mcd.png)

### Modèle Logique (MLD)

![MLD](docs/mld.png)
---

## 🤖 Workflow AI-Assisted

Ce projet a été développé avec l'assistance de coding agents (OpenCode / Claude Code).

Les règles du workflow sont définies dans [`AGENTS.md`](./AGENTS.md) à la racine du projet.

### Conventions

- Chaque feature dispose d'un fichier de spec dans `specs/` (ex : `specs/domains.md`)
- Les coding agents sont utilisés en **mode Plan avant mode Build** pour chaque feature
- Les commits mentionnent explicitement l'usage AI (ex : `feat(concepts): add status toggle [AI-assisted]`)

---

## 📋 User Stories couvertes

| # | Feature | Statut |
|---|---|---|
| US1 | Inscription / Connexion / Déconnexion | ✅ |
| US2 | Liste des domaines avec progression | ✅ |
| US3 | Créer un domaine | ✅ |
| US4 | Modifier / Supprimer un domaine | ✅ |
| US5 | Liste des concepts avec filtre par statut | ✅ |
| US6 | Créer un concept | ✅ |
| US7 | Voir le détail d'un concept | ✅ |
| US8 | Modifier un concept | ✅ |
| US9 | Changer le statut rapidement | ✅ |
| US10 | Supprimer un concept | ✅ |
| US11 | Générer des questions d'entretien (Groq) | ✅ |
| US12 | Voir l'historique des générations | ✅ |
| US13 | Supprimer une génération | ✅ |

---

## 🚨 Gestion des erreurs API

Si l'API Groq ne répond pas ou retourne une erreur :
- Un message d'erreur clair est affiché à l'utilisateur
- Aucune page blanche ou exception non gérée
- Le résultat est sauvegardé en base **avant** affichage

---

## 🔒 Sécurité

- Toutes les routes sont protégées par le middleware `auth`
- Chaque ressource est liée à `user_id` — un utilisateur ne peut accéder qu'à ses propres données
- La clé API Groq est stockée uniquement dans `.env`, jamais dans le code source

---

## 📄 Licence

Usage personnel — projet de formation.