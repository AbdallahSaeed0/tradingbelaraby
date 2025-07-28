<thead class="table-light">
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Registered</th>
        <th>Active</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    @foreach ($users as $user)
        <tr>
            <td>{{ $users->firstItem() + $loop->index }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->created_at->diffForHumans() }}</td>
            <td>
                <form action="{{ route('admin.users.active', $user) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <button
                        class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</button>
                </form>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}"><i
                                    class="fa fa-edit me-2"></i>Edit</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.show', $user) }}"><i
                                    class="fa fa-eye me-2"></i>View</a></li>
                        <li>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Delete user?')">
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
        <td colspan="6" class="p-2">
            <div class="d-flex justify-content-between">
                <small>Showing {{ $users->count() }} of {{ $users->total() }} users</small>
                {{ $users->links() }}
            </div>
        </td>
    </tr>
</tfoot>
