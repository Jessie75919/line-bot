@include('consoles.products.components.head')

<body>

<div class="container">

    <ul class="list-group">
        <li class="list-group-item">{{$product->name}}</li>
        <li class="list-group-item">{{$product->price}}</li>
        <li class="list-group-item">{{$product->description}}</li>
    </ul>
</div>

</body>
</html>
