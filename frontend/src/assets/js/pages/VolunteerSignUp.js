import { registerVolunteer } from "../api/Users.js";
import { getAllSkills } from "../api/Skills.js"

// Fonction pour ajouter un écouteur d'événement de soumission au formulaire
function addVolunteerSubmitEvent() {
    // Supprime les écouteurs d'événements existants, s'il y en a, pour éviter les soumissions multiples
    const form = document.getElementById('registrationForm');
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);

    newForm.addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const jsonData = {};

        // Convertir FormData en objet JSON, en ignorant les disponibilités
        formData.forEach((value, key) => {
            if (key.includes('availabilities') || key.includes('skills')) {
                // Ignorer les clés de disponibilités
                return;
            }
            if (jsonData[key]) {
                if (!Array.isArray(jsonData[key])) {
                    jsonData[key] = [jsonData[key]];
                }
                jsonData[key].push(value);
            } else {
                jsonData[key] = value;
                console.log("jsonData[key] = value", jsonData[key], value);
            }
        });

        // Gérer les disponibilités séparément
        const availabilities = [];
        const rows = document.querySelectorAll('#availabilities tr');

        rows.forEach((row, index) => {
            if (index === 0) return; // Ignorer la ligne d'en-tête

            const checkbox = row.querySelector('input[type="checkbox"]');
            if (checkbox && checkbox.checked) {
                const dayOfWeek = row.querySelector('input[name*="[day_of_week]"]').value;
                const startTime = row.querySelector('input[name*="[start_time]"]').value;
                const endTime = row.querySelector('input[name*="[end_time]"]').value;

                availabilities.push({
                    day_of_week: dayOfWeek,
                    start_time: startTime,
                    end_time: endTime
                });
            }
        });

        jsonData.availabilities = availabilities;

        // Gérer les compétences séparément
        const skills = [];
        document.querySelectorAll('input[name="skills[]"]:checked').forEach(checkbox => {
            skills.push(checkbox.value);
        });

        jsonData.skills = skills;

        console.log(jsonData);

        const result = await registerVolunteer(jsonData);

        if (!result.ok) {
            switch (result.status) {
                case 409:
                    alert("Un utilisateur avec cet e-mail existe déjà");
                    break;
                default:
                    console.log('Échec de l\'inscription du volontaire');
                    alert('Échec de l\'inscription du volontaire');
                    break;
            }
        } else {
            console.log('Volontaire inscrit avec succès');
            alert('Volontaire inscrit avec succès');
            window.location.reload();
        }
    });
}

// Fonction pour peupler le tableau des compétences en s'assurant qu'il n'est pas peuplé plusieurs fois
async function populateSkillTable(loaderID) {
    try {
        if(loaderID == null) {
            loaderID = 'loading-body';
        }

        document.getElementById(loaderID).classList.remove('hidden');
        const response = await getAllSkills();
        const skills = await response.json();
        const skillsTableBody = document.getElementById('skillsTable').querySelector('tbody');

        // Vider le contenu existant du tableau des compétences
        skillsTableBody.innerHTML = '';

        skills.forEach(skill => {
            // Créer une ligne de tableau
            const row = document.createElement('tr');

            // Colonne pour la case à cocher
            const checkboxCell = document.createElement('td');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = skill.id;
            checkbox.name = 'skills[]';
            checkboxCell.appendChild(checkbox);
            row.appendChild(checkboxCell);

            // Colonne pour le nom de la compétence
            const nameCell = document.createElement('td');
            nameCell.textContent = skill.name;
            row.appendChild(nameCell);

            // Colonne pour la description de la compétence
            const descriptionCell = document.createElement('td');
            descriptionCell.textContent = skill.description;
            row.appendChild(descriptionCell);

            // Ajouter la ligne au tableau
            skillsTableBody.appendChild(row);
        });

        // Afficher le formulaire et masquer le chargeur
        document.getElementById('registrationForm').classList.remove('hidden');
        document.getElementById(loaderID).classList.add('hidden');
    } catch (error) {
        console.error('Erreur lors de la récupération des compétences:', error.message);
    }
}

// Configuration initiale lorsque le document est prêt
document.addEventListener('DOMContentLoaded', () => {
    populateSkillTable();
    addVolunteerSubmitEvent();
});

export { populateSkillTable, addVolunteerSubmitEvent };