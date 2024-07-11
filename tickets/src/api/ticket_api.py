import requests

class TicketAPI:
    BASE_URL = "http://localhost:80/tickets"

    @staticmethod
    def create_ticket(data):
        response = requests.post(TicketAPI.BASE_URL, json=data)
        return response.json()

    @staticmethod
    def get_ticket(ticket_id):
        response = requests.get(f"{TicketAPI.BASE_URL}/{ticket_id}")
        return response.json()

    @staticmethod
    def update_ticket(ticket_id, data):
        response = requests.put(f"{TicketAPI.BASE_URL}/{ticket_id}", json=data)
        return response.json()

    @staticmethod
    def delete_ticket(ticket_id):
        response = requests.delete(f"{TicketAPI.BASE_URL}/{ticket_id}")
        return response.json()

    @staticmethod
    def get_all_tickets():
        response = requests.get(TicketAPI.BASE_URL)
        return response.json()
