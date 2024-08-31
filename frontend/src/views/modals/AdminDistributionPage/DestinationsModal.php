<div id="destinationsDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeRouteDestinationsButton">&times;</span>

        <h2> Destinations :</h2>

        <div class="row">
            <button class="add-button" id="addDestinationInModalButton"> Ajouter </button>
            <button class="delete-button" id="deleteDestinationInModalButton"> Supprimer </button>
            <button class="modify-button" id="modifyDestinationInModalButton"> Modifier </button>
        </div>

        <div id="modalBodyDestinationsDetails">
        </div>
        <?php
        $loaderId = 'loadingDestinationsDetails';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>
