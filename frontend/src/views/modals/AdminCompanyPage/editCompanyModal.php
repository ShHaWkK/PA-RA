<div id="editCompanyModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditCompanyModal">&times;</span>
        <h2>Modifier la Société</h2>
        <form id="editCompanyForm">
            <input type="hidden" id="companyId" name="id">

            <label for="editNameInput">Nom de la société :</label>
            <input type="text" id="editNameInput" name="name" required>

            <label for="editAddressInput">Adresse :</label>
            <input type="text" id="editAddressInput" name="address" required>

            <label for="editContactInfoInput">Informations de contact :</label>
            <input type="text" id="editContactInfoInput" name="contact_info" required>

            <label for="editSiretInput">SIRET :</label>
            <input type="text" id="editSiretInput" name="siret" required>

            <label for="editRenewalDateInput">Date de renouvellement :</label>
            <input type="date" id="editRenewalDateInput" name="renewal_date" required>

            <label for="editRenewalStatusSelect">Statut de renouvellement :</label>
            <select id="editRenewalStatusSelect" name="renewal_status" required>
                <option value="pending">En attente</option>
                <option value="completed">Complété</option>
            </select>

            <button type="submit">Enregistrer les Modifications</button>
        </form>
        <?php
        $loaderId = 'loadingEditCompany';
        include ($_SERVER['DOCUMENT_ROOT'] . '/views/includes/Loader.php'); ?>
    </div>
</div>