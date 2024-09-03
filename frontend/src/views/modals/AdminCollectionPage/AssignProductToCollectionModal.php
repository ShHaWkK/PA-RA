<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddProductModal">&times;</span>

        <div id="addProductContent">
            <h2><?php echo $data['add_products_title']; ?></h2>
            <div class="product-notification-table"></div>
            <button type="button" id="addProductsButton"><?php echo $data['save_button']; ?></button>
        </div>

        <?php
        $loaderId = 'loadingBodyNotification';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>

    </div>
</div>
