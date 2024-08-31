<link rel="stylesheet" href="/assets/css/modal.css">

<div id="productDetailModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeProductButton">&times;</span>
        <div id="modalBodyProductDetail">
        </div>
        <?php
        $loaderId = 'loadingProductDetail';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>