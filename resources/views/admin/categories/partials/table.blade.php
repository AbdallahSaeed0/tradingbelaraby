<thead class="table-light">
    <tr>
        <th>#</th>
        <th>Image</th>
        <th>Name</th>
        <th>Description</th>
        <th>Featured</th>
        <th>Courses</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach ($categories as $cat)
        <tr>
            <td>{{ $categories->firstItem() + $loop->index }}</td>
            <td>
                @if ($cat->image)
                    <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="rounded w-50 h-50 img-h-60">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center w-50 h-50">
                        <i class="fas fa-image text-muted"></i>
                    </div>
                @endif
            </td>
            <td>
                <div>
                    <strong>{{ $cat->name }}</strong>
                    <br>
                    <small class="text-muted">{{ $cat->slug }}</small>
                </div>
            </td>
            <td>
                @if ($cat->description)
                    <span title="{{ $cat->description }}">
                        {{ Str::limit($cat->description, 50) }}
                    </span>
                @else
                    <span class="text-muted">No description</span>
                @endif
            </td>
            <td>
                @if ($cat->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star me-1"></i>Featured
                    </span>
                @else
                    <span class="badge bg-secondary">Regular</span>
                @endif
            </td>
            <td>
                <span class="badge bg-info">
                    <i class="fas fa-graduation-cap me-1"></i>{{ $cat->courses_count ?? 0 }}
                </span>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle"
                        data-bs-toggle="dropdown">Actions</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $cat) }}"><i
                                    class="fa fa-edit me-2"></i>Edit</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.categories.show', $cat) }}"><i
                                    class="fa fa-eye me-2"></i>View</a></li>
                        <li>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf @method('DELETE')
                                <button class="dropdown-item text-danger"><i
                                        class="fa fa-trash me-2"></i>Delete</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <td colspan="7" class="p-2">
            <div class="d-flex justify-content-between">
                <small>Showing {{ $categories->count() }} of {{ $categories->total() }} categories</small>
                {{ $categories->links() }}
            </div>
        </td>
    </tr>
</tfoot>
