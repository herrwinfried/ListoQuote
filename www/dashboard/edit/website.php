<?php
require("../../auth.php");
$webTitle = "Website Ayarları";
include("../../layouts/admin.header.php");
include("../../layouts/admin.nav.php");

$website_id = 1;

$WebsiteInfo = $conn->prepare("SELECT * FROM config WHERE id = ?");
$WebsiteInfo->execute([$website_id]);
$WebsiteInfo = $WebsiteInfo->fetch(PDO::FETCH_ASSOC);
if (!$WebsiteInfo) {
    header("Location: /dashboard/index.php");
    exit;
}
?>
    <main class="col" style="margin-left: 2vh; margin-top: 2vh;">
        <h2 class="text-warning">Website Ayarları</h2>
        <br>
        <div class="container">
            <?php
            if ($_POST) {
                $data = $conn->prepare("UPDATE config SET weburl = :wurl, name = :wname, register = :wregister WHERE id = :wid");
                $data->execute([':wurl' => $_POST["formUrl"], ':wname' => $_POST["formName"], ':wregister' => $_POST["register"], ':wid' => $website_id]);
             if ($data) {
                            ?>
                            <div class="alert alert-success" role="alert">
                                Ayarlar güncellendi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="alert alert-danger" role="alert">
                                Ayarlar güncellenemedi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                            </div>
                            <?php
                        }
                header("refresh: 10");
            }
            ?>
            <form id="editSettings" method="POST">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="formUrl">Website URL<span class="text-danger">*</span></label>
                    <input type="url" class="form-control" id="formUrl" name="formUrl" value="<?= $WebsiteInfo["weburl"]; ?>" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="formName">Website İsmi<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="formName" name="formName" value="<?= $WebsiteInfo["name"]; ?>" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label">Kayıt Durumu<span class="text-danger">*</span></label>
                    <div>
                        <input type="radio" id="registerOpen" name="register" value="1" <?= $WebsiteInfo["register"] == 1 ? "checked" : ""; ?> required>
                        <label for="registerOpen">Açık</label>
                        <input type="radio" id="registerClosed" name="register" value="0" <?= $WebsiteInfo["register"] == 0 ? "checked" : ""; ?> required>
                        <label for="registerClosed">Kapalı</label>
                    </div>
                </div>
                    <button type="submit" class="btn btn-warning">Düzenle</button>
                    <a href="/dashboard/index.php" type="button" class="btn btn-danger">İptal</a>
                </div>
            </form>
        </div>
    </main>

<?php
include("../../layouts/admin.footer.php");
?>