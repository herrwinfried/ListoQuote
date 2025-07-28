<?php
require("../auth.php");
$webTitle = "Listeler";
include("../layouts/admin.header.php");
include("../layouts/admin.nav.php");

$products_list = $conn->prepare("SELECT * FROM products_list");
$products_list->execute([]);
$products_list = $products_list->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="col" style="margin-left: 2vh; margin-top: 2vh;">
  <div class="container">
  <a href="/dashboard/create/list.php" type="button" class="btn btn-success" style="margin-bottom: 2vh;">Yeni Oluştur</a>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th scope="col">Liste Adı</th>
        <th scope="col">Liste Tarihi</th>
        <th scope="col">Eylemler</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (empty($products_list)) {
        echo '<tr><td colspan="3" class="text-center">Henüz bir liste oluşturulmamış.</td></tr>';
      }
      foreach ($products_list as $product) {
        $dt = new DateTime($product["list_date"]);
        $dt = $dt->format("d-m-Y H:i");
      ?>
        <tr>
          <td class="w-50"><?= $product["list_name"]; ?></td>
          <td><?= $dt ?></td>
          <td><a href="/dashboard/edit/list.php?id=<?= $product["id"];?>" type="button" class="btn btn-warning">Düzenle</a> <a href="#" type="button" class="btn btn-danger" onclick="DeleteList(<?= $product["id"]; ?>)">Sil</a></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
  </div>
    <script>
        async function DeleteList(id) {
            const modal = createConfirmModal("Emin misiniz?", "Bu listeyi silmek istediğinizden emin misiniz?", async () => {
                const response = await fetch(`/dashboard/delete/list.php?id=${encodeURIComponent(id)}`);
                const data = await response.json();
console.log(data);
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