# Laravel Multi-Tenant SaaS (JWT & Stancl Tenancy)

Ce projet est une plateforme SaaS multi-tenant robuste construite avec Laravel, utilisant **Stancl Tenancy** pour l'isolation des bases de données et **JWT** pour l'authentification sécurisée de l'API centrale.

## 🚀 Fonctionnalités
- **Isolation Totale** : Une base de données par client (tenant).
- **Gestion Centrale** : Administration des clients, des forfaits (Plans) et des modules.
- **Authentification JWT** : Sécurisation de l'API centrale via JSON Web Tokens.
- **Système de Modules** : Activation/Désactivation de fonctionnalités par client selon leur forfait.

---

## 🛠️ Installation

### 1. Prérequis
- PHP 8.1+
- Composer
- Serveur MySQL (Laragon recommandé sur Windows)

### 2. Clonage et Dépendances
```bash
git clone https://github.com/randria2114/Multi-tenat.git
cd Multi-tenat
composer install
```

### 3. Configuration de l'environnement
1. Copiez le fichier `.env.example` en `.env`.
2. Configurez vos accès à la base de données (Base centrale).
3. Générez les clés de sécurité :
```bash
php artisan key:generate
php artisan jwt:secret
```

### 4. Base de données
Exécutez les migrations centrales :
```bash
php artisan migrate
```

### 5. Création de l'Administrateur
Utilisez la commande personnalisée pour créer le premier administrateur à partir des variables définies dans votre `.env` :
```bash
php artisan make:admin --from-env
```

---

## 🧪 Guide de Test (API)

### Point d'entrée Central (`http://localhost:8000/api`)
Toutes les requêtes d'administration doivent inclure le header : `Authorization: Bearer <votre_token>`.

1. **Login** : `POST /auth/login` (Récupérez le token).
2. **Créer un Client** : `POST /tenants`
   ```json
   {
     "id": "client1",
     "subdomain": "client1.localhost",
     "plan_id": 1
   }
   ```
3. **Lister les Clients** : `GET /tenants`.

### Point d'entrée Client (`http://{subdomain}.localhost:8000/api`)
Chaque client dispose de son propre domaine.
- **Accès** : `http://client1.localhost:8000/api`
- **Réponse attendue** : Un JSON confirmant l'identification du tenant et son isolation.

---

## 🛡️ Sécurité
Les domaines en `.localhost` pointent automatiquement vers votre machine locale. Pour des noms de domaines personnalisés, n'oubliez pas de mettre à jour votre fichier `hosts` :
- **Windows** : `C:\Windows\System32\drivers\etc\hosts` (en mode Admin).
- **macOS / Linux** : `/etc/hosts` (utilisez `sudo nano /etc/hosts` dans le terminal).
