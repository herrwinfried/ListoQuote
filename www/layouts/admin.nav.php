  <div class="d-flex" style="height: 100vh; overflow: hidden;">
      <div id="adminMenu" class="navbar-expand-lg bg-body-tertiary flex-shrink-0 p-3" style="width: 280px;"> <a href="/dashboard/index.php"
              class="d-flex align-items-center pb-3 mb-3 link-body-emphasis text-decoration-none border-bottom"> <img src="/assets/image/favicon.ico"
                  class="bi pe-none me-2" width="40" height="40" aria-hidden="true">
              </img> <span class="fs-5 fw-semibold"><?= $webInfo["name"]; ?></span> </a>
          <ul class="list-unstyled ps-0">
              <li class="mb-1"> <button
                      class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                      data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
                      Liste
                  </button>
                  <div class="collapse" id="dashboard-collapse">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                          <li><a href="/dashboard/list.php"
                                  class="link-body-emphasis d-inline-flex text-decoration-none rounded">Göz At</a>
                          </li>
                          <li><a href="/dashboard/create/list.php"
                                  class="link-body-emphasis d-inline-flex text-decoration-none rounded">Oluştur</a>
                          </li>

                      </ul>
                  </div>
              </li>
              <li class="mb-1"> <button
                      class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                      data-bs-toggle="collapse" data-bs-target="#orders-collapse" aria-expanded="false">
                      Teklif
                  </button>
                  <div class="collapse" id="orders-collapse">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                          <li><a href="/dashboard/offer.php"
                                  class="link-body-emphasis d-inline-flex text-decoration-none rounded">Göz at</a></li>
                          <li><a href="/dashboard/create/offer.php"
                                  class="link-body-emphasis d-inline-flex text-decoration-none rounded">Oluştur</a>
                          </li>

                      </ul>
                  </div>
              </li>
              <li class="border-top my-3"></li>
              <li class="mb-1"> <button
                      class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                      data-bs-toggle="collapse" data-bs-target="#management-collapse" aria-expanded="false">
                      Yönetim
                  </button>
                  <div class="collapse" id="management-collapse">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                          <li><a href="/dashboard/edit/website.php"
                                  class="link-body-emphasis d-inline-flex text-decoration-none rounded">Website Ayarları</a>
                          </li>
                      </ul>
                  </div>
              </li>
              <li class="border-top my-3"></li>
              <li class="mb-1"> <button
                      class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                      data-bs-toggle="collapse" data-bs-target="#account-collapse" aria-expanded="false">
                      Hesabım
                  </button>
                  <div class="collapse" id="account-collapse">
                      <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                          <li><a href="/logout"
                                  class="link-body-emphasis d-inline-flex text-decoration-none rounded">Çıkış Yap</a>
                          </li>
                      </ul>
                  </div>
              </li>
              <br>
              <br>
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
          </ul>
      </div>