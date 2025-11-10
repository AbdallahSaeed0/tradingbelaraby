@extends('layouts.app')

@section('title', 'My Wishlist - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('my_wishlist', 'front') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('wishlist_description', 'front') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Wishlist Content -->
    <section class="wishlist-content py-5">
        <div class="container">
            @if ($wishlistItems->count() > 0)
                <div class="row g-4 wishlist-items-container" id="wishlistItemsRow">
                    @foreach ($wishlistItems as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            @include('partials.courses.course-card', ['course' => $item->course])
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($wishlistItems->hasPages())
                    <div class="row mt-4">
                        <div class="col-12">
                            <nav aria-label="Wishlist pagination">
                                {{ $wishlistItems->links() }}
                            </nav>
                        </div>
                    </div>
                @endif
            @endif

            <div class="row justify-content-center {{ $wishlistItems->count() > 0 ? 'd-none' : '' }}" id="emptyWishlistRow">
                <div class="col-lg-6 text-center">
                    <div class="empty-wishlist py-5">
                        <i class="fa fa-heart fa-4x text-muted mb-4"></i>
                        <h3 class="fw-bold mb-3">{{ custom_trans('empty_wishlist', 'front') }}</h3>
                        <p class="text-muted mb-4">{{ custom_trans('empty_wishlist_message', 'front') }}</p>
                        <a href="{{ route('categories.index') }}" class="btn btn-primary">
                            <i class="fa fa-search me-2"></i>{{ custom_trans('browse_courses', 'front') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Override wishlist button behavior on wishlist page
        document.addEventListener('DOMContentLoaded', function() {
            // Remove the global wishlist handler for buttons on this page
            const wishlistButtons = document.querySelectorAll('.wishlist-btn');
            
            wishlistButtons.forEach(button => {
                // Remove all existing event listeners by cloning the button
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                // Add new click handler for wishlist page
                newButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (confirm('{{ custom_trans('remove_from_wishlist_confirm', 'front') }}')) {
                        const courseId = this.dataset.courseId;
                        const cardColumn = this.closest('.col-12, .col-md-6, .col-lg-4');

                        fetch(`/wishlist/remove/${courseId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    toastr.info(data.message || '{{ custom_trans('Course removed from wishlist!', 'front') }}');
                                    
                                    // Reload page after short delay to show toast
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 800);
                                } else {
                                    toastr.error(data.message || '{{ custom_trans('An error occurred', 'front') }}');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                toastr.error('{{ custom_trans('An error occurred. Please try again.', 'front') }}');
                            });
                    }
                });
            });
        });
    </script>
@endpush

