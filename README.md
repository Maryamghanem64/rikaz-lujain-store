Rikaz × Lujain — Silver Jewelry E-Commerce Store

Arabic-first RTL e-commerce platform developed for Rikaz × Lujain, a silver jewelry store focused on handcrafted silver pieces and Yemeni agate.

The platform combines two collections:

- Rikaz — Men's jewelry
- Lujain — Women's jewelry

Live Website

https://rikaz-lujain-store-production.up.railway.app/

Features

Customer Side

- Arabic RTL responsive interface
- Rikaz and Lujain collections
- Product categories
- Product search and filtering
- Product details and image gallery
- Shopping cart
- Guest checkout
- Delivery zones across Lebanon
- Cash payment
- Whish Money payment
- Payment receipt upload
- Order confirmation
- Order tracking
- WhatsApp contact
- Mobile, tablet, and desktop responsive design

Admin Panel

- Secure admin authentication
- Dashboard
- Product management
- Category management
- Product image management
- Order management
- Delivery zone management
- Delivery fee management
- Payment receipt verification
- Inventory management
- Store settings

Inventory System

Each jewelry piece can have its own:

- Stone
- Silver purity
- Size
- Price
- Quantity
- Images

The system uses inventory reservation and database transactions to reduce the risk of selling the same unique jewelry piece twice.

Payment Methods

Cash

Customers can place an order and pay when receiving their order.

Whish Money

Customers can:

1. Select Whish Money during checkout.
2. View the payment information.
3. Upload the payment receipt.
4. Wait for admin verification.
5. Receive order confirmation after verification.

Tech Stack

Backend

- Laravel
- PHP
- MySQL

Frontend

- Blade
- Tailwind CSS
- Alpine.js
- Arabic RTL UI

Media

- Cloudinary

Deployment

- Railway

Architecture

The project separates controllers from business logic using dedicated services.

Main services include:

- "CartService"
- "OrderService"
- "InventoryService"
- "PaymentProofService"
- "ImageService"

This keeps the application easier to maintain, test, and extend.

Security & Data Integrity

The platform includes:

- Server-side price calculation
- Database transactions
- Inventory reservation
- Row locking for inventory operations
- CSRF protection
- Admin-protected routes
- Laravel Form Request validation
- Payment receipt validation
- Secure environment variables
- Production configuration with debug mode disabled

Installation

Clone the repository:

git clone https://github.com/Maryamghanem64/rikaz-lujain-store.git
cd rikaz-lujain-store

Install dependencies:

composer install
npm install

Create the environment file:

cp .env.example .env

Generate the application key:

php artisan key:generate

Configure your database inside ".env".

Then run:

php artisan migrate
npm run dev
php artisan serve

Open:

http://127.0.0.1:8000

Project Scope

The current MVP focuses on:

- Product browsing
- Cart
- Checkout
- Orders
- Payments
- Delivery
- Inventory
- Admin management

Possible future improvements include:

- Customer accounts
- Favorites
- Reviews
- Coupons
- Loyalty system
- Online card payments
- Automated WhatsApp integration
- Multi-language support

GitHub Repository

https://github.com/Maryamghanem64/rikaz-lujain-store

Project Type

Real-world client e-commerce project developed to manage products, orders, payments, delivery, and inventory for Rikaz × Lujain.

---

Built with Laravel for Rikaz × Lujain.