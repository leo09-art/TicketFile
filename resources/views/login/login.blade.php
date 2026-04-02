<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>hello</h1>
<form action="{{route("login.authenticate")}}" method="post">
    @csrf
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="{{old("email")}}">
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </div>
    <button type="submit">Se connecter</button>
    <button type="reset">Annuler</button>
</form>
<p>Vous n'avez pas encore de compte, <a href="{{route("register")}}">Créer un compte ?</a></p>

</body>
</html>


<?php

