@extends('admin.layout')

@section('title', 'Categories')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h1 class="h3 mb-0">Categories</h1>
                        <p class="text-muted mb-0">Manage course categories</p>
                    </div>
                    <div class="admin-list-header-actions">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add Category
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form class="row g-3" method="get" id="categorySearchForm" data-settings-mobile-toolbar="skip">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control"
                                id="categorySearchInput" placeholder="Search categories...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="per_page" class="form-select" id="categoryPerPage">
                            @foreach ([10, 15, 25, 50] as $s)
                                <option value="{{ $s }}" {{ ($perPage ?? 15) == $s ? 'selected' : '' }}>{{ $s }} per page</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive d-none d-lg-block admin-table-no-mobile-cards">
                    <table id="categoriesTable" class="table table-hover table-striped">
                        @include('admin.categories.partials.table')
                    </table>
                </div>

                <div class="admin-mobile-list d-lg-none" id="mobileCategoriesList">
                    @include('admin.categories.partials.mobile-list')
                </div>

                <div class="row mt-3" id="categoriesPagination">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="form-label me-2 mb-0 small">Per page:</label>
                            <select class="form-select form-select-sm w-auto" id="perPageSelect" onchange="changePerPage(this.value)">
                                @php
                                    $perPage = (int) request('per_page', $perPage ?? 15);
                                @endphp
                                @foreach ([10, 15, 25, 50, 100] as $size)
                                    <option value="{{ $size }}" {{ $perPage === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <div id="categoriesPaginationLinks">{{ $categories->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const catTable = document.getElementById('categoriesTable');
        const catSearch = document.getElementById('categorySearchInput');
        const catPer = document.getElementById('categoryPerPage');
        const mobileList = document.getElementById('mobileCategoriesList');

        function changePerPage(value) {
            if (catPer) catPer.value = value;
            loadCats(1);
        }

        function loadCats(page = 1) {
            const params = new URLSearchParams({
                q: catSearch ? catSearch.value : '',
                per_page: catPer ? catPer.value : 15,
                page
            });

            if (mobileList) {
                mobileList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            }

            fetch("{{ route('admin.categories.data') }}?" + params, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.text())
                .then(html => {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    if (catTable) {
                        catTable.innerHTML = '';
                        if (tempDiv.querySelector('thead')) catTable.appendChild(tempDiv.querySelector('thead'));
                        if (tempDiv.querySelector('tbody')) catTable.appendChild(tempDiv.querySelector('tbody'));
                    }

                    const newMobileList = tempDiv.querySelector('#mobileCategoriesListInner');
                    if (newMobileList && mobileList) {
                        mobileList.innerHTML = newMobileList.innerHTML;
                    }

                    const paginationLinks = tempDiv.querySelector('tfoot');
                    const paginationContainer = document.getElementById('categoriesPaginationLinks');
                    if (paginationLinks && paginationContainer) {
                        const linksDiv = paginationLinks.querySelector('div');
                        if (linksDiv) {
                            paginationContainer.innerHTML = linksDiv.querySelector('nav, .pagination') ? linksDiv.innerHTML : '';
                        }
                    }

                    attachPag();
                });
        }

        function attachPag() {
            document.querySelectorAll('#categoriesPaginationLinks ul.pagination a, #categoriesPaginationLinks .pagination a').forEach(a => {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    const url = new URL(a.href);
                    loadCats(url.searchParams.get('page') || 1);
                });
            });
        }

        if (catSearch) catSearch.addEventListener('input', () => loadCats());
        if (catPer) catPer.addEventListener('change', () => loadCats());
        attachPag();
    </script>
@endpush
