<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
      <form action="">
        <input type="search" name="search">
      </form>
        <table class="table">
            <thead>
              <tr>
                <th scope="col">#id</th>
                <th scope="col">name</th>
                
                <th scope="col">image</th>
                <th scope="col">action</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($categories as $item)
                <tr>
                    <th scope="row">{{$item->id}}</th>
                    <td>{{$item->name}}</td>
                  
                    <td><img src="{{$item->image}}" alt="" style="width: 50px; height: 50px" width="50px" height="50px"></td>
                    <td><a href="{{route('Category.edit',['category' => $item->id])}}" class="btn btn-primary">edit</a>

                      <span>
                        <form action="{{ route('Category.delete', $item) }}" method="post">
    
                          @csrf
                          @method('DELETE')
                          <button class="btn btn-danger">Delete</button>
                        </form>
                      </span>
                  
                @endforeach
            </tbody>
          </table>
          {{ $categories->links() }}
    </div>
</body>
</html>