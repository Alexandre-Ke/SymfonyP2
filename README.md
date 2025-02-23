
# Projet SymfonyP2

Ce projet est une application Symfony permettant de gérer :
- Les utilisateurs avec un système de rôles (Administrateur, Manager, Utilisateur).
- Les produits avec un système d'exportation en CSV.
- Les clients avec des permissions restreintes en fonction des rôles.

---

## **Installation du Projet**

### **Prérequis**
- **PHP 8.1+** avec les extensions **mysqli** et **pdo_mysql** activées.
- **Composer** (gestionnaire de dépendances PHP).
- **Symfony CLI** (facultatif mais recommandé).
- **MySQL** ou toute autre base de données compatible avec Doctrine.

### **1. Cloner le dépôt GitHub**
```bash
git clone https://github.com/votre-utilisateur/votre-repo.git
cd votre-repo
```

### **2. Installer les dépendances**
```bash
composer install
```

### **3. Configurer l'environnement**
Copiez le fichier **`.env`** par défaut et personnalisez-le :
```bash
cp .env .env.local
```

Configurez la connexion à la base de données dans **`.env.local`** :
```env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
```

### **4. Créer la base de données et appliquer les migrations**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### **5. Charger les données de test (Fixtures)**
```bash
php bin/console doctrine:fixtures:load
```

### **6. Lancer le serveur Symfony**
```bash
symfony server:start
# Ou avec le serveur PHP intégré
php -S 127.0.0.1:8000 -t public
```

Ouvrez votre navigateur à l'adresse :
```http://127.0.0.1:8000```

---

## **Fonctionnalités Implémentées**

### **Gestion des Utilisateurs**
- **Création, modification et suppression** d'utilisateurs.
- **Système de rôles** : Administrateur, Manager, Utilisateur.
- **Restrictions d'accès** basées sur les rôles (utilisation des Voters Symfony).

### **Gestion des Produits**
- **Ajout, modification et suppression** de produits.
- **Exportation des produits en fichier CSV**.
- **Tri des produits par prix** via l'interface utilisateur.

### **Gestion des Clients**
- **Visualisation, ajout et modification** des clients (administrateurs et managers uniquement).
- **Vérification des permissions via un Voter**.
- **Validation des données** lors de la création ou de la modification d'un client.

---

## **Exécution des Tests**

### **1. Lancer les tests avec PHPUnit**
```bash
php bin/phpunit
```

### **Tests disponibles :**
- **Tests unitaires** : Vérification du service (`ProductCsvExporter`).
- **Tests des entités** : Validation des données utilisateurs, produits et clients.
- **Tests des contrôleurs** : Simulation des requêtes HTTP et vérification des réponses.

### **Exemple de sortie attendue :**
```plaintext
OK (10 tests, 25 assertions)
```
