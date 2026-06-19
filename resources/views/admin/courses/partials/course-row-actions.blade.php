<div class="action-buttons admin-course-actions">
    <a href="{{ route('admin.courses.show', $course) }}"
        class="btn btn-sm btn-outline-primary" title="View">
        <i class="fa fa-eye"></i>
    </a>
    <a href="{{ route('admin.courses.edit', $course) }}"
        class="btn btn-sm btn-outline-secondary" title="Edit">
        <i class="fa fa-edit"></i>
    </a>
    <a href="{{ route('admin.courses.analytics', $course) }}"
        class="btn btn-sm btn-outline-info" title="Analytics">
        <i class="fa fa-chart-bar"></i>
    </a>
    <div class="dropdown d-inline">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
            data-bs-toggle="dropdown" aria-label="More actions">
            <i class="fa fa-ellipsis-v"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('admin.courses.duplicate', $course) }}"><i
                        class="fa fa-copy me-2"></i>Duplicate</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.courses.enrollments', $course) }}"><i
                        class="fa fa-users me-2"></i>Enrollments</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger"
                        onclick="return confirm('Are you sure you want to delete this course?')">
                        <i class="fa fa-trash me-2"></i>Delete
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
