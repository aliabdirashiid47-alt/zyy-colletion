ZY COLLECTION — PHP / MySQL clothing store starter project
===========================================================

FEATURES
- Responsive modern luxury storefront for men and women
- Product categories, search, product details and shopping bag
- Checkout with Zaad or eDahab method and transaction reference
- Order confirmation page
- Admin login, dashboard, product CRUD/image upload and order management
- Payment verification and order status management
- PDO prepared statements, password hashing, session cart and CSRF tokens

REQUIREMENTS
- XAMPP (Apache + MySQL)
- PHP 8.0+ recommended
- MySQL / MariaDB

INSTALLATION
1. Extract the folder "zy_collection" into C:\xampp\htdocs\
2. Start Apache and MySQL from XAMPP Control Panel.
3. Open http://localhost/phpmyadmin
4. Import database.sql.
5. Check config.php. Default XAMPP values are DB user=root and blank password.
6. Open http://localhost/zy_collection/create_admin.php ONCE.
7. Admin login:
   URL: http://localhost/zy_collection/admin/login.php
   Email: admin@zycollection.local
   Password: ChangeMe123!
8. Immediately delete create_admin.php after creating the admin.
9. Log in and change the default admin password by updating it through a secure password-reset workflow before public deployment. This starter intentionally does not expose a public password-change page.
10. Open http://localhost/zy_collection/

IMPORTANT PAYMENT NOTE
Zaad/eDahab are represented as manual mobile-money payment methods in this starter.
The customer transfers money to the real store number that you must provide, then enters
the transaction reference. The admin manually verifies it in the dashboard.
This is NOT a direct API integration or automatic payment gateway. Never claim an order
is paid until you independently verify the transfer.

BEFORE GOING LIVE
- Replace the sample products and placeholder images with your own.
- Add the store's real Zaad/eDahab numbers and clear transfer instructions.
- Set a strong unique admin password; remove create_admin.php.
- Use HTTPS, secure session cookie settings, backups, server-side upload restrictions,
  rate limiting, logging, privacy/return policies and delivery fees.
- Configure production database credentials and disable displayed PHP errors.
- Test a full order, stock update, payment verification and cancellation.
- Confirm local legal, tax and consumer requirements.

The sample catalog and images are placeholders, not actual inventory.
