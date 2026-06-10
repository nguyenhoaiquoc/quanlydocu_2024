<x-admin-layout>
    <h2 class="mb-4">📊 Thống kê sản phẩm theo danh mục</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Danh mục</th>
                <th>Số sản phẩm</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->products_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-admin-layout>
