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
        // Tham gia kênh 'chat' và xử lý các sự kiện khi có người tham gia, rời đi, hoặc khi có tin nhắn mới.
        Echo.join('chat')
            // Khi người dùng khác đã ở trong kênh, hiển thị trạng thái của họ
            .here(users => {
                users.forEach(item => {
                    // Tìm phần tử có id là `link_{item.id}` để xác định vị trí của user
                    let el = document.querySelector(`#link_${item.id}`);
                    console.log(el);

                    // Tạo một phần tử để hiển thị trạng thái online cho user
                    let elementStatus = document.createElement('div');
                    elementStatus.classList.add('status');
                    if (el) {
                        el.appendChild(elementStatus);
                    }
                });
            })
            // Xử lý sự kiện khi một người dùng mới tham gia kênh
            .joining(user => {
                // Tìm phần tử với id tương ứng với user mới
                let el = document.querySelector(`#link_${user.id}`);
                // Tạo và thêm phần tử trạng thái cho user mới
                let elementStatus = document.createElement('div');
                elementStatus.classList.add('status');
                if (el) {
                    el.appendChild(elementStatus);
                }

                console.log(`${user.name} joined the chat.`);
            })

            // Xử lý sự kiện khi một người dùng rời khỏi kênh
            .leaving(user => {
                // Tìm phần tử với id tương ứng với user đã rời đi
                let el = document.querySelector(`#link_${user.id}`);
                let elementStatus = el.querySelector('.status');
                // Nếu phần tử trạng thái tồn tại, xóa nó khỏi giao diện
                if (elementStatus) {
                    el.removeChild(elementStatus);
                }
                console.log(`${user.name} left the chat.`);
            })

            // Nghe sự kiện 'UserOnlined' để hiển thị tin nhắn mới trong kênh chat
            .listen('UserOnlined', event => {
                let blockChat = document.querySelector('.block-chat'); // Vùng hiển thị tin nhắn
                let elementChat = document.createElement('li'); // Tạo phần tử cho tin nhắn mới

                // Hiển thị tin nhắn của người dùng theo định dạng "user: message"
                elementChat.textContent = `${event.user.name} : ${event.message}`;

                // Nếu tin nhắn là của chính user hiện tại, thêm class để định dạng khác biệt
                if (event.user.id == '{{ Auth::user()->id }}') {
                    elementChat.classList.add('my-message');
                }

                // Thêm tin nhắn vào vùng chat
                blockChat.appendChild(elementChat);
            });

        // Lấy các phần tử cho input và button gửi tin nhắn
        let inputChat = document.querySelector('#inputChat');
        let btnSend = document.querySelector('#btnSend');

        // Thêm sự kiện click cho button gửi tin nhắn
        btnSend.addEventListener('click', function() {
            // Gửi tin nhắn đến backend qua route 'sendMessage' và truyền nội dung tin nhắn từ input
            axios.post('{{ route('sendMessage') }}', {
                    'message': inputChat.value
                })
                .then(data => {
                    console.log(data.data.success); // Kiểm tra phản hồi từ server (thành công hay không)
                });
        });
    </script>
@endsection
