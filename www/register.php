<?php
require("connection.php");
$webTitle = "Kayıt Ol";
require("layouts/header.php");
?>
<link href="/assets/css/signin.css" rel="stylesheet">

<body class="d-flex align-items-center py-4 bg-body-tertiary">

    <main class="form-signin w-100 m-auto">
        <form method="POST">
            <img class="mb-4" src="/assets/image/favicon.ico" alt="Logo" width="72" height="57">
            <?php
            if (isset($_SESSION["user_id"])) {
                header("Location: /dashboard/index.php");
                exit();
            }
            $website_id = 1;
            $WebsiteInfo = $conn->prepare("SELECT register FROM config WHERE id = ?");
            $WebsiteInfo->execute([$website_id]);
            $WebsiteInfo = $WebsiteInfo->fetch(PDO::FETCH_ASSOC);
            if (!$WebsiteInfo) {
                header("Location: /setup.php");
                exit;
            }
            if ($_POST && $_POST["email"] && $_POST["password"] && $_POST["username"]) {
            if ($WebsiteInfo["register"] == 1) {
                $AccountInfo = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $AccountInfo->execute([$_POST["username"], $_POST["email"]]);
                $AccountInfo = $AccountInfo->fetch(PDO::FETCH_ASSOC);

                if ($AccountInfo) {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Kayıt Başarısız! Bu kullanıcı adı ve ya e-posta kullanılmakta.
                    </div>
                    <?php
                } else {
                $data = $conn->prepare("INSERT INTO users (username, email, ipaddr, password) VALUES (:username, :email, :ip, :password)");
                $data = $data->execute([
                    ':username' => $_POST["username"],
                    ':email' => $_POST["email"],
                    ':ip' => $_SERVER['REMOTE_ADDR'],
                    ':password' => password_hash($_POST["password"], PASSWORD_BCRYPT),
                ]);
                if ($data) {
                    ?>
                    <div class="alert alert-success" role="alert">
                        Kayıt başarılı. Lütfen Giriş Yapınız
                    </div>
                    <?php
                    header("Refresh: 5; URL=/login.php");
                    exit();
                } else {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        Kayıt Başarısız.
                    </div>
                    <?php
                }
                }
            }
            }
            if ($WebsiteInfo["register"] == 0) {
                ?>
                <div class="alert alert-danger" role="alert">
                    Kayıtlar şu anda kapalıdır.
                </div>
                <?php
            }
            ?>
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" placeholder="isim@ornek.com" <?= ($WebsiteInfo["register"] ?? 0) == 1 ? '' : 'disabled' ?> required>
                <label for="email">E-posta Adresiniz</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı adınız..." <?= ($WebsiteInfo["register"] ?? 0) == 1 ? '' : 'disabled' ?> required>
                <label for="username">Kullanıcı Adınız</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Parola Giriniz..." <?= ($WebsiteInfo["register"] ?? 0) == 1 ? '' : 'disabled' ?> required>
                <label for="password">Parolanız</label>
            </div>

            <button class="btn btn-success w-100 py-2" type="submit" <?= ($WebsiteInfo["register"] ?? 0) == 1 ? '' : 'disabled' ?>>Kayıt Ol</button>
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
            <p class="mt-5 mb-3 text-body-secondary">Zaten bir hesabınız var mı? <a href="/login">Giriş Yapın</a></p>
        </form>
    </main>
</body>

</html>