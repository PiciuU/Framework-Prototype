<!DOCTYPE html>
<html lang="pl-PL">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikacja obserwująca dostępność oraz cenę wskazanych produktów.">
    <meta name="author" content="PiciuU">
    <link rel="shortcut icon" href="favicon.ico">

    <title>StoreWatch | Logowanie</title>

    <link rel="stylesheet" href="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/css/login.css">

    <script src="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/js/login.js"></script>

    <script defer src="https://kit.fontawesome.com/a5a30cbef0.js" crossorigin="anonymous"></script>
</head>

<body>

    <header class="header">
        <div class="header__logo">StoreWatch</div>
    </header>

    <main class="main">
        <div id="login-container" class="container">
            <section class="container__wrapper">
                <div class="container__header">
                    <div class="text text-large">Logowanie</div>
                    <span class="text text-normal">Nie posiadasz konta? <span><span class="text text-links" onclick="changeContainer('register')">Zarejestruj się</span></span>
                    </span>
                </div>
                <form name="login" class="form" onsubmit="event.preventDefault(); loginUser()">
                    <div class="form__group">
                        <input id="login-form-login" type="text" name="login" placeholder="Login" required>
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="form__group">
                        <input id="login-form-password" type="password" name="password" placeholder="Hasło" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="form__group">
                        <button id="login-btn">Zaloguj</button>
                    </div>
                </form>
            </section>
        </div>

        <div id="register-container" class="container hide">
            <section class="container__wrapper">
                <div class="container__header">
                    <div class="text text-large">Rejestracja</div>
                    <span class="text text-normal">Posiadasz już konto? <span><span class="text text-links" onclick="changeContainer('login')">Zaloguj się</span></span>
                    </span>
                </div>
                <form name="register" class="form" onsubmit="event.preventDefault(); registerUser()">
                    <div class="form__group">
                        <input id="register-form-login" type="text" name="login" placeholder="Login" required>
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="form__group">
                        <input id="register-form-password" type="password" name="password" placeholder="Hasło" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="form__group">
                        <input id="register-form-password-confirmation" type="password" name="password" placeholder="Potwierdź hasło" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="form__group">
                        <button id="register-btn">Utwórz konto</button>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>

</html>