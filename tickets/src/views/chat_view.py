import tkinter as tk
from tkinter import messagebox
from src.api.ticket_api import TicketAPI
import json
import logging

class ChatView:
    def __init__(self, master, author_id, recipient_id, ticket_id):
        self.master = master
        self.author_id = author_id
        self.recipient_id = recipient_id
        self.ticket_id = ticket_id

        self.message_system = TicketAPI()

        self.setup_ui()

    def setup_ui(self):
        self.master.title("Chat")
        self.master.geometry("600x400")

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

        self.populate_messages()

    def populate_messages(self):
        response = self.message_system.get_ticket_messages(self.ticket_id)
        logging.debug(f"Raw messages fetched: {response}")

        if 'error' in response:
            messagebox.showerror("Erreur", response['error'])
            return

        if isinstance(response, list):
            self.chat_text.config(state=tk.NORMAL)
            self.chat_text.delete(1.0, tk.END)
            for msg in response:
                self.chat_text.insert(tk.END, f"{msg['author']}: {msg['content']}\n")
            self.chat_text.config(state=tk.DISABLED)
        else:
            logging.error(f"Unexpected response format: {response}")
            messagebox.showerror("Erreur", "Format de réponse inattendu.")

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
