<h1>hello</h1>
<form method="post" action="{{ route('register.store') }}">
    @csrf

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <div>
        <label for="name">Nom</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}">
    </div>
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}">
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation">
    </div>
    <div>
        <label for="role">Role </label>
    <select name="role" id="role">
        <option value=""></option>
        <option value="agent">Agent</option>
        <option value="usager">Usager</option>
        <option value="admin">Admin</option>
    </select>
    </div>

    <button type="submit">S'inscrire</button>
    <p>Vous avez deja un compte, <a href="{{route("login")}}"> Se connecter</a></p>
</form>

