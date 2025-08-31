# Coming Soon Page Setup Guide

This guide explains how to set up and manage the coming soon page functionality.

## Features

✅ **Multilingual Support**: Automatically detects browser language (Arabic/English)  
✅ **Responsive Design**: Works perfectly on desktop and mobile  
✅ **Form Validation**: Client and server-side validation  
✅ **Admin Management**: Full admin panel for managing subscribers  
✅ **Export Functionality**: Export subscribers to CSV  
✅ **Middleware Protection**: Redirects all traffic except admin routes

## Setup Instructions

### 1. Database Setup

The migrations and models are already created. Make sure to run:

```bash
php artisan migrate
```

### 2. Add Translations

Run the seeder to add translation keys:

```bash
php artisan db:seed --class=ComingSoonTranslationsSeeder
```

### 3. Enable Coming Soon Mode

**Option 1: Via Admin Dashboard (Recommended)**

1. Go to **Admin Panel > Settings**
2. Find the "Coming Soon Mode" card
3. Click the toggle button to enable/disable

**Option 2: Via .env file**
Add this to your `.env` file:

```env
COMING_SOON_ENABLED=true
```

### 4. Admin Access

Admins can still access the dashboard via:

```
https://yourdomain.com/admin
```

## How It Works

### Language Detection

-   Automatically detects browser's primary language
-   If Arabic (`ar`) → Shows Arabic interface
-   Otherwise → Shows English interface

### Form Fields

-   **Name**: Required
-   **Phone**: Required
-   **Email**: Required (unique)
-   **WhatsApp Number**: Optional
-   **Country**: Required
-   **Years of Experience**: Required (10, 20, 30, 40, 50)
-   **Notes**: Optional textarea

### Admin Management

Navigate to **Admin Panel > Users > Subscribers** to:

-   View all subscribers
-   Filter by experience, language, or search
-   Export to CSV
-   View detailed subscriber information
-   Delete subscribers

### Middleware Behavior

When `COMING_SOON_ENABLED=true`:

-   ✅ **Allowed**: `/admin` and `/admin/*` routes
-   ✅ **Allowed**: `/coming-soon` and form submission
-   ✅ **Allowed**: Asset files (CSS, JS, images)
-   ❌ **Redirected**: All other routes → Coming Soon page

## Customization

### Styling

The coming soon page uses:

-   Bootstrap 5 for layout
-   Font Awesome for icons
-   Custom CSS for animations and styling
-   Arabic font (Cairo) for RTL support

### Logo

Currently uses Font Awesome graduation cap icon. To use a custom logo:

1. Replace the icon in `resources/views/coming-soon.blade.php`
2. Update the `.logo` CSS class

### Colors

Main colors are defined in CSS variables:

-   `--primary-color: #f15a29` (Orange)
-   `--secondary-color: #17506b` (Dark Blue)

## Translation Management

Translations are stored in the database under the `coming_soon` group. You can:

1. Use the admin translations panel
2. Add new languages via the Languages management
3. Update translations via the admin interface

## Disabling Coming Soon

To disable the coming soon page:

1. Set `COMING_SOON_ENABLED=false` in `.env`
2. Or remove the line entirely (defaults to false)

## Security Notes

-   Form includes CSRF protection
-   Email validation prevents duplicates
-   Admin routes are protected by authentication middleware
-   Input validation on both client and server side

## Troubleshooting

### Coming Soon Not Showing

1. Check `.env` file has `COMING_SOON_ENABLED=true`
2. Clear config cache: `php artisan config:clear`
3. Ensure middleware is registered in `app/Http/Kernel.php`

### Translations Not Working

1. Run the seeder: `php artisan db:seed --class=ComingSoonTranslationsSeeder`
2. Check if languages exist in the database
3. Clear translation cache via admin panel

### Admin Can't Access

1. Ensure admin authentication is working
2. Check admin routes are not affected by middleware
3. Try accessing `/admin/login` directly

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ComingSoonController.php
│   │   └── Admin/SubscriberController.php
│   └── Middleware/ComingSoonMiddleware.php
├── Models/Subscriber.php
database/
├── migrations/xxx_create_subscribers_table.php
└── seeders/ComingSoonTranslationsSeeder.php
resources/views/
├── coming-soon.blade.php
└── admin/subscribers/
    ├── index.blade.php
    └── show.blade.php
```

## Support

For any issues or customizations, refer to the Laravel documentation or contact the development team.
