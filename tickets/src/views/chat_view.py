import tkinter as tk
from tkinter import messagebox
from src.api.ticket_api import TicketAPI
import logging

class ChatView:
    def __init__(self, master, author_id, recipient_id, ticket_id):
        self.master = master
        self.author_id = author_id
        self.recipient_id = recipient_id
        self.ticket_id = ticket_id

        self.message_system = TicketAPI()

        self.setup_ui()
        self.check_ticket_status()

    def setup_ui(self):
        self.master.title("Chat")
        self.master.geometry("800x600")

        self.chat_frame = tk.Frame(self.master)
        self.chat_frame.pack(fill=tk.BOTH, expand=True)

        self.chat_text = tk.Text(self.chat_frame, state=tk.DISABLED)
        self.chat_text.pack(fill=tk.BOTH, expand=True, padx=10, pady=10)

        self.entry_frame = tk.Frame(self.master)
        self.entry_frame.pack(fill=tk.X, padx=10, pady=10)

        self.message_entry = tk.Entry(self.entry_frame)
        self.message_entry.pack(side=tk.LEFT, fill=tk.X, expand=True)

        self.send_button = tk.Button(self.entry_frame, text="Envoyer", command=self.send_message)
        self.send_button.pack(side=tk.RIGHT)

    def check_ticket_status(self):
        ticket_info = self.message_system.get_ticket(self.ticket_id)
        logging.debug(f"Ticket info fetched: {ticket_info}")

        if 'error' in ticket_info:
            self.disable_chat(ticket_info['error'])
            return

        if not ticket_info.get('chat_enabled', True):
            self.disable_chat(ticket_info.get('message', 'Chat is disabled'))
        elif ticket_info.get('chat_read_only', False):
            self.populate_messages(read_only=True)
        else:
            self.populate_messages()

    def disable_chat(self, reason):
        self.chat_text.config(state=tk.NORMAL)
        self.chat_text.delete(1.0, tk.END)
        self.chat_text.insert(tk.END, reason)
        self.chat_text.config(state=tk.DISABLED)

        self.message_entry.config(state=tk.DISABLED)
        self.send_button.config(state=tk.DISABLED)

    def populate_messages(self, read_only=False):
        response = self.message_system.get_ticket_messages(self.ticket_id)
        logging.debug(f"Raw messages fetched: {response}")

        if isinstance(response, dict) and 'message' in response and response['message'] == "Il n'y a aucun message dans ce ticket.":
            self.chat_text.config(state=tk.NORMAL)
            self.chat_text.delete(1.0, tk.END)
            self.chat_text.insert(tk.END, "Il n'y a aucun message dans ce ticket.\n")
            self.chat_text.config(state=tk.DISABLED)
        elif isinstance(response, list):
            self.chat_text.config(state=tk.NORMAL)
            self.chat_text.delete(1.0, tk.END)

            for msg in response:
                if isinstance(msg.get('author'), dict):
                    author_name = f"{msg['author'].get('firstName', 'Unknown')} {msg['author'].get('lastName', '')}".strip()
                else:
                    author_name = msg.get('author', 'Unknown')


                if isinstance(msg.get('recipient'), dict):
                    recipient_name = f"{msg['recipient'].get('firstName', 'Unknown')} {msg['recipient'].get('lastName', '')}".strip()
                else:
                    recipient_name = msg.get('recipient', 'Unknown')

                content = msg.get('content', '')
                self.chat_text.insert(tk.END, f"{author_name} to {recipient_name}: {content}\n")

            self.chat_text.config(state=tk.DISABLED)

            if not read_only:
                self.message_entry.config(state=tk.NORMAL)
                self.send_button.config(state=tk.NORMAL)
            else:
                self.message_entry.config(state=tk.DISABLED)
                self.send_button.config(state=tk.DISABLED)
        else:
            logging.error(f"Unexpected response format: {response}")
            self.chat_text.config(state=tk.NORMAL)
            self.chat_text.delete(1.0, tk.END)
            self.chat_text.insert(tk.END, "Erreur lors du chargement des messages.\n")
            self.chat_text.config(state=tk.DISABLED)

    def send_message(self):
        content = self.message_entry.get()
        if content:
            message_data = {
                'author_id': self.author_id,
                'recipient_id': self.recipient_id,
                'content': content
            }
            logging.debug(f"Sending message data: {message_data}")
            response = self.message_system.add_message(self.ticket_id, message_data)
            logging.debug(f"Send message response: {response}")

            if 'error' in response:
                logging.error("Failed to send message")
                messagebox.showerror("Erreur", "Échec de l'envoi du message.")
            else:
                messagebox.showinfo("Succès", "Message envoyé avec succès !")
                self.populate_messages()
                self.message_entry.delete(0, tk.END)
        else:
            messagebox.showwarning("Attention", "Le message ne peut pas être vide.")
