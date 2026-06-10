<x-layout>
    <div class="container">
        <div class="row">
            <div class="col-4">
                <img src="{{ asset('images/' . $product->photo) }}" alt="" width="350">
                <h1>{{ $product->name }}</h1>
            </div>
            <div class="col-8">
                <h2>{{ $product->price }} VND</h2>
                <p>Mô tả: <br> {{ strip_tags($product->description) }}</p>
            </div>
        </div>

        <h2>Comments</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="cmt" class="form-label"></label>
                <input type="text" class="form-control" id="cmt" placeholder="Nội dung bình luận...">
            </div> <br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</x-layout>