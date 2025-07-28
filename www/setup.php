<?php
require("connection.php");
?>

<!doctype html>
<html lang="tr" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kurulum</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>

<body>

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="bootstrap" viewBox="0 0 118 94">
            <path fill="currentColor" fill-rule="evenodd" d="M24.509 0c-6.733 0-11.715 5.893-11.492 12.284.214 6.14-.064 14.092-2.066 20.577C8.943 39.365 5.547 43.485 0 44.014v5.972c5.547.529 8.943 4.649 10.951 11.153 2.002 6.485 2.28 14.437 2.066 20.577C12.794 88.106 17.776 94 24.51 94H93.5c6.733 0 11.714-5.893 11.491-12.284-.214-6.14.064-14.092 2.066-20.577 2.009-6.504 5.396-10.624 10.943-11.153v-5.972c-5.547-.529-8.934-4.649-10.943-11.153-2.002-6.484-2.28-14.437-2.066-20.577C105.214 5.894 100.233 0 93.5 0H24.508zM80 57.863C80 66.663 73.436 72 62.543 72H44a2 2 0 0 1-2-2V24a2 2 0 0 1 2-2h18.437c9.083 0 15.044 4.92 15.044 12.474 0 5.302-4.01 10.049-9.119 10.88v.277C75.317 46.394 80 51.21 80 57.863M60.521 28.34H49.948v14.934h8.905c6.884 0 10.68-2.772 10.68-7.727 0-4.643-3.264-7.207-9.012-7.207M49.948 49.2v16.458H60.91c7.167 0 10.964-2.876 10.964-8.281s-3.903-8.178-11.425-8.178H49.948z" clip-rule="evenodd" />
        </symbol>
        <symbol id="check2" viewBox="0 0 16 16">
            <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0" />
        </symbol>
        <symbol id="circle-half" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 0 8 1zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16" />
        </symbol>
        <symbol id="moon-stars-fill" viewBox="0 0 16 16">
            <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278" />
            <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.73 1.73 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.73 1.73 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.73 1.73 0 0 0 1.097-1.097zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z" />
        </symbol>
        <symbol id="sun-fill" viewBox="0 0 16 16">
            <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708" />
        </symbol>
    </svg>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/color-modes.js"></script>
    <div class="container mt-5">
        <h2>Kurulum</h2>
        <?php if ($_POST && $_POST["weburl"] && $_POST["name"]) {
            $conn->exec("CREATE TABLE IF NOT EXISTS config (id INTEGER PRIMARY KEY AUTOINCREMENT, weburl TEXT NOT NULL, name TEXT NOT NULL, registerverify INTEGER not null DEFAULT 0, register INTEGER not null DEFAULT 1)");
            $conn->exec("CREATE TABLE IF NOT EXISTS users ( id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, displayname TEXT, password TEXT NOT NULL, rank INTEGER(100) NOT NULL DEFAULT 1, ipaddr TEXT NOT NULL, registerdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, lastlogin DATETIME, isban INTEGER NOT NULL DEFAULT 0 ) ");
            $conn->exec("CREATE TABLE IF NOT EXISTS auth_tokens ( id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, token_hash TEXT NOT NULL, expires_at TEXT NOT NULL, user_agent TEXT, ipaddr TEXT, FOREIGN KEY (user_id) REFERENCES users(id) )");
            $conn->exec("CREATE TABLE IF NOT EXISTS products_list ( id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, list_name TEXT NOT NULL, list_date DATETIME NOT NULL, list_percentage INTEGER NOT NULL DEFAULT 0, FOREIGN KEY (user_id) REFERENCES users(id))");
            $conn->exec("CREATE TABLE IF NOT EXISTS products_list_child ( id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, list_id INTEGER NOT NULL, product_code TEXT, product_name TEXT NOT NULL, product_price INTEGER NOT NULL, product_type TEXT NOT NULL, product_percentage INTEGER, product_vat INTEGER, FOREIGN KEY (user_id) REFERENCES users(id), FOREIGN KEY (list_id) REFERENCES products_list(id) ) ");
            $conn->exec("CREATE TABLE IF NOT EXISTS offers_list ( id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, list_name TEXT NOT NULL, list_date DATETIME NOT NULL, list_percentage INTEGER NOT NULL DEFAULT 0, FOREIGN KEY (user_id) REFERENCES users(id))");
            $conn->exec("CREATE TABLE IF NOT EXISTS offers_list_child ( id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, offer_id INTEGER NOT NULL, product_code TEXT, product_name TEXT NOT NULL, product_price INTEGER NOT NULL, product_quantity INTEGER NOT NULL DEFAULT 1, product_discount INTEGER NOT NULL DEFAULT 0, product_type TEXT NOT NULL, product_percentage INTEGER, product_vat INTEGER, FOREIGN KEY (user_id) REFERENCES users(id), FOREIGN KEY (offer_id) REFERENCES offers_list(id) ) ");

            $data = $conn->prepare("INSERT INTO config (weburl, name) VALUES (:weburl, :webname)");
            $data = $data->execute([
                ':weburl' => $_POST["weburl"],
                ':webname' => $_POST["name"]
            ]);
            if ($data) {
                $data1 = $conn->prepare("INSERT INTO users (username, email, ipaddr, password) VALUES (:username, :email, :ip, :password)");
                $data1 = $data1->execute([
                    ':username' => $_POST["root"],
                    ':email' => $_POST["rootEmail"],
                    ':ip' => $_SERVER['REMOTE_ADDR'],
                    ':password' => password_hash($_POST["rootPassword"], PASSWORD_BCRYPT),
                ]);
                if ($data1) {
        ?>
                    <div class="alert alert-success" role="alert">
                        Kayıt başarılı sayfayı yenileyiniz.
                    </div>
                <?php
                    header("Refresh: 5; URL=/");
                    exit();
                } else {
                    $conn->exec("DROP TABLE IF EXISTS config");
                    $conn->exec("DROP TABLE IF EXISTS users");
                ?>
                    <div class="alert alert-danger" role="alert">
                        Kayıt Başarısız tekrar deneyin.
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="alert alert-danger" role="alert">
                    Kayıt Başarısız tekrar deneyin.
                </div>
        <?php
            }
        }
        ?>

        <form method="POST">
            <div class="mb-3">
                <label for="weburl" class="form-label">Website URL</label>
                <input type="url" class="form-control" id="weburl" name="weburl" placeholder="Website adresini giriniz" required />
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">İsim</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Website İsminiz nedir?" required />
            </div>
            <div class="mb-3">
                <label for="root" class="form-label">Kullanıcı Adınız</label>
                <input type="text" class="form-control" id="root" name="root" placeholder="Kullanıcı adınız giriniz" required />
            </div>
            <div class="mb-3">
                <label for="rootEmail" class="form-label">E-posta</label>
                <input type="email" class="form-control" id="rootEmail" name="rootEmail" placeholder="E-posta adresi giriniz" required />
            </div>
            <div class="mb-3">
                <label for="rootPassword" class="form-label">Parola</label>
                <input type="password" class="form-control" id="rootPassword" name="rootPassword" placeholder="Parola Oluşturun" required />
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="submit" class="btn btn-success">Kaydet</button>

                <div class="d-flex align-items-center dropdown color-modes">
                    <button class="btn btn-warning dropdown-toggle"
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
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                                <svg class="bi me-2 opacity-50 theme-icon">
                                    <use href="#sun-fill"></use>
                                </svg>
                                Açık Tema
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark">
                                <svg class="bi me-2 opacity-50 theme-icon">
                                    <use href="#moon-stars-fill"></use>
                                </svg>
                                Karanlık Tema
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto">
                                <svg class="bi me-2 opacity-50 theme-icon">
                                    <use href="#circle-half"></use>
                                </svg>
                                Otomatik
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

        </form>

    </div>
    </div>
</body>

</html>