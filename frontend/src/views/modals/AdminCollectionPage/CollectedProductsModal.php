<div id="collectedProductsDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeCollectedProductsDetailsButton">&times;</span>

        <div class="row">
            <button class="add-button" id="addProductInModalButton"> <?php echo $data['add_button']; ?> </button>
            <button class="delete-button" id="deleteProductInModalButton"> <?php echo $data['delete_button']; ?> </button>
            <button class="modify-button" id="modifyProductInModalButton"> <?php echo $data['modify_button']; ?> </button>
        </div>

        <div id="modalBodyCollectedProductsDetails">
        </div>
        <?php
        $loaderId = 'loadingCollectedProductsDetails';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>