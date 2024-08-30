import {getPlanning} from "../api/Planning.js";
import {getCookie} from "../api/Api.js";

async function initializeCalendar() {
    try {
        var today = moment();

        const calendarElement = document.getElementById('calendar');
        const loader = document.getElementById('loadingBodyPlanning');

        loader.classList.remove('hidden');
        calendarElement.classList.add('hidden');

        const userId = getCookie('user_id');
        const startDate = today.startOf('month').format('YYYY-MM-DD');
        const endDate = today.endOf('month').format('YYYY-MM-DD');

        const planningData = await getPlanning(userId, startDate, endDate);

        // Transform the planning data into the format expected by the calendar
        const events = [];

        planningData.routes.forEach(route => {
            events.push({
                eventName: `Livraison: ${route.name}`,
                calendar: 'Livraisons',
                color: 'orange',
                date: moment(route.start_time, 'DD-MM-YYYY HH:mm:ss')
            });
        });

        planningData.collections.forEach(collection => {
            events.push({
                eventName: `Collecte: ${collection.volunteer_name}`,
                calendar: 'Collectes',
                color: 'blue',
                date: moment(collection.collection_date, 'DD-MM-YYYY HH:mm:ss')
            });
        });

        loader.classList.add('hidden');

        var calendar = new Calendar('#calendar', events);

        calendarElement.classList.remove('hidden');

    } catch (error) {
        console.error('Error initializing calendar:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initializeCalendar();
});