@extends('layouts.app')

@section('title', 'My Wishlist - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('my_wishlist') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('wishlist_description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Wishlist Content -->
    <section class="wishlist-content py-5">
        <div class="container">
            @if ($wishlistItems->count() > 0)
                <div class="row g-4">
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
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="empty-wishlist py-5">
                            <i class="fa fa-heart fa-4x text-muted mb-4"></i>
                            <h3 class="fw-bold mb-3">{{ custom_trans('empty_wishlist') }}</h3>
                            <p class="text-muted mb-4">{{ custom_trans('empty_wishlist_message') }}</p>
                            <a href="{{ route('categories') }}" class="btn btn-primary">
                                <i class="fa fa-search me-2"></i>{{ custom_trans('browse_courses') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Remove from wishlist functionality
        document.addEventListener('DOMContentLoaded', function() {
            const removeButtons = document.querySelectorAll('.remove-wishlist-btn');

            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('{{ custom_trans('remove_from_wishlist_confirm') }}')) {
                        const courseId = this.dataset.courseId;

                        fetch(`/wishlist/${courseId}/remove`, {
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
                                    // Remove the course card from the page
                                    this.closest('.col-12').remove();

                                    // Check if no more items
                                    if (document.querySelectorAll('.course-card').length ===
                                        0) {
                                        location.reload(); // Reload to show empty state
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred. Please try again.');
                            });
                    }
                });
            });
        });
    </script>
@endpush

