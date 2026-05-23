@extends('auth.index')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-lg ring-1 ring-gray-100">
            {{-- <div class="flex justify-center mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto">
            </div> --}}

            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-2">Đăng nhập</h2>
            <p class="text-sm text-center text-gray-500 mb-6">Nhập email và mật khẩu để tiếp tục</p>

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
                    <div class="mt-1">
                        <input type="password" name="password" id="password" required
                            class="block w-full rounded-lg border border-gray-200 px-4 py-2 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <label class="inline-flex items-center mt-2">
                        <input type="checkbox" id="togglePassword" class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Hiển thị mật khẩu</span>
                    </label>
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
            var toggle = document.getElementById('togglePassword');
            var pwd = document.getElementById('password');
            if(toggle && pwd){
                toggle.addEventListener('change', function(){
                    pwd.type = this.checked ? 'text' : 'password';
                });
            }
        })();
    </script>
@endsection
