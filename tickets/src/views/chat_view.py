import tkinter as tk
from tkinter import scrolledtext, messagebox, ttk
from src.api.ticket_api import TicketAPI
from src.api.message_api import MessageAPI
import json
import logging

class ChatView:
    def __init__(self, master, author_id, recipient_id, ticket_id):
        self.master = master
        self.author_id = author_id
        self.recipient_id = recipient_id
        self.ticket_id = ticket_id
        self.ticket_system = TicketAPI()

        self.master.title("Chat")
        self.master.geometry("600x400")

        self.main_frame = tk.Frame(self.master)
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.text_area = tk.Text(self.main_frame)
        self.text_area.pack(fill=tk.BOTH, expand=True)

        self.entry_frame = tk.Frame(self.master)
        self.entry_frame.pack(fill=tk.X)

        self.message_entry = tk.Entry(self.entry_frame)
        self.message_entry.pack(side=tk.LEFT, fill=tk.X, expand=True)

        self.send_button = tk.Button(self.entry_frame, text="Envoyer", command=self.send_message)
        self.send_button.pack(side=tk.RIGHT)

        self.populate_chat()

    def populate_chat(self):
        messages = self.ticket_system.get_ticket_messages(self.ticket_id)
        logging.debug(f"Messages fetched from API: {messages}")
        if isinstance(messages, dict) and 'error' in messages:
            logging.error(messages['error'])
        else:
            self.text_area.delete('1.0', tk.END)
            for message in messages:
                if isinstance(message, dict):
                    author = message.get('author', 'Unknown')
                    recipient = message.get('recipient', 'Unknown')
                    content = message.get('content', '')
                    self.text_area.insert(tk.END, f"{author} à {recipient}: {content}\n")

    def send_message(self):
        content = self.message_entry.get()
        if content:
            message_data = {
                'author_id': self.author_id,
                'recipient_id': self.recipient_id,
                'content': content
            }
            success = self.ticket_system.add_message(self.ticket_id, message_data)
            if success:
                self.message_entry.delete(0, tk.END)
                self.populate_chat()
            else:
                logging.error("Failed to send message")