@extends('admin.layout')

@section('title', 'Categories')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Categories</h3>
            <form class="d-flex" method="get">
                <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm me-2"
                    placeholder="Search...">
                <select name="per_page" class="form-select form-select-sm me-2 w-auto">
                    @foreach ([10, 15, 25, 50] as $s)
                        <option value="{{ $s }}" {{ ($perPage ?? 15) == $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Add</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="categoriesTable" class="table table-hover table-striped">
                        @include('admin.categories.partials.table')
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row mt-3" id="categoriesPagination">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center me-3">
                                <label class="form-label me-2 mb-0 small">Per page:</label>
                                <select class="form-select form-select-sm w-auto" id="perPageSelect" onchange="changePerPage(this.value)">
                                    @php
                                        $perPage = (int) request('per_page', 10);
                                    @endphp
                                    <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage === 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage === 100 ? 'selected' : '' }}>100</option>
                                    <option value="500" {{ $perPage === 500 ? 'selected' : '' }}>500</option>
                                    <option value="1000" {{ $perPage === 1000 ? 'selected' : '' }}>1000</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            <div id="categoriesPaginationLinks"></div>
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
        const catSearch = document.querySelector('input[name="q"]');
        const catPer = document.querySelector('select[name="per_page"]');

        // Change per page function
        function changePerPage(value) {
            catPer.value = value;
            loadCats(1);
        }

        function loadCats(page = 1) {
            const params = new URLSearchParams({
                q: catSearch.value,
                per_page: catPer.value,
                page
            });
            fetch("{{ route('admin.categories.data') }}?" + params, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.text()).then(html => {
                    // Create a temporary container to parse the HTML
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;
                    
                    // Extract table content (thead and tbody)
                    const newTableContent = tempDiv.querySelector('thead, tbody');
                    if (newTableContent) {
                        // Clear existing content and add new
                        catTable.innerHTML = '';
                        if (tempDiv.querySelector('thead')) {
                            catTable.appendChild(tempDiv.querySelector('thead'));
                        }
                        if (tempDiv.querySelector('tbody')) {
                            catTable.appendChild(tempDiv.querySelector('tbody'));
                        }
                    }
                    
                    // Extract pagination
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
            catTable.querySelectorAll('ul.pagination a').forEach(a => {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    const url = new URL(a.href);
                    loadCats(url.searchParams.get('page'));
                });
            });
        }
        catSearch.addEventListener('input', () => loadCats());
        catPer.addEventListener('change', () => loadCats());
        attachPag();
    </script>
@endpush
