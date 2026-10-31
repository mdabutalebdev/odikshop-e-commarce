{{-- Product cards for the home "All Products" grid.
     Rendered both on initial load and by the Load More AJAX endpoint.
     Expects: $products (paginated Collection of Product). --}}
@foreach($products as $product)
    <x-product-card :product="$product" />
@endforeach
