import { getPlanning } from "../api/Planning.js";
import { getCookie } from "../api/Api.js";

async function initializeCalendar() {
    try {
        const today = moment();
        const calendarElement = document.getElementById('calendar');
        const loader = document.getElementById('loadingBodyPlanning');

        loader.classList.remove('hidden');
        calendarElement.classList.add('hidden');

        const userId = getCookie('user_id');

        const startDate = today.startOf('month').format('YYYY-MM-DD');
        const endDate = today.endOf('month').format('YYYY-MM-DD');

        const planningData = await getPlanning(userId, startDate, endDate);

        console.log(planningData);

        // Initialize events array
        const events = [];

        // Transform routes
        if (Array.isArray(planningData.routes)) {
            planningData.routes.forEach(route => {
                console.log('Raw route start time:', route.start_time);

                // Parse date and ignore time zones, use startOf('day') to ensure only date is considered
                const date = moment(route.start_time, 'DD-MM-YYYY HH:mm:ss').startOf('day');

                console.log('Route Date:', date.format());
                events.push({
                    eventName: `Livraison: ${route.name}`,
                    calendar: 'Livraisons',
                    color: 'orange',
                    date: date
                });
            });
        } else {
            console.warn('No routes data found');
        }

        // Transform collections
        if (Array.isArray(planningData.collections)) {
            planningData.collections.forEach(collection => {

                // Parse date and ignore time zones, use startOf('day') to ensure only date is considered
                const date = moment(collection.collection_date, 'YYYY-MM-DD HH:mm:ss').startOf('day');
                console.log('Collection Date:', date.format());
                events.push({
                    eventName: `Collecte: ${collection.volunteer_name}`,
                    calendar: 'Collectes',
                    color: 'blue',
                    date: date
                });
            });
        } else {
            console.warn('No collections data found');
        }

        // Transform services
        if (Array.isArray(planningData.services)) {
            console.log("services",planningData.services);
            planningData.services.forEach(service => {
                console.log("service",service);

                // Parse date and ignore time zones, use startOf('day') to ensure only date is considered
                const date = moment(service.service.schedule, 'DD-MM-YYYY HH:mm:ss').startOf('day');
                console.log('Service Date:', date.format());
                events.push({
                    eventName: `Service: ${service.service.name}`,
                    calendar: 'Services',
                    color: 'green',
                    date: date
                });
            });
        } else {
            console.warn('No services data found');
        }

        loader.classList.add('hidden');
        calendarElement.classList.remove('hidden');

        console.log("events added", events);

        const calendar = new Calendar('#calendar', events);

    } catch (error) {
        console.error('Error initializing calendar:', error.message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initializeCalendar();
});