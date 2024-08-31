import os
import requests
import logging
from dotenv import load_dotenv

# Load environment variables from the .env file
load_dotenv()

class TicketAPI:
    BASE_URL = os.getenv("API_ENDPOINT")
    SESSION = requests.Session()

    @staticmethod
    def create_ticket(data):
        try:
            response = TicketAPI.SESSION.post(f"{TicketAPI.BASE_URL}/tickets", json=data)
            response.raise_for_status()

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
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}")
            response.raise_for_status()

            # Vérifiez si la réponse est vide ou non au format JSON
            if not response.content.strip():
                logging.error("Réponse vide reçue de l'API")
                return {"error": "Le ticket est en cours d'assignation et cela sera traité sous peu"}

            try:
                ticket_data = response.json()

                # Vérifier si la réponse JSON est valide et contient les informations nécessaires
                if not ticket_data:
                    return {"error": "Le ticket est en cours d'assignation et cela sera traité sous peu"}

                # Scénario: Ticket fermé
                if ticket_data.get('status') == 'closed':
                    ticket_data['message'] = 'Votre ticket est fermé'
                    ticket_data['chat_enabled'] = True  # Chat visible mais en lecture seule
                    ticket_data['chat_read_only'] = True

                # Scénario: Ticket non assigné
                elif ticket_data.get('assignedTo') is None:
                    ticket_data['error'] = "Le ticket est en cours d'assignation et cela sera traité sous peu"
                    ticket_data['chat_enabled'] = False

                # Scénario: Ticket ouvert et assigné
                else:
                    ticket_data['chat_enabled'] = True
                    ticket_data['chat_read_only'] = False  # Chat complètement fonctionnel

                return ticket_data

            except ValueError as e:
                logging.error(f"Échec de l'analyse de la réponse JSON: {e}")
                return {"error": "Le ticket est en cours d'assignation et cela sera traité sous peu"}

        except requests.exceptions.RequestException as e:
            logging.error(f"Échec de la récupération du ticket: {e}")
            return {"error": str(e)}


    @staticmethod
    def update_ticket(ticket_id, data):
        try:
            response = TicketAPI.SESSION.put(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to update ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def delete_ticket(ticket_id):
        try:
            response = TicketAPI.SESSION.delete(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to delete ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def get_all_tickets():
        try:
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/tickets")
            response.raise_for_status()
            return response.json()
        except requests.RequestException as e:
            logging.error(f"Failed to get all tickets: {e}")
            return {'error': 'Failed to get all tickets'}

    @staticmethod
    def get_tickets_by_user(user_id):
        try:
            logging.debug(f"Requesting tickets for user ID: {user_id}")
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/users/{user_id}/tickets")
            response.raise_for_status()

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
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/tickets/search", params=criteria)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to search tickets: {e}")
            return {"error": str(e)}

    @staticmethod
    def auto_assign_ticket(ticket_id):
        try:
            response = TicketAPI.SESSION.put(f"{TicketAPI.BASE_URL}/tickets/assign/{ticket_id}")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to auto-assign ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def get_ticket_messages(ticket_id):
        try:
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/messages")
            response.raise_for_status()
            
            messages = response.json()
            if isinstance(messages, list):
                logging.debug(f"Messages fetched: {messages}")
                return messages
            else:
                logging.error(f"Unexpected response format: {messages}")
                return {"error": "Unexpected response format"}
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get ticket messages: {e}")
            return {"error": str(e)}

    @staticmethod
    def add_message(ticket_id, message_data):
        try:
            response = TicketAPI.SESSION.post(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/messages", json=message_data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to add message to ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def close_ticket(ticket_id, data):
        try:
            response = TicketAPI.SESSION.put(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/close", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to close ticket: {e}")
            return {"error": str(e)}


    @staticmethod
    def login(data):
        try:
            response = TicketAPI.SESSION.post(f"{TicketAPI.BASE_URL}/login", json=data)
            response.raise_for_status()

            user_info = response.json()
            if 'user_id' in user_info:
                TicketAPI.SESSION.headers.update({'User-ID': str(user_info['user_id'])})
                logging.info(f"User logged in with ID: {user_info['user_id']}")
            return user_info
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to login: {e}")
            return {"error": str(e)}

    @staticmethod
    def get_all_admins():
        try:
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/admins")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get all admins: {e}")
            return {"error": str(e)}

    @staticmethod
    def reassign_ticket(ticket_id, data):
        try:
            response = TicketAPI.SESSION.put(f"{TicketAPI.BASE_URL}/tickets/{ticket_id}/reassign", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to reassign ticket: {e}")
            return {"error": str(e)}

    @staticmethod
    def search_admin_by_name(name):
        try:
            response = TicketAPI.SESSION.get(f"{TicketAPI.BASE_URL}/admins/search", params={'name': name})
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get admin by name: {e}")
            return {"error": str(e)}

    @staticmethod
    def assign_admin_to_ticket(ticket_id, data):
        try:
            response = TicketAPI.SESSION.put(f"{TicketAPI.BASE_URL}/tickets/assign/{ticket_id}", json=data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to assign admin to ticket: {e}")
            return {"error": str(e)}
