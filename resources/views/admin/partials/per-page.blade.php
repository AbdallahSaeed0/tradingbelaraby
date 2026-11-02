{{-- "Show [10] entries" selector for admin list tables. Usage: @include('admin.partials.per-page', ['paginator' => $items])
     The change handler lives in admin/layout.blade.php; controllers read the value with admin_per_page(). --}}
<div class="d-flex flex-wrap align-items-center gap-2 small text-muted admin-per-page">
    <label class="d-flex align-items-center gap-2 mb-0">
        <span>Show</span>
        <select class="form-select form-select-sm w-auto js-per-page" aria-label="Rows per page">
            @foreach (admin_per_page_options() as $size)
                <option value="{{ $size }}" {{ $paginator->perPage() === $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
        <span>entries</span>
    </label>
</div>
