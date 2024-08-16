<div id="collectedProductsDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeCollectedProductsDetailsButton">&times;</span>

        <div class="row">
            <button class="add-button" id="addProductInModalButton"> Ajouter </button>
            <button class="delete-button" id="deleteProductInModalButton"> Supprimer </button>
            <button class="modify-button" id="modifyProductInModalButton"> Modifier </button>
        </div>

        <div id="modalBodyCollectedProductsDetails">
        </div>
        <?php
        $loaderId = 'loadingCollectedProductsDetails';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>