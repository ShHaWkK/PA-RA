<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddProductModal">&times;</span>

        <div id="addProductContent">
            <h2>Ajouter des produits à la collecte</h2>
            <div class="product-notification-table"></div>
            <button type="button" id="addProductsButton">Enregistrer</button>
        </div>

        <?php
        $loaderId = 'loadingBodyNotification';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

    </div>
</div>