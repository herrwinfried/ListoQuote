<?php
require("../../auth.php");
$webTitle = "Teklif Oluştur";
include("../../layouts/admin.header.php");
include("../../layouts/admin.nav.php");
?>
    <main class="col" style="margin-left: 2vh; margin-top: 2vh;">
        <h2 class="text-success">Teklif Oluşturma</h2>
        <br>
        <div class="container">
            <?php
            if ($_POST) {
                $listName = $_POST['formLabel'];
                $listDate = $_POST['formDate'];
                $productCodes = $_POST['product_code'] ?? [];
                $productNames = $_POST['product_name'] ?? [];
                $productPrices = $_POST['product_price'] ?? [];
                $productQuantity = $_POST['product_quantity'] ?? [];
                $productTypes = $_POST['product_type'] ?? [];
                $productPercentages = $_POST['product_percentage'] ?? [];
                $productDiscounts = $_POST['product_discount'] ?? [];
                $productVats = $_POST['product_vat'] ?? [];
                $TotelProductSize = count($productNames);
                $offers_list = $conn->prepare("INSERT INTO offers_list (list_name, list_date, user_id) VALUES (?, ?, ?)");
                $offers_list->execute([
                    $listName,
                    $listDate,
                    $_SESSION['user_id']
                ]);
                $offers_list = $conn->prepare("SELECT * FROM offers_list WHERE list_name = ? AND list_date = ?");
                $offers_list->execute([$listName, $listDate]);
                $offers_list = $offers_list->fetch(PDO::FETCH_ASSOC);
            $data = $conn->prepare("INSERT INTO offers_list_child (offer_id, product_code, product_name, product_price, product_quantity, product_type, product_percentage, product_discount, product_vat, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($productNames as $index => $name) {
                $data->execute([
                    $offers_list["id"],
                    $productCodes[$index],
                    $name,
                    $productPrices[$index],
                    $productQuantity[$index],
                    $productTypes[$index],
                    $productPercentages[$index],
                    $productDiscounts[$index],
                    $productVats[$index],
                    $_SESSION['user_id']
                ]);
            if ($data && ($TotelProductSize === $index + 1)) {
            ?>
            <div class="alert alert-success" role="alert">
                Teklif Oluşturuldu
            </div>
            <?php
            } elseif (!$data && ($index === 0)) {
            $conn->prepare("DELETE FROM offers_list WHERE list_name = ? AND list_date = ?")->execute([$listName, $listDate]);
            ?>
            <div class="alert alert-danger" role="alert">
                Teklif Oluşturulamadı
            </div>
            <?php
            } elseif (!$data && ($TotelProductSize === $index + 1)) {
            ?>
            <div class="alert alert-danger" role="alert">
                Teklif Oluşturulamadı
            </div>
            <?php
                }
            }
        }
        ?>
            <form id="createOffer" method="POST">
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="formLabel">Teklif Adı<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="formLabel" name="formLabel" required>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="formDate">Teklif Tarihi<span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control" id="formDate" name="formDate" required>
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
                    </tbody>
                </table>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="addRow()">Yeni Satır Ekle</button>
                    <button type="submit" class="btn btn-success">Oluştur</button>
                    <a href="/dashboard/offer.php" type="button" class="btn btn-danger">İptal</a>
                </div>
            </form>
        </div>
    </main>

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


    <script>
        function readonlySelect() {
            document.querySelectorAll('.readonly-select').forEach(select => {
                select.addEventListener('mousedown', e => e.preventDefault());
                select.addEventListener('keydown', e => e.preventDefault());
                select.addEventListener('focus', e => e.target.blur());
            });
        }
        document.getElementById("createOffer").addEventListener("submit", async function(e) {
            const formLabel = document.getElementById("formLabel").value.trim();
            const formDate = document.getElementById("formDate").value.trim();
            e.preventDefault();
            if (!formLabel || !formDate) {
                showModal("Uyarı!", "Teklif adı ve tarihi boş olamaz.");
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
<button type="button" class="btn btn-info btn-sm" name="product_sid[]" data-bs-toggle="modal" data-bs-target="#listModal">
  Ürün Seç
</button>
            </td>
            <td style="display: none; visibility: hidden;"><input type="hidden" name="product_code[]" class="form-control" placeholder="İsteğe Bağlı" readonly></td>
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

        function updateDeleteButtons() {
            const deleteButtons = document.querySelectorAll(".delete-btn");

            if (deleteButtons.length === 1) {
                deleteButtons[0].setAttribute("readonly", true);
            } else {
                deleteButtons.forEach(btn => btn.removeAttribute("readonly"));
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

        document.addEventListener("click", function(e) {
            if (e.target && e.target.matches("button[name='product_sid[]']")) {
                selectedRow = e.target.closest("tr");
            }
        });

        function handleAddRow() {
            if (!selectedProduct || !selectedRow) {
                showModal("Uyarı!", "Lütfen önce bir ürün seçin.");
                return;
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
            console.log(listIdFind);
            selectedRow.querySelector("input[name='product_code[]']").value = selectedProduct.product_code || "";
            selectedRow.querySelector("input[name='product_name[]']").value = selectedProduct.product_name || "";
            selectedRow.querySelector("input[name='product_price[]']").value = selectedProduct.product_price || "";
            selectedRow.querySelector("select[name='product_type[]']").value = selectedProduct.product_type || "";
            selectedRow.querySelector("input[name='product_percentage[]']").value = selectedProduct.product_percentage || listIdFind.list_percentage || "";
            selectedRow.querySelector("input[name='product_vat[]']").value = selectedProduct.product_vat || "20";

            const modal = bootstrap.Modal.getInstance(document.getElementById("listModal"));
            modal.hide();

            document.getElementById("productSearch").value = "";
            document.getElementById("autocompleteResults").innerHTML = "";
            selectedProduct = null;
            selectedRow = null;
        }

        window.onload = addRow;
    </script>



<?php
include("../../layouts/admin.footer.php");
?>