<div id="mobileCategoriesListInner">
    @forelse($categories as $cat)
        @include('admin.categories.partials.mobile-category-card', ['cat' => $cat])
    @empty
        <div class="text-center py-4 text-muted">
            <i class="fa fa-folder fa-3x mb-3"></i>
            <p class="mb-0">No categories found.</p>
        </div>
    @endforelse
</div>
