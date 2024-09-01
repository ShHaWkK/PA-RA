// Fonction pour formater les dates au format francophone
export function formatDateToFrench(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

export function parseDate(dateString) {
    // Split the date and time parts
    const [datePart, timePart] = dateString.split(' ');
    const [day, month, year] = datePart.split('-');
    const [hours, minutes, seconds] = timePart.split(':');

    // Construct a date string in ISO format (YYYY-MM-DDTHH:MM:SS)
    const isoDateString = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
    return new Date(isoDateString);
}

export function extractDateTime(dateTimeStr) {
    // Décomposer la chaîne en date et heure
    const [date, time] = dateTimeStr.split(' ');

    // Extraire la date (format JJ-MM-AAAA)
    const [day, month, year] = date.split('-');
    const formattedDate = `${day}-${month}-${year}`;

    // Extraire l'heure (format HH:MM:SS)
    return {
        dateOnly: formattedDate,
        timeOnly: time,
    };
}