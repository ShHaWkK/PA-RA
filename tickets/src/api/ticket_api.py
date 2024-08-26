import os
import requests
import logging
from dotenv import load_dotenv

# Load environment variables from the .env file
load_dotenv()

class TicketAPI:
    BASE_URL = os.getenv("API_ENDPOINT")

    @staticmethod
    def create_ticket(data):
        try:
            response = requests.post(f"{TicketAPI.BASE_URL}/tickets", json=data)
            response.raise_for_status()

            # Vérification du format de la réponse
            result = response.json()
            if 'id' not in result or 'message' not in result:
                logging.error(f"Unexpected response format: {result}")
                return {"error": "Unexpected response format from API"}

            return result
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to create ticket: {e}")
            return {"error": str(e)}
        except ValueError as e:
            logging.error(f"Failed to parse JSON response: {e}")
            return {"error": "Invalid JSON format"}


    @staticmethod
    def get_ticket(ticket_id):
        try:
            response = requests.get(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def update_ticket(ticket_id, data):
        try:
            response = requests.put(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to update ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def delete_ticket(ticket_id):
        try:
            response = requests.delete(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to delete ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def get_all_tickets():
        try:
            response = requests.get(f"{TicketAPI.BASE_URL}/tickets")
            response.raise_for_status()
            return response.json()
        except requests.RequestException as e:
            logging.error(f"Failed to get all tickets: {e}")
            return {'error': 'Failed to get all tickets'}

    @staticmethod
    def get_tickets_by_user(user_id):
            try:
                logging.debug(f"Requesting tickets for user ID: {user_id}")
                response = requests.get(f"{TicketAPI.BASE_URL}/users/{user_id}/tickets")
                response.raise_for_status()

                logging.debug(f"Raw response content: {response.content}")

                if not response.content:
                    logging.error("Empty response received from the API")
                    return {"error": "Empty response from API"}

                return response.json()
            except requests.exceptions.RequestException as e:
                logging.error(f"Failed to get tickets by user: {e}")
                return {"error": str(e)}
            except ValueError as e:
                logging.error(f"Failed to parse JSON response: {e}")
                return {"error": "Invalid JSON format"}

    @staticmethod
    def search_tickets(criteria):
        try:
            response = requests.get(f"{TicketAPI.BASE_URL}/tickets/search", params=criteria)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to search tickets: {e}")
            return {"error": str(e)}

    @staticmethod
    def auto_assign_ticket(ticket_id):
        try:
            response = requests.put(f"{TicketAPI.BASE_URL}/tickets/assign/{ticket_id}")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to auto-assign ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def get_ticket_messages(ticket_id):
        try:
            response = requests.get(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/messages")
            response.raise_for_status()
            if response.content:
                return response.json()
            else:
                return []
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get ticket messages: {e}")
            return {"error": str(e)}

    @staticmethod
    def add_message(ticket_id, message_data):
        try:
            response = requests.post(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/messages", json=message_data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to add message to ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def close_ticket(ticket_id, data):
        try:
            response = requests.put(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/close", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to close ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def login(data):
        try:
            response = requests.post(f"{TicketAPI.BASE_URL}/login", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to login: {e}")
            return {"error": str(e)}

    @staticmethod
    def get_all_admins():
        try:
            response = requests.get(f"{TicketAPI.BASE_URL}/admins")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get all admins: {e}")
            return {"error": str(e)}

    @staticmethod
    def reassign_ticket(ticket_id, data):
        try:
            response = requests.put(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/reassign", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to reassign ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def search_admin_by_name(name):
        try:
            response = requests.get(f"{TicketAPI.BASE_URL}/admins/search", params={'name': name})
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get admin by name: {e}")
            return {"error": str(e)}

    @staticmethod
    def assign_admin_to_ticket(ticket_id, data):
        try:
            response = requests.put(f"{TicketAPI.BASE_URL}/tickets/assign/{ticket_id}", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to assign admin to ticket: {e}")
            return {"error": str(e)}
