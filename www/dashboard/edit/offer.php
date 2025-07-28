<?php
require("../../auth.php");
$webTitle = "Teklif Düzenle";
include("../../layouts/admin.header.php");
include("../../layouts/admin.nav.php");

$offer_id = $_GET['id'] ?? ($_POST['offer_id']);
if (!$offer_id) {
    header("Location: /dashboard/offer.php");
    exit;
}
$OffersInfo = $conn->prepare("SELECT * FROM offers_list WHERE id = ?");
$OffersInfo->execute([$offer_id]);
$OffersInfo = $OffersInfo->fetch(PDO::FETCH_ASSOC);
if (!$OffersInfo) {
    header("Location: /dashboard/offer.php");
    exit;
}
$OffersInfoChild = $conn->prepare("SELECT * FROM offers_list_child WHERE offer_id = ?");
$OffersInfoChild->execute([$offer_id]);
$OffersInfoChild = $OffersInfoChild->fetchAll(PDO::FETCH_ASSOC);
?>
    <main class="col" style="margin-left: 2vh; margin-top: 2vh;">
        <h2 class="text-warning">Teklif Düzenle</h2>
        <br>
        <div class="container">
            <?php
            if ($_POST) {
                $ProductUpdate = $conn->prepare("UPDATE offers_list SET list_name = :lname, list_date = :ldate WHERE id = :lid");
                $ProductUpdate->execute([':lname' => $_POST["formLabel"], ':ldate' => $_POST["formDate"], ':lid' => $offer_id]);
                $ProductChildUpdate = $conn->prepare("UPDATE offers_list_child SET product_code = :pcode, product_name = :pname, product_price = :pprice, product_quantity = :pquantity, product_type = :ptype, product_percentage = :ppercentage, product_discount = :pdiscount, product_vat = :pvat WHERE id = :id");
                $TotelProductSize = count($_POST["up_product_id"]);
                foreach ($_POST["up_product_id"] as $index => $productid) {
                    $TotelProductDelSize = count($_POST["up_delete"] ?? []);

                    if (isset($_POST["up_delete"][$index]) && $_POST["up_delete"][$index] === "on") {
                        $data = $conn->prepare("DELETE FROM offers_list_child WHERE id = :id");
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
                        $ProductChildUpdate->execute([':id' => $productid, ':pcode' => $_POST["up_product_code"][$index], ':pname' => $_POST["up_product_name"][$index], ':pprice' => $_POST["up_product_price"][$index], ':pquantity' => $_POST["up_product_quantity"][$index],':ptype' => $_POST["up_product_type"][$index], ':ppercentage' => $_POST["up_product_percentage"][$index], ':pdiscount' => $_POST["up_product_discount"][$index], ':pvat' => $_POST["up_product_vat"][$index]]);
                        if ($TotelProductSize === $index + 1) {
                            ?>
                            <div class="alert alert-success" role="alert">
                                Liste mamuller düzenlendi. Güncel hali için Sayfayı 10 saniye içinde yenilenecek
                            </div>
                            <?php
                        }
                    }
                }
                $ProductChildInsert = $conn->prepare("INSERT INTO offers_list_child (offer_id, product_code, product_name, product_price, product_quantity, product_type, product_percentage, product_discount, product_vat, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if (isset($_POST['product_name']) && $_POST['product_name']) {
                    $TotelProductSize = count($_POST['product_name']);
                    foreach ($_POST['product_name'] as $index => $name) {
                        $ProductChildInsert->execute([
                            $offer_id,
                            $_POST['product_code'][$index],
                            $name,
                            $_POST['product_price'][$index],
                            $_POST['product_quantity'][$index],
                            $_POST['product_type'][$index],
                            $_POST['product_percentage'][$index],
                            $_POST['product_discount'][$index],
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
            <form id="editOffer" method="POST">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="formLabel">Liste Adı<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="formLabel" name="formLabel" value="<?= $OffersInfo["list_name"]; ?>" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="formDate">Liste Tarihi<span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="formDate" name="formDate" value="<?= $OffersInfo["list_date"]; ?>" required>
                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col" style="display: none;">Liste ID</th>
                        <th scope="col"></th>
                        <th scope="col" style="display: none;">Mamul Kodu</th>
                        <th scope="col">Mamul İsmi<span class="text-danger">*</span></th>
                        <th scope="col" style="width: 150px;">Birim Fiyat<span class="text-danger">*</span></th>
                        <th scope="col" style="width: 100px;">Adet<span class="text-danger">*</span></th>
                        <th scope="col" style="width: 100px;">Birim<span class="text-danger">*</span></th>
                        <th scope="col" style="width: 80px;">% Kaç Eklensin</th>
                        <th scope="col" style="width: 80px;">İskonto<span class="text-danger">*</span></th>
                        <th scope="col" style="width: 80px;">KDV<span class="text-danger">*</span></th>
                        <th scope="col">Seçenek</th>
                    </tr>
                    </thead>
                    <tbody id="table-body">
                    <?php foreach ($OffersInfoChild as $ProductChild) {
                        ?>
                            <tr>
                                <td style="display: none; visibility: hidden;">
                                    <input type="hidden" name="up_offer_id[]" class="form-control">
                                    <input type="hidden" name="up_product_id[]" class="form-control" value="<?= $ProductChild['id'] ?>">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" name="product_sid[]" data-write="update" data-bs-toggle="modal" data-bs-target="#listModal">
                                        Ürün Seç
                                    </button>
                                </td>
                                <td style="display: none; visibility: hidden;"><input type="hidden" name="up_product_code[]" class="form-control" value="<?= $ProductChild['product_code'] ?>" placeholder="İsteğe Bağlı" readonly></td>
                                <td><input type="text" name="up_product_name[]" class="form-control" value="<?= $ProductChild['product_name'] ?>" readonly></td>
                                <td><input type="number" name="up_product_price[]" class="form-control" value="<?= $ProductChild['product_price'] ?>" min=0 step="1" required></td>
                                <td><input type="number" name="up_product_quantity[]" class="form-control" min=1 step="1" value="<?= $ProductChild['product_quantity'] ?>"  required></td>
                                <td>
                                    <select name="up_product_type[]" class="form-select readonly-select">
                                        <option value="mt" <?= $ProductChild['product_type'] == 'mt' ? 'selected' : '' ?>>Mt</option>
                                        <option value="piece" <?= $ProductChild['product_type'] == 'piece' ? 'selected' : '' ?>>Adet</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="up_product_percentage[]" value="<?= $ProductChild['product_percentage'] ?>" class="form-control" min=0 step="1">
                                </td>
                                <td>
                                    <input type="number" name="up_product_discount[]" value="<?= $ProductChild['product_discount'] ?>" class="form-control" min=0 step="1" required>
                                </td>
                                <td>
                                    <input type="number" name="up_product_vat[]" value="<?= $ProductChild['product_vat'] ?>" class="form-control" min=0 step="1" value="20" required>
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
                    <a href="/dashboard/offer.php" type="button" class="btn btn-danger">İptal</a>
                </div>
            </form>
            <div class="modal fade" id="listModal" tabindex="-1" aria-labelledby="listModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="listModalLabel">Ürün Seçim</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <select id="SelectPriceList" onclick="getList()" onchange="setSelectedList()" class="form-select" aria-label="Liste Seçimi">
                            </select>
                            <br>
                            <input type="text" id="productSearch" class="form-control" oninput="ProductSearchAutocomplete()" placeholder="Ürün adı girin...">
                            <ul id="autocompleteResults" class="list-group mt-2"></ul>
                            <br>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" onclick="handleAddRow()">Ekle</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        function readonlySelect() {
            document.querySelectorAll('.readonly-select').forEach(select => {
                select.addEventListener('mousedown', e => e.preventDefault());
                select.addEventListener('keydown', e => e.preventDefault());
                select.addEventListener('focus', e => e.target.blur());
            });

        }
        document.getElementById("editOffer").addEventListener("submit", async function(e) {
            const currentListName = <?= json_encode($OffersInfo["list_name"]); ?>;
            const currentListDate = <?= json_encode($OffersInfo["list_date"]); ?>;
            const formLabel = document.getElementById("formLabel").value.trim();
            const formDate = document.getElementById("formDate").value.trim();

            e.preventDefault();
            if (!formLabel || !formDate) {
                showModal("Uyarı!", "Teklif adı ve tarihi boş olamaz.");
                return;
            }

            if (formLabel === currentListName && formDate === currentListDate) {
                e.target.submit();
                return;
            }

            const response = await fetch(`/dashboard/test/offer_exists.php?list_name=${encodeURIComponent(formLabel)}&list_date=${encodeURIComponent(formDate)}`);
            const data = await response.json();

            if (data.exists) {
                showModal("Dikkat!", "Bu Teklif adı ve tarih kombinasyonu zaten mevcut! Teklif adı ve ya tarih farklı olarak değiştirin.");
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
            <td style="display: none; visibility: hidden;"><input type="hidden" name="list_id[]" class="form-control"><input type="hidden" name="product_id[]" class="form-control"></td>
<td>
<button type="button" class="btn btn-info btn-sm" name="product_sid[]" data-bs-toggle="modal" data-bs-target="#listModal" data-write="add">
  Ürün Seç
</button>
            </td>
            <td><input type="text" name="product_code[]" class="form-control" placeholder="İsteğe Bağlı" readonly></td>
            <td><input type="text" name="product_name[]" class="form-control" readonly></td>
            <td><input type="number" name="product_price[]" class="form-control" min=0 step="1" required></td>
            <td><input type="number" name="product_quantity[]" class="form-control" min=1 step="1" required></td>
            <td>
                <select name="product_type[]" class="form-select readonly-select">
                    <option value="">Seç...</option>
                    <option value="mt">Mt</option>
                    <option value="piece">Adet</option>
                </select>
            </td>
<td>
                <input type="number" name="product_percentage[]" class="form-control" min=0 step="1">
</td>
<td>
                <input type="number" name="product_discount[]" class="form-control" min=0 step="1" value="0" required>
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
            readonlySelect();
        }

        var ProductsList;
        async function getList() {
            const response = await fetch('/dashboard/test/get_lists.php');
            const lists = await response.json();
            const selectElement = document.getElementById("SelectPriceList");
            selectElement.innerHTML = "";
            if (lists.length === 0) {
                const option = document.createElement("option");
                option.value = "";
                option.textContent = "Hiç Liste Bulunamadı";
                selectElement.appendChild(option);
                return;
            }
            ProductsList = lists;
            lists.forEach(list => {
                const option = document.createElement("option");
                option.value = list.id;
                option.textContent = `${list.list_name} (${new Date(list.list_date).toLocaleString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false })})`;
                selectElement.appendChild(option);
            });
        }

        let selectedListId = null;
        let selectedProduct = null;

        function ProductSearchAutocomplete() {

            const select = document.getElementById("SelectPriceList");
            selectedListId = select.value;
            if (!selectedListId) {
                console.warn("Liste ID seçilmedi.");
                return;
            }
            const input = document.getElementById("productSearch");
            const resultsContainer = document.getElementById("autocompleteResults");

            if (!input || !resultsContainer) {
                console.warn("Autocomplete liste elementi bulunamadı.");
                return;
            }

            input.addEventListener("input", async function () {
                const query = this.value.trim();

                if (!selectedListId || query.length < 2) {
                    resultsContainer.innerHTML = "";
                    return;
                }

                try {
                    const response = await fetch(`/dashboard/test/productsearch.php?list_id=${selectedListId}&q=${encodeURIComponent(query)}`);
                    const items = await response.json();

                    resultsContainer.innerHTML = "";

                    items.forEach(item => {
                        const li = document.createElement("li");
                        li.textContent = item.product_name;
                        li.classList.add("list-group-item", "list-group-item-action");
                        li.addEventListener("click", () => {
                            input.value = item.product_name;
                            selectedProduct = item;
                            resultsContainer.innerHTML = "";
                        });
                        resultsContainer.appendChild(li);
                    });
                } catch (err) {
                    console.error("Autocomplete başarısız:", err);
                }
            });
        }
        let selectedRow = null;

        let modalMode = "add";
        document.addEventListener("click", function(e) {
            if (e.target && e.target.matches("button[name='product_sid[]']")) {
                selectedRow = e.target.closest("tr");
                modalMode = e.target.dataset.write === "update" ? "update" : "add";
                console.log(`Modal Mode: ${modalMode}`);
            }
        });

        function handleAddRow(prefix = "") {
            if (!selectedProduct || !selectedRow) {
                showModal("Uyarı!", "Lütfen önce bir ürün seçin.");
                return;
            }
            if (!prefix && modalMode === "update") {
                prefix = "up_";
            }

            const exists = Array.from(document.querySelectorAll("input[name$='product_code[]']")).some(codeInput => {
                const row = codeInput.closest("tr") || codeInput.closest(".row");
                const nameInput = row.querySelector("input[name$='product_name[]']");
                return codeInput.value.trim() === selectedProduct.product_code.trim() &&
                       nameInput?.value.trim() === selectedProduct.product_name.trim();
            });
            if (exists) {
                    showModal("Uyarı!", "Bu ürün zaten listede mevcut. Lütfen farklı bir ürün seçin.");
                return;
            }
            const listIdFind = ProductsList.find(list => list.id === selectedProduct.list_id);
            selectedRow.querySelector(`input[name='${prefix}product_code[]']`).value = selectedProduct.product_code || "";
            selectedRow.querySelector(`input[name='${prefix}product_name[]']`).value = selectedProduct.product_name || "";
            selectedRow.querySelector(`input[name='${prefix}product_price[]']`).value = selectedProduct.product_price || "";
            selectedRow.querySelector(`select[name='${prefix}product_type[]']`).value = selectedProduct.product_type || "";
            selectedRow.querySelector(`input[name='${prefix}product_percentage[]']`).value = selectedProduct.product_percentage || listIdFind.list_percentage || "";
            selectedRow.querySelector(`input[name='${prefix}product_vat[]']`).value = selectedProduct.product_vat || "20";

            const modal = bootstrap.Modal.getInstance(document.getElementById("listModal"));
            modal.hide();

            document.getElementById("productSearch").value = "";
            document.getElementById("autocompleteResults").innerHTML = "";
            selectedProduct = null;
            selectedRow = null;
        }

        window.onload = updateDeleteButtons();
    </script>

<?php
include("../../layouts/admin.footer.php");
?>