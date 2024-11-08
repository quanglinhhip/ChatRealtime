@extends('layouts.app')

@section('style')
    <style>
        .item img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .item {
            display: flex;
            padding: 10px;
            align-items: center;
            background: rgb(246, 236, 236);
            border-bottom: 1px solid rgb(167, 161, 161);
            position: relative;
        }

        .item:hover {
            opacity: 0.6;
        }

        .status {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: green;
            top: 5px;
        }

        .block-chat {
            width: 100%;
            height: 450px;
            border: 1px solid rgb(179, 174, 174);
            overflow-y: scroll;
            /* lăn chuột nếu dài quá */
            list-style: none;
        }

        .my-message {
            color: blue;
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="row">
                    @foreach ($users as $user)
                        <div class="col-md-12">
                            <a href="" class="item" id="link_{{ $user->id }}">
                                {{-- <div class="status"></div> --}}
                                <img src="{{ $user->image }}" alt="">
                                <p>{{ $user->name }}</p>
                            </a>

                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-9">
                <ul class="block-chat">

                </ul>
                <form>
                    {{-- @csrf --}}
                    <div class="d-flex">
                        <input type="text" name="" id="inputChat" class="form-control me-3">
                        <button type="button" class="btn btn-success" id="btnSend">Send</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection



@section('script')
    <script type=module>
        Echo.join('chat')
            .here(users => {
                // console.log(users, 'here');
                users.forEach(item => {
                    let el = document.querySelector(`#link_${item.id}`);
                    console.log(el);

                    let elementStatus = document.createElement('div');
                    elementStatus.classList.add('status');
                    if (el) {
                        el.appendChild(elementStatus);
                    }
                })
            })
            .joining(user => {

                let el = document.querySelector(`#link_${user.id}`);
                console.log(el);

                let elementStatus = document.createElement('div');
                elementStatus.classList.add('status');
                if (el) {
                    el.appendChild(elementStatus);
                }


                console.log(`${user.name} joined the chat.`);
                // console.log(user, 'joining');

            })
            .leaving(user => {

                let el = document.querySelector(`#link_${user.id}`);
                // console.log(el);

                let elementStatus = el.querySelector('.status');
                if (elementStatus) {
                    el.removeChild(elementStatus);
                }
                console.log(`${user.name} left the chat.`);
                // console.log(user, 'leaving');

            })
            // listen event từ UserOnlined
            .listen('UserOnlined', event => {
                // console.log(event); //log user va message
                let blockChat = document.querySelector('.block-chat');
                let elementChat = document.querySelector('li');
                elementChat.textContent = `${event.user.name} : ${event.message}`
                if (event.user.id == '{{ Auth::user()->id }}') {
                    elementChat.classList.add('my-message');
                }
                blockChat.appendChild(elementChat);

            })


        let inputChat = document.querySelector('#inputChat');
        let btnSend = document.querySelector('#btnSend');

        btnSend.addEventListener('click', function() {
            // console.log('abc');
            axios.post('{{ route('sendMessage') }}', {
                    'message': inputChat.value
                })
                .then(data => {
                    console.log(data.data.success);

                })
        });
    </script>
@endsection
