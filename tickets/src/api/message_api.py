import requests
import os
import logging

# path : tickets/src/api/message_api.py

class MessageAPI:
    BASE_URL = os.getenv("API_ENDPOINT")

    def get_messages(self, ticket_id):
        url = f"{self.BASE_URL}/tickets/{ticket_id}/messages"
        try:
            response = requests.get(url)
            response.raise_for_status()
            return response.json()
        except requests.RequestException as e:
            logging.error(f"Failed to get messages: {e}")
            return {'error': 'Failed to get messages'}
    @staticmethod
    def get_ticket_messages(ticket_id):
        try:
            response = requests.get(f"{MessageAPI.BASE_URL}/tickets/{ticket_id}/messages")
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to get ticket messages: {e}")
            return {"error": str(e)}

    @staticmethod
    def add_message(ticket_id, message_data):
        try:
            response = requests.post(f"{MessageAPI.BASE_URL}/tickets/{ticket_id}/messages", json=message_data)
            response.raise_for_status()
            return response.json()
        except requests.exceptions.RequestException as e:
            logging.error(f"Failed to add message to ticket: {e}")
            return {"error": str(e)}