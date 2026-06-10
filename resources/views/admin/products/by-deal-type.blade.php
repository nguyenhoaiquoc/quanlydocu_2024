<x-admin-layout>
    <h2 class="mb-4">🏷️ Thống kê sản phẩm theo hình thức giao dịch</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hình thức giao dịch</th>
                <th>Số lượng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $item)
                <tr>
                    <td>{{ ucfirst($item->deal_type) }}</td>
                    <td>{{ $item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-admin-layout>
