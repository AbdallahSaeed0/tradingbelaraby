# Courses Platform (Laravel)

A bilingual (Arabic / English) online courses platform built with Laravel 10. Instructors publish courses made of sections and lectures, students enroll and pay online or through the mobile apps, and admins manage everything from a dashboard.

## Features

- **Courses, sections and lectures** with lecture types (theory, demo, exercise, quiz), difficulty levels, learning objectives, prerequisites, resources, transcripts and subtitles
- **Full Arabic / English content** for every course, section and lecture, with RTL support and a language switcher
- **Built-in SEO tooling** per course: localized meta tags, Open Graph and Twitter cards, canonical URLs, hreflang, JSON-LD structured data and an auto-generated sitemap
- **Payments**: free enrollment, bank card payments (NBE), and in-app purchases through Apple and Google Play with server-side verification (prices in SAR)
- **Authentication**: email login, social login (Google, X/Twitter, Sign in with Apple), email and WhatsApp OTP verification, and API tokens via Sanctum
- **Mobile app backend**: API for the iOS and Android apps, Universal Links and Google Play real-time notifications
- **Certificates** and PDF generation (dompdf)
- **Admin dashboard** for course content, orders, enrollments and site settings
- **Notifications** through Firebase

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+, Laravel 10 |
| Database | MySQL |
| Frontend tooling | Vite, Blade |
| Auth | Laravel Sanctum, Socialite |
| Other | dompdf, spatie/laravel-sitemap, kreait/laravel-firebase |

## Getting started

```bash
git clone https://github.com/AbdallahSaeed0/tradingbelaraby.git
cd tradingbelaraby
composer install
npm install
cp .env.example .env
php artisan key:generate
# set your database and service credentials in .env
php artisan migrate
npm run dev
php artisan serve
```

## Multilingual content

Each translatable field has an `_ar` counterpart (for example `name` and `name_ar`). Models expose localized accessors such as `$course->localized_name`, and a `MultilingualHelper` handles slugs, SEO metadata and text direction.

## Payments

Third-party services (payment gateway, Apple, Google Play, Firebase, WhatsApp) are configured through environment variables and `config/services.php`. Never commit real credentials.

## Author

Built by [Abdallah Saeed](https://github.com/AbdallahSaeed0).
