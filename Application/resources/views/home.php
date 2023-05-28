<!DOCTYPE html>
<html lang="pl-PL">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikacja obserwująca dostępność oraz cenę wskazanych produktów.">
    <meta name="author" content="PiciuU">
    <link rel="shortcut icon" href="favicon.ico">

    <title>StoreWatch</title>

    <link rel="stylesheet" href="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/css/app.css">

    <script src="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/js/app.js"></script>

    <script src="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/js/table.js"></script>
    <script src="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/js/form.js"></script>

    <script defer src="<?php echo config('APP_URL_SUBFOLDER'); ?>/static/js/webpush.js"></script>
    <script defer src="https://kit.fontawesome.com/a5a30cbef0.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="header">
        <div class="header__logo">StoreWatch</div>
        <div class="header__options">
            <div id="header-notifications" class="header__notifications hide">
                Powiadomienia:
                <span id="header-notifications-status"></span>
                <button id="header-enable-notification" class="header__button hide" onclick="enableNotifications()">(Włącz)</button>
                <button id="header-disable-notification" class="header__button hide" onclick="disableNotifications()">(Wyłącz)</button>
            </div>
            <div class="header__profile">
                Zalogowany jako: <span id="logged-as"><?php echo $request->user->getLogin() ?></span>
                <button id="logout" class="header__button" onclick="logout()">(Wyloguj)</button>
            </div>
        </div>
    </header>

    <main>
        <div class="card">
            <div class="card__inner">
                <div class="card__title">
                    Obserwuj nowy produkt
                    <div class="align__right">
                        <button id="add-product-btn" form="add-product-form" title="Obserwuj nowy produkt"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card__content">
                    <form id="add-product-form" class="form" onsubmit="event.preventDefault(); addProduct()">
                        <div class="form__group layout__grid">
                            <div class="form__input">
                                <select id="form-store" required>
                                    <option disabled selected value></option>
                                    <option value="morele.net">Morele.net</option>
                                    <option value="proshop.pl">Proshop.pl</option>
                                </select>
                                <label for="form-store">Obserwowany sklep</label>
                            </div>

                            <div class="form__input">
                                <input type="text" id="form-link" placeholder="&#8203;" required/>
                                <label for="form-link">Link</label>
                            </div>

                            <div class="form__input">
                                <input type="text" id="form-price" placeholder="&#8203;" pattern="[0-9]+" required/>
                                <label for="form-price">Maksymalna cena (zł)</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card__inner">
                <div class="card__title">
                    Obserwowane produkty
                </div>
                <div class="card__content card__content-table">
                    <div id="table-products-empty" class="card__content-empty <?php if ($products) echo 'hide'; ?>">Nie znaleziono żadnych produktów.</div>
                    <table id="table-products" class="table <?php if (!$products) echo 'hide'; ?>">
                        <thead>
                            <tr>
                                <th class="text-left">Nazwa produktu</th>
                                <th>Dostępność</th>
                                <th>Ostatnia cena</th>
                                <th>Powiadomienie</th>
                                <th>Operacje</th>
                            </tr>
                        </thead>
                        <tbody id="table-products-tbody">
                            <?php echo '<script> renderTable('.json_encode($products).'); </script>' ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="edit-product-modal" class="modal hide">
            <div class="modal__container">
                <div class="modal__header">
                    <div class="modal__title">
                        Edycja produktu
                    </div>
                    <i class="fas fa-times" onclick="closeModal()"></i>
                </div>
                <div class="modal__body">
                    <div class="form form-vertical">
                        <div class="form__group">
                            <input type="text" id="modal-product-url" disabled/>
                            <label for="form-link">Link</label>
                        </div>
                        <div class="form__group">
                            <input type="text" id="modal-product-name" disabled/>
                            <label for="form-link">Nazwa produktu</label>
                        </div>
                        <div class="form__group">
                            <input type="text" id="modal-product-price" placeholder="&#8203;" pattern="[0-9]+" required/>
                            <label for="form-link">Maksymalna cena (zł)</label>
                        </div>
                        <div class="form__group">
                            <input type="checkbox" id="modal-product-notification"/>
                            <label for="form-link">Otrzymuj powiadomienia</label>
                        </div>

                        <div class="form__group">
                            <div>Ostatnia aktualizacja produktu:</div>
                            <div id="modal-product-last-update"></div>
                        </div>
                    </div>
                </div>
                <div class="modal__footer">
                    <button class="modal__button modal__button-secondary" onclick="closeModal()">Odrzuć</button>
                    <button id="update-product-btn" class="modal__button modal__button-primary" onclick="updateProduct()">Akceptuj</button>
                </div>
            </div>
        </div>
    </main>
</body>

</html>