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
            <div class="table-responsive">
                <table id="categoriesTable" class="table table-hover table-striped table-custom align-middle mb-0">
                    @include('admin.categories.partials.table')
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const catTable = document.getElementById('categoriesTable');
        const catSearch = document.querySelector('input[name="q"]');
        const catPer = document.querySelector('select[name="per_page"]');

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
                    catTable.innerHTML = html;
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
