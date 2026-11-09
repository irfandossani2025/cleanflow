# CleanFlow

CleanFlow is a focused Laravel SaaS for small residential cleaning businesses: **enquiry → quote → follow-up → booking**. It is server-rendered so it is suitable for conventional PHP hosting, including Plesk.

`cleanflow.techittechnologies.com` is a suggested subdomain only. DNS availability and trademark implications have **not** been checked.

## Included

- Public product, pricing, FAQ, contact, and business quote-request pages.
- Authentication, password reset, email verification, onboarding, and isolated business workspaces.
- Owner/admin, staff (assigned-job), and private-data-restricted platform-status roles.
- Enquiries, services, owner-approved quotes, secure customer response links, browser printable quote/PDF workflow, bookings, and responsive jobs views.
- PayPal Subscription API integration for **CleanFlow's own SaaS subscription**, with hosted PayPal approval and verified webhook signatures. It is not a payment tool for cleaning customers.
- Business-configured currency, locale, timezone, tax rate per quote, terms and expiry; seeded plans have no invented public prices.

## Requirements and local setup

- PHP **8.3+**, Composer 2, and PDO/MySQL (or SQLite locally).
- Node is **not required**. CSS is checked in at `public/css/cleanflow.css`; `npm run build` is a harmless compatibility message that works with Node 17.9.

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

For MySQL/MariaDB, set `DB_*` in `.env`. Never commit `.env`.

```bash
php artisan migrate:fresh --seed
php artisan test
```

## Plesk deployment — GitHub Pull only

No SSH is needed or documented for this project.

1. In **Plesk → Databases**, create the database and database user.
2. In **Plesk → Git**, connect this HTTPS GitHub repository and use **Pull Now** for releases.
3. In the domain’s **Hosting Settings**, set the document root to this repository’s `public` directory—never the repository root.
4. In **Laravel Toolkit**, select PHP 8.3+, set/generate `APP_KEY`, add environment variables, install production Composer dependencies, and run migrations. Use the toolkit rather than SSH commands.
5. Use Plesk file permissions/Laravel Toolkit to make `storage` and `bootstrap/cache` writable.
6. Set `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`, database credentials, `SESSION_DRIVER=database`, `CACHE_STORE=database`, and `QUEUE_CONNECTION=database`.
7. In Plesk Scheduled Tasks/Laravel Toolkit, configure the scheduler in the supported UI so `php artisan schedule:run` runs every minute. Add a durable queue worker only through Plesk after choosing a queue strategy.

Do not deploy, change DNS, or enable live billing until the relevant credentials, access, and approval are available.

## SMTP setup

Configure SMTP only in Plesk environment settings: `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`/`MAIL_SCHEME`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME`. No credentials are committed.

- `noreply@techittechnologies.com`: transactional sender (`MAIL_FROM_ADDRESS`)
- `support@techittechnologies.com`: `CLEANFLOW_SUPPORT_ADDRESS`
- `sales@techittechnologies.com`: `CLEANFLOW_SALES_ADDRESS`
- `payments@techittechnologies.com`: `CLEANFLOW_PAYMENTS_ADDRESS`

Email delivery is unverified until SMTP configuration and a real mailbox test are complete.

## PayPal subscriptions

### Sandbox

1. Create a Sandbox product and monthly billing plans in PayPal Developer.
2. Store the resulting ID in the matching `subscription_plans.paypal_plan_id` record. Its price/currency must match the PayPal plan.
3. In Plesk, set `PAYPAL_MODE=sandbox`, `PAYPAL_BASE_URL=https://api-m.sandbox.paypal.com`, `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`, and `PAYPAL_WEBHOOK_ID`.
4. Register `https://YOUR-APP-URL/webhooks/paypal` for billing subscription lifecycle events (activation, cancellation, suspension, expiry, and payment failure).
5. Test with a Sandbox buyer. A browser return is only a return: access changes after trusted PayPal verification/webhook processing.

### Live mode

Create separate live PayPal products/plans/webhook. Replace Plesk environment values and database plan IDs, set `PAYPAL_BASE_URL=https://api-m.paypal.com`, verify HTTPS delivery, then enable plans. Do not reuse Sandbox IDs or enable live billing before an end-to-end approval and webhook test.

## Boundaries and security

- Taxes are business-configured—not tax/legal compliance advice.
- The first UI is English. Locale, currency and timezone are stored for future localization; no unreviewed translations are shipped.
- No AI, SMS, WhatsApp, cleaner-customer payments, payroll, accounting, inventory, route optimisation, or social inbox features are included.
- Public forms are validated, CSRF protected, rate limited, and include a honeypot. Quote links are high-entropy and expire. Server-side checks scope business records.
