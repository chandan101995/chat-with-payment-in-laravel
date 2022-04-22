<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link href="{{ asset('/css/style.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Chat Applicatioons</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="container bootstrap snippets bootdey">
        <div class="row">
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                                                 document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
            <div class="col-md-4 bg-white">
                <div class="row border-bottom padding-sm" style="height: 40px;">
                    Member
                </div>
                <!-- =============================================================== -->
                <!-- member list -->
                <ul class="friend-list">
                    <form method="POST">
                        {{ csrf_field() }}
                        @foreach ($users as $user)
                            <li class="active bounceInDown">
                                <input type="hidden" value="{{ $user->id }}" />
                                <a href="javascript:;" class="clearfix">
                                    <img src="{{ $user->image }}" alt="{{ $user->name }}" class="img-circle"
                                        title="{{ $user->name }}" />
                                    <div class="friend-name">
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                    <small class="time text-muted"><i
                                            class="fa fa-circle user-status-icon user-icon-{{ $user->id }}"
                                            title="away"></i></small>
                                    <small
                                        class="chat-alert label label-danger">{{ count($user->messagesSeen) }}</small>
                                </a>
                            </li>
                        @endforeach
                    </form>
                </ul>
            </div>

            <!--=========================================================-->
            <!-- selected chat -->
            <div class="col-md-8 bg-white">
                <div class="fallback"></div>
                <div class="chat-message">
                    {!! $html !!}
                </div>
                <div class="chat-box bg-white">
                    <form method="POST">
                        @csrf
                        <div class="input-group">
                            <input required class="form-control border no-shadow no-rounded"
                                name="text_input" placeholder="Type your message here" />
                            <span class="input-group-btn">
                                <button class="btn btn-success no-rounded textSubmit" type="submit">Send</button>
                            </span>
                        </div>
                    </form>
                    <!-- /input-group -->
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
</body>
<script>
    $(document).ready(function() {
        let ip_address = '127.0.0.1';
        let socket_port = '3000';
        let socket = io(ip_address + ':' + socket_port);
        let user_id = "{{ auth()->user()->id }}";
        socket.on('connection');

        var typing=false;
        var user;
        var timeout=undefined
        
        $('.bounceInDown').click(function() {
            var u_id = $(this).find('input').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('userChat') }}",
                data: {
                    'u_id': u_id
                },
                success: function(data) {
                    if (data.data != '') {
                        $('.chat-message').html(data.data);
                    }
                },
                error: function(data) {
                    console.log('fail', data);
                }
            });
        });

        // text message send to server
        $('.textSubmit').click(function(e) {
            e.preventDefault();
            var r_id = $('input[name=r_id]').val();
            var message = $('input[name=text_input]').val();
            sendMessage(r_id, message);
        });

        $('input[name=text_input]').keypress((e)=>
        {
            if(e.which!=13){
                typing=true
                socket.emit('typing', {user:"user", typing:true})
                clearTimeout(timeout)
                timeout=setTimeout(typingTimeout, 1500)
            }else{
                clearTimeout(timeout)
                typingTimeout()
                sendMessage()
            }
        })

        //code explained later
        socket.on('display', (data)=>{
        if(data.typing==true)
            $('.fallback').text(`${data.user} is typing...`);
        else
            $('.fallback').text("");
        });
      
        function sendMessage(r_id, message) {
            var html = '<li class="right clearfix">';
            html += '<span class="chat-img pull-right">';
            html +=
                '<img src="{{ auth::user()->image }}" alt="{{ auth::user()->name }}" title="{{ auth()->user()->name }}" />';
            html += '</span>';
            html += '<div class="chat-body clearfix">';
            html += '<div class="header">';
            html += '<strong class="primary-font">{{ auth()->user()->name }}</strong>';
            html +=
                '<small class="pull-right text-muted"><i class="fa fa-clock-o"></i>{{ \Carbon\Carbon::parse(date('Y-m-d H:i:s'))->diffForHumans() }}</small>';
            html += '</div>';
            html += '<p>' + message + '</p>';
            html += '</div>';
            html += '</li>';
            socket.emit('sendChatToServer', message);
            $('input[name=text_input]').val('');
            $('#chatData').append(html);

            if (message) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "{{ route('chatMessage') }}",
                    data: {
                        'r_id': r_id,
                        'text_val': message
                    },
                    success: function(data) {

                    },
                    error: function(data) {
                        console.log('fail', data);
                    }
                });
                return false;
            } else {
                alert('Please fill the input field!');
                return false;
            }
        }

        socket.on('sendChatToClient', (message) => {
            var html = '<li class="right clearfix">';
            html += '<span class="chat-img pull-right">';
            html +='<img src="{{ auth::user()->image }}" alt="{{ auth::user()->name }}" title="{{ auth()->user()->name }}" />';
            html += '</span>';
            html += '<div class="chat-body clearfix">';
            html += '<div class="header">';
            html += '<strong class="primary-font">{{ auth()->user()->name }}</strong>';
            html +='<small class="pull-right text-muted"><i class="fa fa-clock-o"></i>{{ \Carbon\Carbon::parse(date('Y-m-d H:i:s'))->diffForHumans() }}</small>';
            html += '</div>';
            html += '<p>' + message + '</p>';
            html += '</div>';
            html += '</li>';
            $('#chatData').append(html);
        });

        socket.on('connect', function() {
            socket.emit('user_connected', user_id);
        });

        socket.on('updateUserStatus', (data) => {
            let $userStatusIcon = $('.user-status-icon');
            $userStatusIcon.removeClass('text-success');
            $userStatusIcon.attr('title', 'Away');

            $.each(data, function(key, val) {
                if (val !== null && val !== 0) {
                    let $userIcon = $(".user-icon-" + key);
                    $userIcon.addClass('text-success');
                    $userIcon.attr('title', 'Online');
                }
            });
        });
        //unseen message get from messages table 

    });

    function typingTimeout(){
        typing=false
        socket.emit('typing', {user:user, typing:false})
    }
</script>

</html>
