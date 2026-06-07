@extends('auth.index')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-10 rounded-3xl shadow-2xl ring-1 ring-gray-100 animate__animated animate__zoomIn">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-2xl shadow-lg mb-4 animate__animated animate__bounceIn delay-1">
                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                </div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">ĐĂNG NHẬP</h2>
                <div class="h-1 w-20 bg-blue-600 mx-auto mt-2 rounded-full"></div>
            </div>
            
            <p class="text-sm text-center text-gray-500 mb-8 font-medium">Hệ thống Thống kê Huấn luyện Chiến Đấu</p>

            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-md mb-4" role="alert">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="block w-full rounded-lg border border-gray-200 px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                    <div class="mt-1 relative">
                        <input type="password" name="password" id="password" required
                            class="block w-full rounded-lg border border-gray-200 px-4 py-2 pr-10 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-2 flex items-center text-gray-500">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" id="remember" value="1" {{ old('remember') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Ghi nhớ đăng nhập</span>
                    </label>
                    <div>
                        {{-- <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Quên mật khẩu?</a> --}}
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full inline-flex justify-center items-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        Đăng nhập
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Chưa có tài khoản? Liên hệ quản trị viên.</p>
            </div>
        </div>
    </div>

    <script>
        (function(){
            var btn = document.getElementById('togglePassword');
            var pwd = document.getElementById('password');
            var eye = document.getElementById('eyeIcon');
            if(btn && pwd){
                btn.addEventListener('click', function(){
                    if(pwd.type === 'password'){
                        pwd.type = 'text';
                        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"/>';
                    } else {
                        pwd.type = 'password';
                        eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                    }
                });
            }
        })();
    </script>
@endsection
