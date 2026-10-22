# Courses Platform (Laravel)

A bilingual (Arabic / English) online courses platform built with Laravel 10. Instructors publish courses made of sections and lectures, students enroll and pay online, and admins manage everything from a dashboard.

## Features

- **Courses, sections and lectures** with lecture types (theory, demo, exercise, quiz), difficulty levels, learning objectives, prerequisites, resources, transcripts and subtitles
- **Full Arabic / English content** for every course, section and lecture, with RTL support and a language switcher
- **Built-in SEO tooling** per course: localized meta tags, Open Graph and Twitter cards, canonical URLs, hreflang, JSON-LD structured data and an auto-generated sitemap
- **Multiple payment options**: free enrollment, credit card, Tabby (buy now, pay later) and PayPal with verified webhooks and automatic enrollment activation (SAR currency)
- **Authentication**: email login plus social login (Socialite with Apple and Twitter providers) and API tokens via Sanctum
- **Certificates** and PDF generation (dompdf)
- **Admin dashboard** for course content, orders and enrollments
- **Notifications** through Firebase

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+, Laravel 10 |
| Database | MySQL |
| Frontend tooling | Vite, Blade |
| Auth | Laravel Sanctum, Socialite |
| Other | dompdf, spatie/laravel-sitemap, kreait/laravel-firebase |

## Project structure

```
app/                  Models, controllers, services, helpers
config/               Application configuration
database/             Migrations and seeders
lang/                 Arabic and English translations
resources/views/      Blade templates (admin and public site)
routes/               Web and API routes
tests/                PHPUnit tests
```

## Getting started

```bash
git clone https://github.com/AbdallahSaeed0/tradingbelaraby.git
cd tradingbelaraby
composer install
npm install
cp .env.example .env
php artisan key:generate
# set your database and payment credentials in .env
php artisan migrate
npm run dev
php artisan serve
```

## Multilingual content

Each translatable field has an `_ar` counterpart (for example `name` and `name_ar`). Models expose localized accessors such as `$course->localized_name`, and a `MultilingualHelper` handles slugs, SEO metadata and text direction.

## Payments

Payment gateways are configured through environment variables. PayPal needs a webhook pointing to `/webhook/paypal`; the webhook ID goes in `PAYPAL_WEBHOOK_ID`. Never commit real credentials.

## Author

Built by [Abdallah Saeed](https://github.com/AbdallahSaeed0).
