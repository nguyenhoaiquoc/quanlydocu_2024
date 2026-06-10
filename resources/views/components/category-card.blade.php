
    <li>
      @foreach($product->categories as $category)
        <div class="category">
            <a href="#">{{ $category->name }}</a>
        </div>
    @endforeach
    </li>