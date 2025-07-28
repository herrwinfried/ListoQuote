<?php
require("connection.php");
$webTitle = "Giriş yap";
require("layouts/header.php");
?>
<link href="/assets/css/signin.css" rel="stylesheet">

<body class="d-flex align-items-center py-4 bg-body-tertiary">

    <main class="form-signin w-100 m-auto">
        <?php
        if (isset($_SESSION["user_id"])) {
            header("Location: /dashboard/index.php");
            exit();
        }
        if ($_POST && $_POST["email"] && $_POST["password"]) {
            $remember = isset($_POST["remember"]);
            $data = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $data->execute([$_POST["email"]]);
            $accountInfo = $data->fetch(PDO::FETCH_ASSOC);
            if ($accountInfo && password_verify($_POST["password"], $accountInfo["password"])) {
                $_SESSION["user_id"] = $accountInfo["id"];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);
                    $expiry = date('c', strtotime('+30 days'));
                    setcookie("remember_me", $token, time() + (86400 * 30), "/", "", false, true);
                    $conn->prepare("INSERT INTO auth_tokens (user_id, token_hash, expires_at, user_agent, ipaddr)
                VALUES (?, ?, ?, ?, ?)")
                        ->execute([
                            $accountInfo['id'],
                            $tokenHash,
                            $expiry,
                            $_SERVER['HTTP_USER_AGENT'],
                            $_SERVER['REMOTE_ADDR']
                        ]);
                }
                header("Location: /dashboard/index.php");
                exit();
            } else {
        ?>
                <div class="alert alert-danger" role="alert">
                    E-posta ve ya parola hatalı tekrar deneyiniz.
                </div>
        <?php
            }
        }
        ?>
        <form method="POST">
            <img class="mb-4" src="/assets/image/favicon.ico" alt="Logo" width="72" height="57">

            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="isim@ornek.com">
                <label for="email">E-posta Adresiniz</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Parola Giriniz...">
                <label for="password">Parolanız</label>
            </div>

            <div class="form-check text-start my-3">
                <input class="form-check-input" type="checkbox" value="true" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Beni Hatırla
                </label>
            </div>

            <button class="btn btn-success w-100 py-2" type="submit">Giriş Yap</button>
            <br> <br>
            <div class="d-flex align-items-center dropdown color-modes">
                <button class="btn btn-warning dropdown-toggle w-100 py-2"
                    id="bd-theme"
                    type="button"
                    aria-expanded="false"
                    data-bs-toggle="dropdown"
                    data-bs-display="static">
                    <svg class="bi my-1 me-2 theme-icon-active">
                        <use href="#circle-half"></use>
                    </svg>
                    <span id="bd-theme-text">Tema Seç</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="bd-theme" style="--bs-dropdown-min-width: 8rem;">
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center w-100 py-2" data-bs-theme-value="light">
                            <svg class="bi me-2 opacity-50 theme-icon">
                                <use href="#sun-fill"></use>
                            </svg>
                            Açık Tema
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center w-100 py-2" data-bs-theme-value="dark">
                            <svg class="bi me-2 opacity-50 theme-icon">
                                <use href="#moon-stars-fill"></use>
                            </svg>
                            Karanlık Tema
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item d-flex align-items-center w-100 py-2" data-bs-theme-value="auto">
                            <svg class="bi me-2 opacity-50 theme-icon">
                                <use href="#circle-half"></use>
                            </svg>
                            Otomatik
                        </button>
                    </li>
                </ul>
            </div>
            <p class="mt-5 mb-3 text-body-secondary">Hesabınız yok mu? <a href="/register">Hesap Oluşturun</a></p>
        </form>
    </main>
</body>

</html>