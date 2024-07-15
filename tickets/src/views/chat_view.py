import tkinter as tk
from tkinter import scrolledtext, messagebox, ttk
from src.api.ticket_api import TicketAPI

class ChatView:
    def __init__(self, master, user_id, other_user_id, ticket_id):
        self.master = master
        self.user_id = user_id
        self.other_user_id = other_user_id
        self.ticket_id = ticket_id
        self.chat_manager = TicketAPI()
        self.target_language = 'fr'

        self.master.title("Chat")
        self.master.geometry("500x400")
        self.master.configure(bg="#e1f5e1")

        self.chat_box = scrolledtext.ScrolledText(master, state='disabled', bg="#ffffff", fg="#000000", font=("Arial", 10))
        self.chat_box.pack(pady=10, padx=10, fill=tk.BOTH, expand=True)

        self.msg_entry = tk.Entry(master, bg="#ffffff", fg="#000000", font=("Arial", 12))
        self.msg_entry.pack(side=tk.LEFT, expand=True, fill=tk.X, padx=10)

        self.send_button = tk.Button(master, text="Envoyer", command=self.send_message, bg="#4CAF50", fg="white", padx=10, pady=5, relief="flat", overrelief="ridge")
        self.send_button.pack(side=tk.RIGHT, padx=10)

        self.lang_label = tk.Label(master, text="Langue:", bg="#e1f5e1", font=("Arial", 10))
        self.lang_label.pack(side=tk.LEFT, padx=10)

        self.lang_selector = ttk.Combobox(master, values=['fr', 'en', 'es', 'de', 'it'])
        self.lang_selector.pack(side=tk.LEFT, padx=10)
        self.lang_selector.set('fr')
        self.lang_selector.bind("<<ComboboxSelected>>", self.change_language)

        self.update_chat()

    def send_message(self):
        message = self.msg_entry.get()
        if message.strip():
            message_data = {
                'content': message,
                'author': self.user_id
            }
            if self.chat_manager.add_message(self.ticket_id, message_data):
                self.msg_entry.delete(0, tk.END)
                self.update_chat()
            else:
                messagebox.showerror("Erreur", "Échec de l'envoi du message.")

    def update_chat(self):
        self.chat_box.config(state=tk.NORMAL)
        self.chat_box.delete('1.0', tk.END)
        messages = self.chat_manager.get_ticket_messages(self.ticket_id)
        for msg in messages:
            expediteur = "Vous" if msg['author'] == self.user_id else f"Utilisateur {msg['author']}"
            couleur = 'red' if expediteur == "Admin" else 'black'
            self.chat_box.tag_configure(expediteur, foreground=couleur)
            self.chat_box.insert(tk.END, f"{expediteur}: {msg['content']} [{msg['timestamp']}]\n", expediteur)
        self.chat_box.config(state=tk.DISABLED)
        self.master.after(5000, self.update_chat)

    def change_language(self, event):
        self.target_language = self.lang_selector.get()
        self.update_chat()
