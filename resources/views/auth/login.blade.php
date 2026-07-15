<x-guest-layout>
    <x-auth-session-status class="flash success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <div class="field">
            <label for="password">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="current-password">
            @error('password') <span class="error-text">{{ $message }}</span> @enderror
        </div>

        <label class="auth-remember">
            <input type="checkbox" name="remember">
            Ingat saya
        </label>

        <button type="submit" class="btn btn-volt btn-block" style="margin-top:22px">Masuk</button>

        @if (Route::has('password.request'))
            <div style="text-align:center;margin-top:16px">
                <a href="{{ route('password.request') }}" class="auth-forgot">Lupa kata sandi?</a>
            </div>
        @endif
    </form>
</x-guest-layout>