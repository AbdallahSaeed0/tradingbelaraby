// Mobile Navigation Toggle
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    const mobileBtn = document.querySelector('.mobile-menu-btn i');

    if (navLinks.style.display === 'flex' || navLinks.style.display === 'block') {
        navLinks.style.display = 'none';
        mobileBtn.className = 'fas fa-bars';
    } else {
        navLinks.style.display = 'flex';
        navLinks.style.flexDirection = 'column';
        navLinks.style.position = 'absolute';
        navLinks.style.top = '100%';
        navLinks.style.left = '0';
        navLinks.style.right = '0';
        navLinks.style.background = '#fff';
        navLinks.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        navLinks.style.zIndex = '1000';
        mobileBtn.className = 'fas fa-times';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Mobile Bottom Navigation
    const mobileNavItems = document.querySelectorAll('.mobile-nav-item');

    mobileNavItems.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();

            // Remove active class from all items
            mobileNavItems.forEach(navItem => navItem.classList.remove('active'));

            // Add active class to clicked item
            this.classList.add('active');

            // Handle navigation based on the clicked item
            const icon = this.querySelector('i').className;

            if (icon.includes('fa-home')) {
                // Navigate to home
                console.log('Navigate to Home');
                // window.location.href = '/';
            } else if (icon.includes('fa-shopping-cart')) {
                // Navigate to cart
                console.log('Navigate to Cart');
                // window.location.href = '/cart';
            } else if (icon.includes('fa-bell')) {
                // Navigate to notifications
                console.log('Navigate to Notifications');
                // window.location.href = '/notifications';
            } else if (icon.includes('fa-user')) {
                // Navigate to profile
                console.log('Navigate to Profile');
                // window.location.href = '/profile';
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Search functionality
    const searchForm = document.querySelector('.search-box');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const searchInput = this.querySelector('input');
            if (searchInput.value.trim()) {
                // Implement search functionality here
                console.log('Searching for:', searchInput.value);
            }
        });
    }

    // Add animation to feature cards
    const featureCards = document.querySelectorAll('.feature-card');
    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add animation to course cards
    const courseCards = document.querySelectorAll('.course-card');
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
        });
    });

    // Add scroll animation for elements
    const animateOnScroll = function () {
        const elements = document.querySelectorAll('.feature-card, .course-card, .about-features .feature');

        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight;

            if (elementPosition < screenPosition) {
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }
        });
    };

    // Set initial styles for animation
    document.querySelectorAll('.feature-card, .course-card, .about-features .feature').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    });

    // Listen for scroll events
    window.addEventListener('scroll', animateOnScroll);
    // Initial check for elements in view
    animateOnScroll();
});

document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('coursesSlider');
    const prevBtn = document.getElementById('coursesPrev');
    const nextBtn = document.getElementById('coursesNext');

    // Only proceed if slider exists
    if (!slider) return;

    let isDown = false;
    let startX;
    let scrollLeft;

    // Mouse events for drag scrolling
    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.style.cursor = 'grabbing';
        slider.style.userSelect = 'none';
        e.preventDefault();
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
        isDown = false;
        slider.style.cursor = 'grab';
        slider.style.userSelect = '';
    });

    slider.addEventListener('mouseup', () => {
        isDown = false;
        slider.style.cursor = 'grab';
        slider.style.userSelect = '';
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2; // Scroll speed multiplier
        slider.scrollLeft = scrollLeft - walk;
    });

    // Button controls
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            const cardWidth = slider.querySelector('.col-12').offsetWidth;
            slider.scrollBy({
                left: -cardWidth,
                behavior: 'smooth'
            });
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const cardWidth = slider.querySelector('.col-12').offsetWidth;
            slider.scrollBy({
                left: cardWidth,
                behavior: 'smooth'
            });
        });
    }

    // Touch events for mobile devices
    let touchStartX = 0;
    let touchEndX = 0;

    slider.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    });

    slider.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            const cardWidth = slider.querySelector('.col-12').offsetWidth;
            if (diff > 0) {
                // Swipe left
                slider.scrollBy({
                    left: cardWidth,
                    behavior: 'smooth'
                });
            } else {
                // Swipe right
                slider.scrollBy({
                    left: -cardWidth,
                    behavior: 'smooth'
                });
            }
        }
    }
});

// Footer: Back to Top Button and Language Dropdown
window.addEventListener('DOMContentLoaded', function () {
    const backToTopBtn = document.getElementById('backToTopBtn');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    backToTopBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Language dropdown (demo: log selected value)
    const langSelect = document.getElementById('footerLangSelect');
    if (langSelect) {
        langSelect.addEventListener('change', function () {
            console.log('Language changed to:', this.value);
            // Implement language change logic here
        });
    }
});

// Category page: List/Grid view toggle and course card hover icons
window.addEventListener('DOMContentLoaded', function () {
    const listViewBtn = document.getElementById('listViewBtn');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const coursesList = document.getElementById('coursesList');

    if (listViewBtn && gridViewBtn && coursesList) {
        listViewBtn.addEventListener('click', function () {
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            // Show list view
            coursesList.querySelectorAll('.course-card-col').forEach(function (col) {
                if (col.querySelector('.course-card-list')) {
                    col.classList.remove('d-none');
                } else {
                    col.classList.add('d-none');
                }
            });
        });
        gridViewBtn.addEventListener('click', function () {
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            // Show grid view
            coursesList.querySelectorAll('.course-card-col').forEach(function (col) {
                if (col.querySelector('.course-card-grid')) {
                    col.classList.remove('d-none');
                } else {
                    col.classList.add('d-none');
                }
            });
        });
    }

    // Show hover icons on course card image hover
    document.addEventListener('mouseover', function (e) {
        const card = e.target.closest('.course-card-list, .course-card-grid');
        if (card) {
            const icons = card.querySelector('.course-hover-icons');
            if (icons) icons.style.display = 'flex';
        }
    });
    document.addEventListener('mouseout', function (e) {
        const card = e.target.closest('.course-card-list, .course-card-grid');
        if (card) {
            const icons = card.querySelector('.course-hover-icons');
            if (icons) icons.style.display = 'none';
        }
    });
});
