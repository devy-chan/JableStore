# JableStore Production Deployment

## 1. Database
1. Create a MySQL/MariaDB database and database user in your hosting control panel.
2. Import `database/store.sql` using phpMyAdmin.
3. Give the database user full privileges on the JableStore database.

## 2. Database configuration
On the server, copy:

`backend/config/db_connect.local.example.php`

to:

`backend/config/db_connect.local.php`

Then enter the real hosting database credentials and production URL:

```php
$localhost = 'localhost';
$username  = 'YOUR_DATABASE_USER';
$password  = 'YOUR_DATABASE_PASSWORD';
$dbname    = 'YOUR_DATABASE_NAME';
$store_url = 'https://yourdomain.com/';
```

Do not commit `db_connect.local.php` to GitHub.

## 3. Upload
Upload the contents of the JableStore folder to the web root (`public_html` on many hosts). Do not upload `.git` or `.vscode`.

## 4. Test
- `https://yourdomain.com/`
- `https://yourdomain.com/backend/admin/login.php`
- Test products, images, customers and orders.

## 5. Password migration
The original project used MD5 passwords. This version accepts an existing MD5 password once and automatically upgrades it to `password_hash()` after successful login. Newly created or changed passwords use `password_hash()` immediately.

The bundled `database/store.sql` still contains the original demo admin hash. Change the admin password immediately after the first successful login.

## 6. HTTPS
Enable SSL/HTTPS on the hosting account before using real customer or order data. Keep `store_url` set to the HTTPS URL.
