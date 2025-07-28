<?php
require("../auth.php");
$webTitle = "Teklifler";
include("../layouts/admin.header.php");
include("../layouts/admin.nav.php");

$products_list = $conn->prepare("SELECT * FROM offers_list");
$products_list->execute([]);
$products_list = $products_list->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exportModalLabel">Dışarı Aktar</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="typeRadio" id="typeRadio0" checked>
                        <label class="form-check-label" for="typeRadio0">
                            Her Ürün ayrı KDV ve İskontosu olsun
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="typeRadio" id="typeRadio1">
                        <label class="form-check-label" for="typeRadio1">
                            Tüm ürünlerin KDV ve İskontosu aynı olsun
                        </label>
                    </div>

                    <div id="uniformInputs" class="mt-3" style="display: none;">
                        <div class="mb-3">
                            <label for="uniformVat" class="form-label">KDV (%)</label>
                            <input type="number" class="form-control" id="uniformVat" name="uniformVat" min="0" step="1" value="20">
                        </div>
                        <div class="mb-3">
                            <label for="uniformDiscount" class="form-label">İskonto (%)</label>
                            <input type="number" class="form-control" id="uniformDiscount" name="uniformDiscount" min="0" step="1" value="20">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>

                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Dışa Aktar
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item export-option" href="#" data-ext="xlsx">Excel</a></li>
                            <li><a class="dropdown-item export-option" href="#" data-ext="ods">OpenDocument</a></li>
                            <li><a class="dropdown-item export-option" href="#" data-ext="pdf">PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<main class="col" style="margin-left: 2vh; margin-top: 2vh;">
  <div class="container">
  <a href="/dashboard/create/offer.php" type="button" class="btn btn-success" style="margin-bottom: 2vh;">Yeni Oluştur</a>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th scope="col">Teklif Adı</th>
        <th scope="col">Teklif Tarihi</th>
        <th scope="col">Eylemler</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (empty($products_list)) {
        echo '<tr><td colspan="3" class="text-center">Henüz bir teklif oluşturulmamış.</td></tr>';
      }
      foreach ($products_list as $product) {
        $dt = new DateTime($product["list_date"]);
        $dt = $dt->format("d-m-Y H:i");
      ?>
        <tr>
          <td class="w-50"><?= $product["list_name"]; ?></td>
          <td><?= $dt ?></td>
          <td><a href="/dashboard/edit/offer.php?id=<?= $product["id"];?>" type="button" class="btn btn-warning">Düzenle</a> <a href="#" type="button" data-bs-toggle="modal" data-bs-target="#exportModal" class="btn btn-info" data-exportid="<?= $product["id"];?>">Dışa Aktar</a> <a href="#" type="button" class="btn btn-danger" onclick="DeleteList(<?= $product["id"]; ?>)">Sil</a></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
  </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const typeRadio0 = document.getElementById("typeRadio0");
            const typeRadio1 = document.getElementById("typeRadio1");
            const inputSection = document.getElementById("uniformInputs");
            const exportOptions = document.querySelectorAll(".export-option");

            let currentOfferId = null;

            const exportModal = document.getElementById("exportModal");
            exportModal.addEventListener("show.bs.modal", function (event) {
                const button = event.relatedTarget;
                currentOfferId = button.getAttribute("data-exportid");
            });

            function toggleInputs() {
                inputSection.style.display = typeRadio1.checked ? "block" : "none";
            }

            typeRadio0.addEventListener("change", toggleInputs);
            typeRadio1.addEventListener("change", toggleInputs);
            toggleInputs();

            exportOptions.forEach(option => {
                option.addEventListener("click", function (e) {
                    e.preventDefault();

                    const ext = this.getAttribute("data-ext");

                    if (!currentOfferId) {
                        alert("Teklif ID alınamadı!");
                        return;
                    }

                    let url = `/dashboard/export/offer.php?id=${currentOfferId}`;

                    if (typeRadio0.checked) {
                        url += `&type=0`;
                    } else {
                        const vat = document.getElementById("uniformVat").value || 0;
                        const discount = document.getElementById("uniformDiscount").value || 0;
                        url += `&type=1&vat=${vat}&discount=${discount}`;
                    }

                    url += `&ext=${ext}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error("Hata oluştu");
                            return response.blob();
                        })
                        .then(blob => {
                            const link = document.createElement("a");
                            link.href = window.URL.createObjectURL(blob);
                            link.download = `teklif-${currentOfferId}.${ext}`;
                            link.click();
                        })
                        .catch(error => {
                            console.error("Hata:", error);
                            alert("Dışa aktarım sırasında bir hata oluştu.");
                        });
                });
            });
        });
        async function DeleteList(id) {
            const modal = createConfirmModal("Emin misiniz?", "Bu listeyi silmek istediğinizden emin misiniz?", async () => {
                const response = await fetch(`/dashboard/delete/offer.php?id=${encodeURIComponent(id)}`);
                const data = await response.json();
                const title = data.success ? "Başarılı!" : "Başarısız!";
                const message = data.success ? "Liste başarıyla silindi. 3 Saniye İçinde Sayfa Yenilecek" : "Liste silinemedi. 3 Saniye İçinde Sayfa Yenilecek";
                if (data.success) {
                    createInfoModal(title, message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }
            });

            modal.show();
        }
        function createConfirmModal(titleText, bodyText, onConfirm) {
            const modalEl = document.createElement("div");
            modalEl.className = "modal fade";
            modalEl.tabIndex = -1;

            modalEl.innerHTML = `
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${titleText}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
          </div>
          <div class="modal-body">
            ${bodyText}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hayır</button>
            <button type="button" class="btn btn-danger" id="confirmBtn">Evet</button>
          </div>
        </div>
      </div>
    `;

            document.body.appendChild(modalEl);
            const modal = new bootstrap.Modal(modalEl);

            modalEl.querySelector("#confirmBtn").addEventListener("click", () => {
                modal.hide();
                onConfirm?.();
                setTimeout(() => modalEl.remove(), 500);
            });

            modalEl.addEventListener("hidden.bs.modal", () => modalEl.remove());

            return modal;
        }
        function createInfoModal(titleText, bodyText) {
            const modalEl = document.createElement("div");
            modalEl.className = "modal fade";
            modalEl.tabIndex = -1;

            modalEl.innerHTML = `
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">${titleText}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
          </div>
          <div class="modal-body">
            ${bodyText}
          </div>
        </div>
      </div>
    `;

            document.body.appendChild(modalEl);
            const modal = new bootstrap.Modal(modalEl);

            modal.show();
            modalEl.addEventListener("hidden.bs.modal", () => modalEl.remove());
        }

    </script>
</main>

<?php
include("../layouts/admin.footer.php");
?>