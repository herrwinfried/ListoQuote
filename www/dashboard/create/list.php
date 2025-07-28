<?php
require("../../auth.php");
$webTitle = "Liste Oluştur";
include("../../layouts/admin.header.php");
include("../../layouts/admin.nav.php");
?>
<main class="col" style="margin-left: 2vh; margin-top: 2vh;">
    <h2 class="text-success">Liste Oluşturma</h2>
    <br>
    <div class="container">
        <?php
        if ($_POST) {
            $listName = $_POST['formLabel'];
            $listDate = $_POST['formDate'];
            $listPercentage = $_POST['formPercentage'];
            $productCodes = $_POST['product_code'];
            $productNames = $_POST['product_name'];
            $productPrices = $_POST['product_price'];
            $productTypes = $_POST['product_type'];
            $productPercentages = $_POST['product_percentage'];
            $productVats = $_POST['product_vat'];
            $TotelProductSize = count($productNames);
            $products_list = $conn->prepare("INSERT INTO products_list (list_name, list_date, list_percentage, user_id) VALUES (?, ?, ?, ?)");
            $products_list->execute([
                $listName,
                $listDate,
                $listPercentage,
                $_SESSION['user_id']
            ]);
            $products_list = $conn->prepare("SELECT * FROM products_list WHERE list_name = ? AND list_date = ?");
            $products_list->execute([$listName, $listDate]);
            $products_list = $products_list->fetch(PDO::FETCH_ASSOC);

            $data = $conn->prepare("INSERT INTO products_list_child (list_id, product_code, product_name, product_price, product_type, product_percentage, product_vat, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($productNames as $index => $name) {
                $data->execute([
                    $products_list["id"],
                    $productCodes[$index],
                    $name,
                    $productPrices[$index],
                    $productTypes[$index],
                    $productPercentages[$index],
                    $productVats[$index],
                    $_SESSION['user_id']
                ]);
                if ($data && ($TotelProductSize === $index + 1)) {
        ?>
                    <div class="alert alert-success" role="alert">
                        Liste Oluşturuldu
                    </div>
                <?php
                } elseif (!$data && ($index === 0)) {
                    $conn->prepare("DELETE FROM products_list WHERE list_name = ? AND list_date = ?")->execute([$listName, $listDate]);
                ?>
                <div class="alert alert-danger" role="alert">
                        Liste Oluşturulamadı
                    </div>
                <?php
                } elseif (!$data && ($TotelProductSize === $index + 1)) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        Liste Oluşturulamadı
                    </div>
        <?php
                }
            }
        }
        ?>
        <form id="createList" method="POST">
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="formLabel">Liste Adı<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="formLabel" name="formLabel" required>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="formDate">Liste Tarihi<span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="formDate" name="formDate" required>
            </div>
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="formPercentage">% Kaç Eklenecek<span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="formPercentage" name="formPercentage" min=0 step="1" value="0" required>
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
                </tbody>
            </table>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="addRow()">Yeni Satır Ekle</button>
                <button type="submit" class="btn btn-success">Oluştur</button>
                <a href="/dashboard/list.php" type="button" class="btn btn-danger">İptal</a>
            </div>
        </form>
    </div>
</main>

<script>
    document.getElementById("createList").addEventListener("submit", async function(e) {
        const formLabel = document.getElementById("formLabel").value.trim();
        const formDate = document.getElementById("formDate").value.trim();
        e.preventDefault();
        if (!formLabel || !formDate) {
            showModal("Uyarı!", "Liste adı ve tarihi boş olamaz.");
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

    function updateDeleteButtons() {
        const deleteButtons = document.querySelectorAll(".delete-btn");

        if (deleteButtons.length === 1) {
            deleteButtons[0].setAttribute("disabled", true);
        } else {
            deleteButtons.forEach(btn => btn.removeAttribute("disabled"));
        }
    }

    window.onload = addRow;
</script>

<?php
include("../../layouts/admin.footer.php");
?>