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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-lg-10"> 
                <h2>Using Media Library With Laravel 9</h2>
                <div class="row">
                    <form action="{{route('post.update', $post->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="form-group">
                          <label for="document">Name:</label>
                          <input type="text" name="name" value="{{$post->name}}" class="input-form"/>
                        </div>
                        <div class="form-group">
                            <label for="document">Upload:</label>
                            <div class="needsclick dropzone" id="document-dropzone"></div>
                        </div>
                        @foreach($post->media as $image)
                            <img src="{{ $image->getUrl() }}" alt="no image" width="100" height="100">
                        @endforeach
                        <div>
                          <input class="btn btn-danger" type="submit">
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </div>
    <script>
        var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
          url: '{{ route('post.upload') }}',
          maxFilesize: 1, // MB
          acceptedFiles: ".png, .jpeg, .jpg, .gif",
          addRemoveLinks: true,
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          success: function (file, response) {
            $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name
          },
          removedfile: function (file) {
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined')
            {
              name = file.file_name
            } else {
              name = uploadedDocumentMap[file.name]
            }
            $('form').find('input[name="document[]"][value="' + name + '"]').remove()
          },
          init: function () {
            @if(isset($project) && $project->document)
              var files =
                {!! json_encode($project->document) !!}
              for (var i in files) {
                var file = files[i]
                this.options.addedfile.call(this, file)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
              }
            @endif
          }
        }
      </script>
</body>
</html>