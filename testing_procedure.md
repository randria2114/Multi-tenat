# Procédure de Test - Central Administration & Tenants

Ce guide explique comment tester les fonctionnalités d'administration centrale et l'accès aux tenants.

## 1. Prérequis

Avant de commencer, assurez-vous d'avoir créé au moins un utilisateur administrateur dans la base de données centrale.

**Création via la commande Artisan :**

1. **Option 1 : Interactive (par défaut)**
   ```bash
   php artisan make:admin admin@example.com
   ```
   L'invite vous demandera le nom et le mot de passe.

2. **Option 2 : Via le fichier .env**
   Configurez d'abord votre `.env` :
   ```env
   ADMIN_NAME="Administrator"
   ADMIN_EMAIL="admin@example.com"
   ADMIN_PASSWORD="password"
   ADMIN_ROLE="admin"
   ```
   Puis lancez :
   ```bash
   php artisan make:admin --from-env
   ```

**Note:** Si vous avez une erreur indiquant que la table `users` n'existe pas, lancez d'abord les migrations :
```bash
php artisan migrate
```

---

## 2. Administration Centrale (API Centrale)

L'URL de base est généralement `http://localhost/api` (ou votre domaine configuré).

### A. Authentification

1. **Login**
   - **Endpoint:** `POST /auth/login`
   - **Body:**
     ```json
     {
       "email": "admin@example.com",
       "password": "password"
     }
     ```
   - **Commande cURL:**
     ```bash
     curl -X POST http://localhost/api/auth/login -H "Content-Type: application/json" -d '{"email":"admin@example.com", "password":"password"}'
     ```
   - **Note:** Récupérez le `token` dans la réponse pour les étapes suivantes. (Le token JWT est composé de trois parties séparées par des points).

2. **Me (Vérifier le profil)**
   - **Endpoint:** `GET /auth/me`
   - **Header:** `Authorization: Bearer [TOKEN]`

3. **Logout**
   - **Endpoint:** `POST /auth/logout`
   - **Header:** `Authorization: Bearer [TOKEN]`

---

### B. Gestion des Modules (CRUD)

1. **Lister les modules**
   - **Endpoint:** `GET /modules`
   - **Header:** `Authorization: Bearer [TOKEN]`

2. **Créer un module**
   - **Endpoint:** `POST /modules`
   - **Body:**
     ```json
     {
       "name": "Nouveau Module",
       "slug": "nouveau-module",
       "description": "Une description"
     }
     ```

---

### C. Gestion des Plans (CRUD)

1. **Lister les plans**
   - **Endpoint:** `GET /plans`
   - **Header:** `Authorization: Bearer [TOKEN]`

2. **Créer un plan**
   - **Endpoint:** `POST /plans`
   - **Body:**
     ```json
     {
       "name": "Pro Plan",
       "price": 49.99,
       "billing_cycle": "monthly",
       "max_users": 10,
       "module_ids": [1, 2]
     }
     ```

---

### D. Gestion des Tenants (Clients)

1. **Lister les Tenants**
   - **Endpoint:** `GET /tenants`
   - **Header:** `Authorization: Bearer [TOKEN]`

2. **Détails d'un Tenant**
   - **Endpoint:** `GET /tenants/{id}`
   - **Header:** `Authorization: Bearer [TOKEN]`

3. **Créer un Tenant**
   - **Endpoint:** `POST /tenants`
   - **Body:**
     ```json
     {
       "id": "client1",
       "subdomain": "client1",
       "plan_id": 1
     }
     ```
   - **Effet:** Cela crée une base de données séparée pour `client1` et configure le domaine `client1.localhost`.

4. **Modifier un Tenant**
   - **Endpoint:** `PUT /tenants/{id}`
   - **Body:**
     ```json
     {
       "subdomain": "nouveau-sous-domaine",
       "plan_id": 2
     }
     ```

5. **Supprimer un Tenant**
   - **Endpoint:** `DELETE /tenants/{id}`
   - **Header:** `Authorization: Bearer [TOKEN]`

6. **Prolonger un abonnement**
   - **Endpoint:** `POST /tenants/{id}/extend`
   - **Body:** `{"months": 12}`

7. **Changer de plan (Spécialisé)**
   - **Endpoint:** `POST /tenants/{id}/plan`
   - **Body:** `{"plan_id": 2}`

---

## 3. Accès au Tenant (API Tenant)

Une fois le tenant créé (ex: `client1`), vous pouvez tester l'API spécifique au tenant.

- **URL de base:** `http://client1.localhost/api`

1. **Test d'accueil Tenant**
   - **Endpoint:** `GET /`
   - **Réponse attendue:** `{ "message": "This is your multi-tenant application.", "tenant": "client1" }`

2. **Test de module (ex: SMS)**
   - **Endpoint:** `GET /sms/logs`
   - **Note:** Ce module ne sera accessible que si le plan du tenant contient le module `sms`.

---

## 4. Astuces pour les tests

- **Erreur 401 Unauthenticated:** Vérifiez que le `Bearer token` est bien présent et n'a pas expiré.
- **Domaines .localhost:** Sous Windows, les sous-domaines `.localhost` fonctionnent généralement sans modification du fichier `hosts`.
- **Base de données:** Vérifiez la table `tenants` et `domains` dans la base centrale pour confirmer la création.
