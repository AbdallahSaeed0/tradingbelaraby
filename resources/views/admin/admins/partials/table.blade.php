<thead class="table-light">
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Type</th>
        <th>Active</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach ($admins as $admin)
        <tr>
            <td>{{ $admins->firstItem() + $loop->index }}</td>
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ ucfirst($admin->type) }}</td>
            <td>
                <form action="{{ route('admin.admins.active', $admin) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <button
                        class="btn btn-sm {{ $admin->is_active ? 'btn-success' : 'btn-secondary' }}">{{ $admin->is_active ? 'Active' : 'Inactive' }}</button>
                </form>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.admins.edit', $admin) }}"><i
                                    class="fa fa-edit me-2"></i>Edit</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.admins.show', $admin) }}"><i
                                    class="fa fa-eye me-2"></i>View</a></li>
                        <li>
                            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                                onsubmit="return confirm('Delete admin?')">
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
                <small>Showing {{ $admins->count() }} of {{ $admins->total() }} admins</small>
                {{ $admins->links() }}
            </div>
        </td>
    </tr>
</tfoot>
