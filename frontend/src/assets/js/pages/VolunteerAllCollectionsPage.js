import { populateVolunteerCollectionTable } from "/assets/js/modules/tables/CollectionTable.js";
import {getCookie} from "../api/Api.js";

let allDates = true;

function handleTableUpdate() {
    const selectedDate = allDates ? null : document.getElementById('collectionDate').value;
    const selectedCompletion = document.getElementById('completionSelector').value;
    const volunteerId = getCookie('user_id');

    populateVolunteerCollectionTable(selectedDate, selectedCompletion, volunteerId);
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('collectionDate').addEventListener('change', function() {
        allDates = false;
        handleTableUpdate();
    });

    document.getElementById('allCollectionDates').addEventListener('click', function() {
        allDates = true;
        handleTableUpdate();
    });

    document.getElementById('completionSelector').addEventListener('change', handleTableUpdate);

    handleTableUpdate();
});