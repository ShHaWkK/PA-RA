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

    def send_message(self, data):
        url = f"{self.BASE_URL}/messages"
        try:
            response = requests.post(url, json=data)
            response.raise_for_status()
            return response.json()
        except requests.RequestException as e:
            logging.error(f"Failed to send message: {e}")
            return {'error': 'Failed to send message'}
