<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Post Upload like & comment</title>
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-10"> 
                <h2>Using Media Library With Laravel 9 List</h2>
                <div class="row">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Post</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>
                                    {{-- <img src="{{ $post->getFirstMediaUrl('document','thumb') }}" alt="no image" width="100" height="100"> --}}
                                    @foreach($post->media as $image)
                                        <img src="{{ $image->getUrl() }}" alt="no image" width="100" height="100">
                                    @endforeach
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-primary" href="{{route('post.edit',$post->id)}}">Edit </a>
                                </td>
                                <td>
                                    {{-- <form action="{{ route('images.destroy',$post->id) }}" method="POST" onsubmit="return confirm('{{ trans('are You Sure ? ') }}');"
                                        style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                    </form> --}}
                                    <input type="submit" class="btn btn-xs btn-danger" value="Delete">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>    
            </div>
        </div>
    </div>
    
</body>
</html>