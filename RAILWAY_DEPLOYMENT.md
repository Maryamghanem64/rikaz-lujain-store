# Railway deployment preparation

This repository is prepared for a stateless Railway Laravel application. This document is a deployment checklist; it does not indicate that a deployment or any Railway resource already exists.

## Architecture

- One Laravel application service, detected and served by Railway/Railpack.
- One Railway MySQL service with its managed persistent database storage.
- One private Railway Bucket shared by two Laravel disks with isolated prefixes:
  - `railway_products` -> `products/`
  - `railway_payment_proofs` -> `payment-proofs/`
- One short-lived Railway cron service for expired reservation release.
- No Redis, queue worker, media server, or application volume is currently required.

The bucket remains private. Product images are streamed through the public `media.products.show` application route. Whish receipts are streamed only through the authenticated Admin order/proof route.

## 1. Create the project resources

1. Create a Railway project.
2. Add a MySQL service.
3. Add one Railway Storage Bucket in the desired region.
4. Add the GitHub repository as the Laravel application service.
5. Do **not** attach a Railway volume to the Laravel service for `storage/app`; runtime uploads belong in the bucket.

## 2. Application variables

Set the following on the Laravel application service. Replace `MySQL` and `Bucket` in reference expressions if the services use different names.

```dotenv
APP_NAME="Rikaz x Lujain"
APP_ENV=production
APP_DEBUG=false
APP_KEY=<unique production key generated securely>
APP_URL=https://<generated-or-custom-domain>

DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stderr
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter

PRODUCT_IMAGE_DISK=railway_products
PAYMENT_PROOF_DISK=railway_payment_proofs
RAILWAY_BUCKET_ACCESS_KEY_ID=${{Bucket.ACCESS_KEY_ID}}
RAILWAY_BUCKET_SECRET_ACCESS_KEY=${{Bucket.SECRET_ACCESS_KEY}}
RAILWAY_BUCKET_REGION=${{Bucket.REGION}}
RAILWAY_BUCKET_NAME=${{Bucket.BUCKET}}
RAILWAY_BUCKET_ENDPOINT=${{Bucket.ENDPOINT}}
RAILWAY_BUCKET_USE_PATH_STYLE_ENDPOINT=false

RIKAZ_ADMIN_NAME=<real name>
RIKAZ_ADMIN_EMAIL=<real email>
RIKAZ_ADMIN_PASSWORD=<strong unique password>
LUJAIN_ADMIN_NAME=<real name>
LUJAIN_ADMIN_EMAIL=<real email>
LUJAIN_ADMIN_PASSWORD=<strong unique password>
```

Use Railway sealed variables for credentials where appropriate. Never commit a production `APP_KEY`, database password, bucket secret, or Admin password. Generate a production key separately with `php artisan key:generate --show`; do not replace the local development key.

Railway Buckets use virtual-hosted-style endpoints by default, so path-style access remains `false` unless the Bucket Credentials screen explicitly says otherwise.

## 3. Build and pre-deploy commands

Railway can detect Laravel and serve it with php-fpm and Caddy; no Dockerfile is required for this project.

Configure the application service:

- Custom build command: `npm run build`
- Pre-deploy command: `sh railway/init-app.sh`
- Start command: leave automatic Laravel/Railpack detection in place
- Healthcheck path: `/up`

The pre-deploy script runs only safe, repeatable production commands:

- `php artisan migrate --force`
- Laravel cache clearing and production cache compilation

It does not run `migrate:fresh`, seeders, cleanup, or Admin creation.

## 4. Initial production seed data

Run these only after reviewing the new production database and setting real Admin environment credentials:

```sh
php artisan db:seed --class=SectionCategorySeeder --force
php artisan db:seed --class=SettingSeeder --force
php artisan db:seed --class=AdminUserSeeder --force
```

Run the Admin seeder deliberately, not automatically on every deployment. Do not run product, order, or development/test seeders.

## 5. Cron service

Create a second service from the same repository and configure it as a Railway Cron Job:

- Start command: `sh railway/run-cron.sh`
- Cron schedule: `0 * * * *`
- Variables: reference the same application `APP_KEY` and MySQL variables
- Public domain: none

The script directly runs the sole hourly reservation command and exits. It does not keep an idle scheduler process alive. Railway cron schedules use UTC.

No queue worker service is required because the application currently dispatches no queued jobs. Keep `QUEUE_CONNECTION=database` for compatibility; add a worker only when real queued work is introduced.

## 6. Domain, HTTPS, and uploads

1. Generate the Railway public domain for the application service.
2. Set `APP_URL` to the exact HTTPS URL.
3. Keep secure session cookies enabled.
4. Verify generated links and redirects use HTTPS behind Railway's proxy before attaching a custom domain.
5. Configure PHP with `upload_max_filesize >= 8M` and `post_max_size >= 10M`.
6. Confirm any Railway/proxy request-size limit permits the 5 MB application upload limit.

No wildcard trusted-proxy override is committed. Add one only if deployment testing proves Railway's detected Laravel runtime does not honor its forwarded HTTPS headers.

## 7. Storage acceptance tests

After the first deployment:

1. Upload a disposable product image from the correct brand Admin.
2. Confirm the database key has no local path and the bucket object is under `products/` exactly once.
3. Confirm storefront and Admin product pages load it through `/media/products/{id}`.
4. Create a disposable Whish order and upload a receipt.
5. Confirm its object is under `payment-proofs/` exactly once.
6. Confirm Rikaz Admin and Lujain Admin can inspect the shared receipt.
7. Confirm a guest is redirected to Admin login and a non-admin receives 403.
8. Confirm there is no permanent public receipt or bucket URL.
9. Redeploy the application and repeat the product/receipt access checks.
10. Delete the disposable product image and confirm its bucket object is removed.

Static branding, logos, editorial images, Vite assets, and favicon remain source-controlled and are not uploaded to the Railway Bucket.

## 8. Backups and launch checks

- Enable and verify MySQL backups.
- Define a backup/retention policy for both bucket prefixes, especially private receipts.
- Run `php artisan about`, `route:list`, `schedule:list`, and the automated test suite against the release configuration.
- Verify `/up`, storefront navigation, checkout methods, Admin authorization, shared orders, receipt verify/reject, and reservation expiry.
- Review Railway stderr logs after the smoke test.
