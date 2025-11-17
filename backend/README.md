# Backend E-Commerce - Laravel API

## 🚀 Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## 📝 Comptes de test

**Admin:**
- Email: admin@ecom.com
- Password: password

**Client:**
- Email: client@ecom.com
- Password: password

## 🔗 Endpoints API

### Authentification
- POST `/api/register` - Inscription
- POST `/api/login` - Connexion
- POST `/api/logout` - Déconnexion (auth)
- GET `/api/me` - Profil utilisateur (auth)

### Produits
- GET `/api/products` - Liste des produits
- GET `/api/products/{id}` - Détail produit

### Catégories
- GET `/api/categories` - Liste des catégories
- GET `/api/categories/{id}` - Détail catégorie

### Panier (auth)
- GET `/api/cart` - Voir le panier
- POST `/api/cart/add` - Ajouter au panier
- PUT `/api/cart/{id}` - Modifier quantité
- DELETE `/api/cart/{id}` - Retirer du panier

### Commandes (auth)
- GET `/api/orders` - Mes commandes
- POST `/api/orders` - Créer une commande
- GET `/api/orders/{id}` - Détail commande

### Admin (auth + admin)
- POST `/api/admin/categories` - Créer catégorie
- PUT `/api/admin/categories/{id}` - Modifier catégorie
- DELETE `/api/admin/categories/{id}` - Supprimer catégorie
- POST `/api/admin/products` - Créer produit
- PUT `/api/admin/products/{id}` - Modifier produit
- DELETE `/api/admin/products/{id}` - Supprimer produit
- PUT `/api/admin/orders/{id}/status` - Modifier statut commande
- GET `/api/admin/dashboard` - Statistiques

## 🗄️ Base de données

Tables:
- users
- categories
- products
- product_images
- carts
- cart_items
- orders
- order_items
