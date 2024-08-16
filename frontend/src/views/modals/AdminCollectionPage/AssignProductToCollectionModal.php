<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddProductModal">&times;</span>
        <h2>Ajouter des produits à la collecte</h2>
        <form id="addProductForm">
            <div id="productListContainer">
                <!-- Les champs pour les produits seront ajoutés ici dynamiquement -->
            </div>
            <button type="button" id="addProductButton">Ajouter un produit</button>
            <button type="submit">Enregistrer</button>
        </form>
        <?php
        $loaderId = 'loadingAddProduct';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>