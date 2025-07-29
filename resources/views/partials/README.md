# Blade Partials Structure

This directory contains organized Blade partials for the Laravel e-learning application.

## Directory Structure

```
partials/
├── home/                    # Home page specific partials
│   ├── hero.blade.php      # Hero section with search
│   ├── features-section.blade.php
│   ├── courses-slider.blade.php
│   ├── testimonials.blade.php
│   ├── featured-categories.blade.php
│   ├── about-university.blade.php
│   ├── cta-video.blade.php
│   └── scholarship-banner.blade.php
├── courses/                 # Course related partials
│   ├── banner.blade.php    # Course page banner
│   ├── course-card.blade.php # Reusable course card
│   ├── blog-news.blade.php
│   ├── faq-section.blade.php
│   └── info-split.blade.php
├── shared/                  # Shared components across pages
│   ├── breadcrumb.blade.php # Breadcrumb navigation
│   └── contact-form.blade.php # Contact form component
└── README.md               # This file
```

## Usage Examples

### Including Home Partials

```blade
@include('partials.home.hero')
@include('partials.home.features-section')
@include('partials.home.courses-slider')
```

### Including Course Partials

```blade
@include('partials.courses.banner')
@include('partials.courses.course-card', ['course' => $course])
@include('partials.courses.faq-section')
```

### Including Shared Partials

```blade
@include('partials.shared.header')
@include('partials.shared.footer')
@include('partials.shared.breadcrumb')
@include('partials.shared.contact-form')
```

## Component Details

### Home Partials

-   **hero.blade.php**: Main hero section with search functionality
-   **features-section.blade.php**: Key features showcase
-   **courses-slider.blade.php**: Featured courses carousel
-   **testimonials.blade.php**: Student testimonials
-   **featured-categories.blade.php**: Course categories display
-   **about-university.blade.php**: About section
-   **cta-video.blade.php**: Call-to-action with video
-   **scholarship-banner.blade.php**: Scholarship promotion

### Course Partials

-   **banner.blade.php**: Course page header with breadcrumbs
-   **course-card.blade.php**: Reusable course display card
-   **blog-news.blade.php**: Blog/news section
-   **faq-section.blade.php**: FAQ accordion
-   **info-split.blade.php**: Information split layout

### Shared Partials

-   **header.blade.php**: Site navigation and top bar
-   **footer.blade.php**: Site footer with links and contact
-   **breadcrumb.blade.php**: Dynamic breadcrumb navigation
-   **contact-form.blade.php**: Reusable contact form with validation

## Styling

All partials include their own CSS styles where needed. The main color scheme uses:

-   Primary: `#ff6b35` (Orange)
-   Secondary: `#f7931e` (Light Orange)
-   Success: `#28a745` (Green)
-   Info: `#17a2b8` (Blue)

## Responsive Design

All partials are built with Bootstrap 5 and are fully responsive across all device sizes.

## Dependencies

-   Bootstrap 5
-   Font Awesome Icons
-   Laravel Blade templating
-   Custom CSS variables for theming
