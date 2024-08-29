import { populateCompanyTable } from "../modules/tables/CompaniesTable.js";
import { deleteCompany, getCompany, updateCompany, createCompany } from "../api/Companies.js";

async function deleteCompanies() {
    const selectedRadio = document.querySelector('input[name="CompanySelection"]:checked');
    const selectedCompanyId = selectedRadio ? selectedRadio.value : null;

    if (!selectedCompanyId) {
        alert("Veuillez sélectionner au moins une société à supprimer.");
        return;
    }

    const isConfirmed = confirm("Êtes-vous sûr de vouloir supprimer cette société ? Cette action est définitive.");

    if (!isConfirmed) {
        return;
    }

    try {
        await deleteCompany(selectedCompanyId);
        alert("Société supprimée avec succès");
        await populateCompanyTable();
    } catch (error) {
        console.error("Erreur lors de la suppression de la société :", error.message);
        alert("Une erreur est survenue lors de la suppression de la société.");
    }
}

async function populateEditCompanyModalData(companyId) {
    const formElement = document.getElementById('editCompanyForm');
    const loadingIndicator = document.getElementById('loadingEditCompany');

    formElement.classList.add('hidden');
    loadingIndicator.classList.remove('hidden');

    try {
        const companyData = await getCompany(companyId);
        if (!companyData) {
            throw new Error('Données de la société non trouvées');
        }

        console.log("company Data",companyData);

        document.getElementById('companyId').value = companyData.id ?? '';
        document.getElementById('editNameInput').value = companyData.name ?? '';
        document.getElementById('editAddressInput').value = companyData.address ?? '';
        document.getElementById('editContactInfoInput').value = companyData.contact_info ?? '';
        document.getElementById('editSiretInput').value = companyData.siret ?? '';
        document.getElementById('editRenewalDateInput').value = companyData.renewal_date ?? '';
        document.getElementById('editRenewalStatusSelect').value = companyData.renewal_status ?? '';

    } catch (error) {
        console.error('Erreur lors du chargement des données de la société :', error.message);
        alert('Une erreur est survenue lors du chargement des données de la société. Veuillez réessayer plus tard.');
    } finally {
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

async function editCompany(companyId) {
    const formElement = document.getElementById('editCompanyForm');
    const loadingIndicator = document.getElementById('loadingEditCompany');
    const modalElement = document.getElementById('editCompanyModal');

    loadingIndicator.classList.remove('hidden');
    formElement.classList.add('hidden');

    const name = document.getElementById('editNameInput').value.trim();
    const address = document.getElementById('editAddressInput').value.trim();
    const contactInfo = document.getElementById('editContactInfoInput').value.trim();
    const siret = document.getElementById('editSiretInput').value.trim();
    const renewalDate = document.getElementById('editRenewalDateInput').value;
    const renewalStatus = document.getElementById('editRenewalStatusSelect').value;

    if (!name || !address || !contactInfo || !siret || !renewalDate || !renewalStatus) {
        alert('Tous les champs sont requis.');
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
        return;
    }

    const companyData = {
        name,
        address,
        contact_info: contactInfo,
        siret,
        renewal_date: renewalDate,
        renewal_status: renewalStatus
    };

    try {
        await updateCompany(companyId, companyData);
        alert("Société modifiée avec succès");
        modalElement.style.display = 'none';
        await populateCompanyTable();
    } catch (error) {
        console.error('Erreur lors de la modification de la société :', error);
        alert('Erreur lors de la modification de la société. Veuillez réessayer.');
    } finally {
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

async function addCompany() {
    const formElement = document.getElementById('addCompanyForm');
    const loadingIndicator = document.getElementById('loadingAddCompany');
    const modalElement = document.getElementById('addCompanyModal');

    loadingIndicator.classList.remove('hidden');
    formElement.classList.add('hidden');

    const name = document.getElementById('addNameInput').value.trim();
    const address = document.getElementById('addAddressInput').value.trim();
    const contactInfo = document.getElementById('addContactInfoInput').value.trim();
    const siret = document.getElementById('addSiretInput').value.trim();
    const renewalDate = document.getElementById('addRenewalDateInput').value;
    const renewalStatus = document.getElementById('addRenewalStatusSelect').value;

    if (!name || !address || !contactInfo || !siret || !renewalDate || !renewalStatus) {
        alert('Tous les champs sont requis.');
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
        return;
    }

    const companyData = {
        name,
        address,
        contact_info: contactInfo,
        siret,
        renewal_date: renewalDate,
        renewal_status: renewalStatus
    };

    try {
        await createCompany(companyData);
        alert("Société ajoutée avec succès");
        modalElement.style.display = 'none';
        await populateCompanyTable();
    } catch (error) {
        console.error('Erreur lors de l\'ajout de la société :', error);
        alert('Erreur lors de l\'ajout de la société. Veuillez réessayer.');
    } finally {
        loadingIndicator.classList.add('hidden');
        formElement.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    populateCompanyTable();
    document.getElementById('deleteCompanyButton').addEventListener('click', deleteCompanies);

    // Fenêtre de modification de la société
    const editCompanyModal = document.getElementById("editCompanyModal");
    const editCompanySpan = document.getElementById("closeEditCompanyModal");
    const editCompanyForm = document.getElementById("editCompanyForm");
    const editCompanyButton = document.getElementById("modifyCompanyButton");

    editCompanyButton.onclick = function () {
        const selectedCheckbox = document.querySelector('input[name="CompanySelection"]:checked');
        const selectedCompanyID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedCompanyID) {
            alert("Veuillez sélectionner une société.");
            return;
        }

        document.getElementById("editCompanyModal").style.display = "block";
        populateEditCompanyModalData(selectedCompanyID);
    }

    editCompanySpan.onclick = function () {
        editCompanyModal.style.display = "none";
    };

    editCompanyForm.onsubmit = async function (event) {
        event.preventDefault();

        const selectedCheckbox = document.querySelector('input[name="CompanySelection"]:checked');
        const selectedCompanyID = selectedCheckbox ? selectedCheckbox.value : null;

        if (!selectedCompanyID) {
            alert("Veuillez sélectionner une société.");
            return;
        }

        await editCompany(selectedCompanyID);
    };

    // Fenêtre d'ajout de la société
    const addCompanyModal = document.getElementById("addCompanyModal");
    const addCompanySpan = document.getElementById("closeAddCompanyModal");
    const addCompanyButton = document.getElementById("addCompanyButton");
    const addCompanyForm = document.getElementById("addCompanyForm");

    addCompanyButton.onclick = function () {
        addCompanyModal.style.display = "block";
    };

    addCompanySpan.onclick = function () {
        addCompanyModal.style.display = "none";
    };

    addCompanyForm.onsubmit = async function (event) {
        event.preventDefault();
        await addCompany();
    };
});