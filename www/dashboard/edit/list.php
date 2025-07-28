<?php
require("../../auth.php");
$webTitle = "Liste Düzenle";
include("../../layouts/admin.header.php");
include("../../layouts/admin.nav.php");

$list_id = $_GET['id'] ?? ($_POST['list_id']);
if (!$list_id) {
    header("Location: /dashboard/list.php");
    exit;
}
$ProductInfo = $conn->prepare("SELECT * FROM products_list WHERE id = ?");
$ProductInfo->execute([$list_id]);
$ProductInfo = $ProductInfo->fetch(PDO::FETCH_ASSOC);
if (!$ProductInfo) {
    header("Location: /dashboard/list.php");
    exit;
}
$ProductInfoChild = $conn->prepare("SELECT * FROM products_list_child WHERE list_id = ?");
$ProductInfoChild->execute([$list_id]);
$ProductInfoChild = $ProductInfoChild->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="col" style="margin-left: 2vh; margin-top: 2vh;">
    <h2 class="text-warning">Liste Düzenle</h2>
    <br>
    <div class="container">
        <?php
        if ($_POST) {
            $ProductUpdate = $conn->prepare("UPDATE products_list SET list_name = :lname, list_date = :ldate, list_percentage = :lpercentage WHERE id = :lid");
            $ProductUpdate->execute([':lname' => $_POST["formLabel"], ':ldate' => $_POST["formDate"], ':lpercentage' => $_POST["formPercentage"], ':lid' => $list_id]);
            $ProductChildUpdate = $conn->prepare("UPDATE products_list_child SET product_code = :pcode, product_name = :pname, product_price = :pprice, product_type = :ptype, product_percentage = :ppercentage, product_vat = :pvat WHERE id = :id");
            $TotelProductSize = count($_POST["up_product_id"]);
            foreach ($_POST["up_product_id"] as $index => $productid) {
                $TotelProductDelSize = count($_POST["up_delete"] ?? []);

                if (isset($_POST["up_delete"][$index]) && $_POST["up_delete"][$index] === "on") {
                    $data = $conn->prepare("DELETE FROM products_list_child WHERE id = :id");
                    $data->execute([':id' => $productid]);
                    if ($data && ($TotelProductDelSize === $index + 1)) {
        ?>
                        <div class="alert alert-success" role="alert">
                            Liste istenilen mamuller silindi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="alert alert-danger" role="alert">
                            Liste istenilen mamuller silinemedi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                        </div>
                    <?php
                    }
                } else {
                    $ProductChildUpdate->execute([':id' => $productid, ':pcode' => $_POST["up_product_code"][$index], ':pname' => $_POST["up_product_name"][$index], ':pprice' => $_POST["up_product_price"][$index], ':ptype' => $_POST["up_product_type"][$index], ':ppercentage' => $_POST["up_product_percentage"][$index], ':pvat' => $_POST["up_product_vat"][$index]]);
                    if ($TotelProductSize === $index + 1) {
                    ?>
                        <div class="alert alert-success" role="alert">
                            Liste mamuller düzenlendi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                        </div>
                    <?php
                    }
                }
            }
            $ProductChildInsert = $conn->prepare("INSERT INTO products_list_child (list_id, product_code, product_name, product_price, product_type, product_percentage, product_vat, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (isset($_POST['product_name']) && $_POST['product_name']) {
                $TotelProductSize = count($_POST['product_name']);
                foreach ($_POST['product_name'] as $index => $name) {
                    $ProductChildInsert->execute([
                        $list_id,
                        $_POST['product_code'][$index],
                        $name,
                        $_POST['product_price'][$index],
                        $_POST['product_type'][$index],
                        $_POST['product_percentage'][$index],
                        $_POST['product_vat'][$index],
                        $_SESSION['user_id']
                    ]);
                    if ($TotelProductSize === $index + 1) {
                    ?>
                        <div class="alert alert-success" role="alert">
                            Liste yeni mamuller eklendi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="alert alert-danger" role="alert">
                            Liste yeni mamuller eklenemedi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                        </div>
        <?php
                    }
                }
            }
            header("refresh: 10");
        }
        ?>
        <form id="editList" method="POST">
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="formLabel">Liste Adı<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="formLabel" name="formLabel" value="<?= $ProductInfo["list_name"]; ?>" required>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="formDate">Liste Tarihi<span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="formDate" name="formDate" value="<?= $ProductInfo["list_date"]; ?>" required>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="formPercentage">% Kaç Eklenecek<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="formPercentage" name="formPercentage" min=0 step="1" value="<?= $ProductInfo["list_percentage"]; ?>" required>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Mamul Kodu</th>
                    <th scope="col">Mamul İsmi<span class="text-danger">*</span></th>
                    <th scope="col" style="width: 150px;">Birim Fiyat<span class="text-danger">*</span></th>
                    <th scope="col">Birim<span class="text-danger">*</span></th>
                    <th scope="col" style="width: 80px;">% Kaç Eklensin</th>
                    <th scope="col" style="width: 80px;">KDV<span class="text-danger">*</span></th>
                    <th scope="col">Seçenek</th>
                </tr>
                </thead>
                <tbody id="table-body">
                    <?php foreach ($ProductInfoChild as $ProductChild) {
                    ?>
                        <tr>
                            <input type="hidden" name="up_product_id[]" value="<?= $ProductChild['id'] ?>">
                            <td><input type="text" name="up_product_code[]" class="form-control" value="<?= $ProductChild['product_code'] ?>" placeholder="İsteğe Bağlı"></td>
                            <td><input type="text" name="up_product_name[]" class="form-control" value="<?= $ProductChild['product_name'] ?>" required></td>
                            <td><input type="number" name="up_product_price[]" class="form-control" value="<?= $ProductChild['product_price'] ?>" min=0 step=1 required></td>
                            <td>
                                <select name="up_product_type[]" class="form-select" required>
                                    <option value="mt" <?= $ProductChild['product_type'] == 'mt' ? 'selected' : '' ?>>Mt</option>
                                    <option value="piece" <?= $ProductChild['product_type'] == 'piece' ? 'selected' : '' ?>>Adet</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="up_product_percentage[]" class="form-control" min=0 step="1" value="<?= $ProductChild['product_percentage'] ?>">
                            </td>
                            <td>
                                <input type="number" name="up_product_vat[]" class="form-control" min=0 step="1" value="<?= $ProductChild['product_vat'] ?>" required>
                            </td>
                            <td>
                                <input type="checkbox" class="btn-check" onclick="updateDeleteLabels()" id="delete-<?= $ProductChild['id'] ?>" name="up_delete[]" autocomplete="off">
                                <label class="btn btn-outline-warning btn-sm up-delete-btn" for="delete-<?= $ProductChild['id'] ?>">Sil</label>
                            </td>

                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="addRow()">Yeni Satır Ekle</button>
                <button type="submit" class="btn btn-warning">Düzenle</button>
                <a href="/dashboard/list.php" type="button" class="btn btn-danger">İptal</a>
            </div>
        </form>
    </div>
</main>

<script>
    document.getElementById("editList").addEventListener("submit", async function(e) {
        const currentListName = <?= json_encode($ProductInfo["list_name"]); ?>;
        const currentListDate = <?= json_encode($ProductInfo["list_date"]); ?>;
        const formLabel = document.getElementById("formLabel").value.trim();
        const formDate = document.getElementById("formDate").value.trim();

        e.preventDefault();
        if (!formLabel || !formDate) {
            showModal("Uyarı!", "Liste adı ve tarihi boş olamaz.");
            return;
        }

        if (formLabel === currentListName && formDate === currentListDate) {
            e.target.submit();
            return;
        }

        const response = await fetch(`/dashboard/test/list_exists.php?list_name=${encodeURIComponent(formLabel)}&list_date=${encodeURIComponent(formDate)}`);
        const data = await response.json();

        if (data.exists) {
            showModal("Dikkat!", "Bu liste adı ve tarih kombinasyonu zaten mevcut! Liste adı ve ya tarih farklı olarak değiştirin.");
            return;
        } else {
            e.target.submit();
        }

    });

    function showModal(title, message) {
        const modalBody = document.getElementById("alertModalBody");
        modalBody.textContent = message;
        modalTitle.textContent = title;

        const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
        alertModal.show();
    }

    function addRow() {
        const tableBody = document.getElementById("table-body");
        const row = document.createElement("tr");

        row.innerHTML = `
            <td><input type="text" name="product_code[]" class="form-control" placeholder="İsteğe Bağlı"></td>
            <td><input type="text" name="product_name[]" class="form-control" required></td>
            <td><input type="number" name="product_price[]" class="form-control" min=0 step="1" required></td>
            <td>
                <select name="product_type[]" class="form-select" required>
                    <option value="">Seç...</option>
                    <option value="mt">Mt</option>
                    <option value="piece">Adet</option>
                </select>
            </td>
<td>
                <input type="number" name="product_percentage[]" class="form-control" min=0 step="1">
</td>
<td>
                <input type="number" name="product_vat[]" class="form-control" min=0 step="1" value="20" required>
</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-btn" onclick="removeRow(this)">Sil</button>
            </td>
        `;
        tableBody.appendChild(row);

        updateDeleteButtons();
    }

    function removeRow(button) {
        const row = button.closest("tr");
        row.remove();

        updateDeleteButtons();
    }

    function updateDeleteLabels() {
        const checkboxes = document.querySelectorAll('input[name="up_delete[]"]');
        updateDeleteButtons();
        checkboxes.forEach(cb => {
            const label = document.querySelector(`label[for="${cb.id}"]`);
            if (!label) return;

            if (cb.checked) {
                label.textContent = "Silinecek";
            } else {
                label.textContent = "Sil";
            }

        });
    }

    function updateDeleteButtons() {
        const deleteButtons = document.querySelectorAll(".delete-btn");
        const checkboxes = document.querySelectorAll('input[name="up_delete[]"]:not(:checked)');
        const totalCount = deleteButtons.length + checkboxes.length;

        if (totalCount === 1) {
            deleteButtons.forEach(btn => btn.setAttribute("disabled", true));
            checkboxes.forEach(cb => {
                const label = document.querySelector(`label[for="${cb.id}"]`);
                if (label) label.classList.add("disabled");
            });
        } else {
            deleteButtons.forEach(btn => btn.removeAttribute("disabled"));
            checkboxes.forEach(cb => {
                const label = document.querySelector(`label[for="${cb.id}"]`);
                if (label) label.classList.remove("disabled");
            });
        }
    }


    window.onload = updateDeleteButtons();
</script>

<?php
include("../../layouts/admin.footer.php");
?>