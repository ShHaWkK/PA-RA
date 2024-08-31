import { getAllRoutes } from '/assets/js/api/Distributions.js';
import { formatDateToFrench } from "../modules/FormatDate.js";
import { populateVolunteerRouteTable } from "../modules/tables/RouteTable.js"
import {getCookie} from "../api/Api.js";

let allDates = true;

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('distributionDate').addEventListener('change', function() {
        allDates = false;
        handleTableUpdate();
    });

    document.getElementById('allDistributionDates').addEventListener('click', function() {
        allDates = true;
        const volunteerId = getCookie('user_id');
        populateVolunteerRouteTable(volunteerId);
    });

    document.getElementById('completionSelector').addEventListener('change', handleTableUpdate);

    function handleTableUpdate() {
        const selectedDate = allDates ? null : document.getElementById('distributionDate').value;
        const selectedCompletion = document.getElementById('completionSelector').value;
        populateVolunteerRouteTable(selectedDate, selectedCompletion);
    }

    populateVolunteerRouteTable();
});