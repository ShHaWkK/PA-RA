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

    @staticmethod
    def get_ticket_messages(ticket_id):
        response = requests.get(f"{TicketAPI.BASE_URL}/{ticket_id}/messages")
        return response.json()

    @staticmethod
    def add_message(ticket_id, message_data):
        response = requests.post(f"{TicketAPI.BASE_URL}/{ticket_id}/messages", json=message_data)
        return response.status_code == 200

    @staticmethod
    def close_ticket(ticket_id, data):
        response = requests.put(f"{TicketAPI.BASE_URL}/{ticket_id}/close", json=data)
        return response.status_code == 200

    @staticmethod
    def login(data):
        response = requests.post(f"http://localhost:80/login", json=data)
        return response
