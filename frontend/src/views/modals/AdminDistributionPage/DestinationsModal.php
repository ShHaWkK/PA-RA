<div id="destinationsDetailsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeRouteDestinationsButton">&times;</span>

        <h2><?php echo $data['destinations_title']; ?></h2>

        <div class="row">
            <button class="add-button" id="addDestinationInModalButton"><?php echo $data['add_button']; ?></button>
            <button class="delete-button" id="deleteDestinationInModalButton"><?php echo $data['delete_button']; ?></button>
            <button class="modify-button" id="modifyDestinationInModalButton"><?php echo $data['modify_button']; ?></button>
        </div>

        <div id="modalBodyDestinationsDetails">
            <!-- Destination details will be loaded here -->
        </div>

        <?php
        $loaderId = 'loadingDestinationsDetails';
        include($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php');
        ?>
    </div>
</div>