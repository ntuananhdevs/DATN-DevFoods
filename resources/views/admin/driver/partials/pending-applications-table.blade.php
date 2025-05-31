@forelse($applications as $application)
    <tr>
        <td>
            <div class="data-table-id">
                {{ $application->id }}
            </div>
        </td>
        <td>
            <div class="data-table-product-name">{{ $application->full_name }}</div>
        </td>
        <td>{{ $application->phone_number }}</td>
        <td>{{ $application->license_plate }}</td>
        <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
        <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
        <td>
            <div class="data-table-action-buttons">
                <a href="{{ route('admin.drivers.applications.show', ['application' => $application->id]) }}"
                    class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center">
            <div class="data-table-empty">
                <div class="data-table-empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Không có đơn đăng ký nào đang chờ xử lý</h3>
            </div>
        </td>
    </tr>
@endforelse 