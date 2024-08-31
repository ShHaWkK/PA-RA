import {formatDateToFrench, parseDate} from "../FormatDate.js";
import { getAllCompanies } from "../../api/Companies.js";
import { populateCompanyEmployeesModal } from "../../pages/AdminCompanyPage.js";

export async function populateCompanyTable(queryParameters) {
    const loader = document.getElementById('loadingBodyCompany');
    const tableContainer = document.querySelector('.company-table');

    try {
        // Afficher le loader
        if (loader) {
            loader.classList.remove('hidden');
            console.log('Loader shown.');
        } else {
            console.error('Loader element not found.');
        }

        // Cacher la table si elle existe
        if (tableContainer) {
            tableContainer.classList.add('hidden');
        } else {
            console.error('Company table container not found.');
            return;
        }

        // Récupérer les sociétés
        const companies = await getAllCompanies(queryParameters);
        console.log("Retrieved companies:", companies);

        // Vérifiez si des sociétés sont disponibles
        if (!companies || companies.length === 0) {
            console.log('No companies found');
            tableContainer.innerHTML = '<p>No companies available.</p>'; // Afficher un message si aucune société n'est trouvée
            if (loader) {
                loader.classList.add('hidden');
                console.log('Loader hidden.');
            }
            return;
        }

        // Créer la structure de la table
        const table = document.createElement('table');
        table.classList.add('company-table'); // Ajouter une classe pour le style

        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        // Définir les en-têtes de la table
        const headers = ['', 'Name', 'Address', 'Contact Info', 'SIRET', 'Renewal Date', 'Renewal Status', 'Employees', 'Created At', 'Updated At'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Créer le tbody
        const tbody = document.createElement('tbody');

        // Ajouter les lignes au tbody
        companies.forEach(company => {
            const row = document.createElement('tr');
            row.dataset.companyId = company.id;

            // Ajout du radio-button dans la première cellule
            const radioCell = document.createElement('td');
            const radioButton = document.createElement('input');
            radioButton.type = 'radio';
            radioButton.name = 'CompanySelection';
            radioButton.value = company.id;
            radioCell.appendChild(radioButton);
            row.appendChild(radioCell);

            const nameCell = document.createElement('td');
            nameCell.textContent = company.name;

            const addressCell = document.createElement('td');
            addressCell.textContent = company.address;

            const contactInfoCell = document.createElement('td');
            contactInfoCell.textContent = company.contact_info;

            const siretCell = document.createElement('td');
            siretCell.textContent = company.siret;

            const renewalDateCell = document.createElement('td');
            renewalDateCell.textContent = company.renewal_date ? formatDateToFrench(company.renewal_date) : 'N/A';

            const renewalStatusCell = document.createElement('td');
            renewalStatusCell.textContent = company.renewal_status;

            const employeesButtonCell = document.createElement('td');
            const employeesButton = document.createElement('button');
            employeesButton.textContent = 'Voir';
            employeesButton.onclick = () => viewEmployees(company.id);
            employeesButtonCell.appendChild(employeesButton);

            const createdAtCell = document.createElement('td');
            createdAtCell.textContent = company.created_at ? formatDateToFrench(company.created_at) : 'N/A';

            const updatedAtCell = document.createElement('td');
            updatedAtCell.textContent = company.updated_at ? formatDateToFrench(company.updated_at) : 'N/A'

            // Ajouter toutes les cellules à la ligne
            row.appendChild(radioCell);
            row.appendChild(nameCell);
            row.appendChild(addressCell);
            row.appendChild(contactInfoCell);
            row.appendChild(siretCell);
            row.appendChild(renewalDateCell);
            row.appendChild(renewalStatusCell);
            row.appendChild(employeesButtonCell);
            row.appendChild(createdAtCell);
            row.appendChild(updatedAtCell);

            tbody.appendChild(row);
        });

        // Attacher le tbody à la table
        table.appendChild(tbody);

        // Réinitialiser le contenu du conteneur
        tableContainer.innerHTML = '';
        // Attacher la table complète au conteneur
        tableContainer.appendChild(table);

    } catch (error) {
        console.error('Error in populateCompanyTable:', error.message);
    } finally {
        // Cacher le loader
        if (loader) {
            loader.classList.add('hidden');
            console.log('Loader hidden.');
        }

        // Afficher la table
        if (tableContainer) {
            tableContainer.classList.remove('hidden');
        }
    }
}

function viewEmployees(companyId) {
    populateCompanyEmployeesModal(companyId);
    const employeeModal = document.getElementById("companyEmployeesModal");
    employeeModal.style.display = "block";
}

