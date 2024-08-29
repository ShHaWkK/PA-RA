<div id="addCompanyModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddCompanyModal">&times;</span>
        <h2>Ajouter une Société</h2>
        <form id="addCompanyForm">
            <label for="addNameInput">Nom de la société :</label>
            <input type="text" id="addNameInput" name="name" required>

            <label for="addAddressInput">Adresse :</label>
            <input type="text" id="addAddressInput" name="address" required>

            <label for="addContactInfoInput">Informations de contact :</label>
            <input type="text" id="addContactInfoInput" name="contact_info" required>

            <label for="addSiretInput">SIRET :</label>
            <input type="text" id="addSiretInput" name="siret" required>

            <label for="addRenewalDateInput">Date de renouvellement :</label>
            <input type="date" id="addRenewalDateInput" name="renewal_date" required>

            <label for="addRenewalStatusSelect">Statut de renouvellement :</label>
            <select id="addRenewalStatusSelect" name="renewal_status" required>
                <option value="pending">En attente</option>
                <option value="completed">Complété</option>
            </select>

            <button type="submit">Ajouter la Société</button>
        </form>
        <?php
        $loaderId = 'loadingAddCompany';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>