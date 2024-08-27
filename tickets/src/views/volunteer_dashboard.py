import os
import requests
import logging
from dotenv import load_dotenv
import base64 
import json
import tkinter as tk
from tkinter import ttk, messagebox, simpledialog, filedialog
from src.api.ticket_api import TicketAPI
from src.views.chat_view import ChatView
from logging.handlers import RotatingFileHandler

load_dotenv()

# Setting up logging
logging.basicConfig(level=logging.DEBUG, handlers=[
    RotatingFileHandler('volunteer.log', maxBytes=2000, backupCount=5, delay=True)
])

class VolunteerView:
    def __init__(self, master, user_data):
        self.master = master
        self.user_data = user_data
        self.ticket_system = TicketAPI()
        
        self.master.title("Espace Bénévole")
        self.master.geometry("800x600")

        self.main_frame = tk.Frame(self.master)
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.header_frame = tk.Frame(self.main_frame)
        self.header_frame.pack(fill=tk.X)
        self.header_label = tk.Label(self.header_frame, text="Volunteer Dashboard", font=("Arial", 18))
        self.header_label.pack(pady=10)

        self.tickets_frame = tk.Frame(self.main_frame)
        self.tickets_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        self.tickets_treeview = ttk.Treeview(self.tickets_frame, columns=("ID", "Description", "Status", "Admin ID", "Created At", "Updated At"), show="headings")
        self.tickets_treeview.pack(fill=tk.BOTH, expand=True)
        self.tickets_treeview.heading("ID", text="ID du Ticket")
        self.tickets_treeview.heading("Description", text="Description du Ticket")
        self.tickets_treeview.heading("Status", text="Statut")
        self.tickets_treeview.heading("Admin ID", text="ID de l'Admin")
        self.tickets_treeview.heading("Created At", text="Créé le")
        self.tickets_treeview.heading("Updated At", text="Mis à jour le")
        self.populate_tickets()

        self.button_frame = tk.Frame(self.main_frame)
        self.button_frame.pack(fill=tk.X, pady=10)

        self.create_ticket_button = tk.Button(self.button_frame, text="Créer un Ticket", command=self.create_ticket)
        self.create_ticket_button.pack(side=tk.LEFT, padx=5)

        self.close_ticket_button = tk.Button(self.button_frame, text="Fermer le Ticket", command=self.close_ticket)
        self.close_ticket_button.pack(side=tk.LEFT, padx=5)

        self.open_chat_button = tk.Button(self.button_frame, text="Ouvrir Chat", command=self.open_chat_for_selected_ticket)
        self.open_chat_button.pack(side=tk.LEFT, padx=5)

    def populate_tickets(self):
        for item in self.tickets_treeview.get_children():
            self.tickets_treeview.delete(item)
        response = self.ticket_system.get_tickets_by_user(self.user_data['id'])

        if 'error' in response:
            messagebox.showerror("Erreur", response['error'])
            return

        tickets = response if isinstance(response, list) else []

        for ticket in tickets:
            ticket_id = ticket.get('id', '')
            description = ticket.get('description', '')
            status = ticket.get('status', '')
            admin_id = ticket.get('assignedTo', '') if ticket.get('assignedTo') is not None else ''
            created_at = ticket.get('createdAt', '')
            updated_at = ticket.get('updatedAt', '')
            self.tickets_treeview.insert("", tk.END, values=(ticket_id, description, status, admin_id, created_at, updated_at))

    def open_chat_for_selected_ticket(self):
            selected_items = self.tickets_treeview.selection()
            if selected_items:
                item = selected_items[0]
                ticket_info = self.tickets_treeview.item(item, "values")
                if ticket_info:
                    try:
                        ticket_id = int(ticket_info[0])
                        # Get the current user's ID (admin or volunteer)
                        current_user_id = self.user_data['id']
                        # Fetch the created_by or recipient user ID depending on the user's role
                        admin_id = int(ticket_info[3]) if ticket_info[3] else None
                        recipient_id = admin_id if admin_id != current_user_id else None  # Assuming recipient ID logic
                        self.open_chat_for_ticket(ticket_id, current_user_id, recipient_id)
                    except ValueError:
                        messagebox.showerror("Erreur", "ID invalide. L'ID doit être un entier.")
            else:
                messagebox.showwarning("Attention", "Veuillez sélectionner un ticket pour ouvrir le chat.")

    def open_chat_for_ticket(self, ticket_id, author_id, recipient_id):
            try:
                chat_window = tk.Toplevel(self.master)
                # Pass all required arguments to ChatView
                chat_view = ChatView(chat_window, author_id, recipient_id, ticket_id)
            except ValueError:
                messagebox.showerror("Erreur", "L'ID doit être un entier.")

    def create_ticket(self):
        # Créer une nouvelle fenêtre de dialogue pour la création de ticket
        create_window = tk.Toplevel(self.master)
        create_window.title("Créer un Ticket")
        create_window.geometry("400x300")

        # Champ pour entrer la description du ticket
        tk.Label(create_window, text="Description du Ticket:").pack(pady=5)
        description_entry = tk.Text(create_window, height=5, width=40)
        description_entry.pack(pady=5)

        # Sélection du type de ticket
        tk.Label(create_window, text="Type de Ticket:").pack(pady=5)
        ticket_type_var = tk.StringVar()
        ticket_type_combobox = ttk.Combobox(
            create_window, textvariable=ticket_type_var,
            values=['adhesion', 'collecte', 'stock', 'tournee', 'benevole', 'service'],
            state="readonly"
        )
        ticket_type_combobox.pack(pady=5)

        # Ajouter des pièces jointes
        attachments = []
        def add_attachment():
            file_path = filedialog.askopenfilename(
                title="Sélectionnez le fichier à joindre",
                filetypes=(("Tous les fichiers", "*.*"), ("Fichiers texte", "*.txt"), ("Images", "*.png;*.jpg;*.jpeg"))
            )
            if file_path:
                try:
                    with open(file_path, "rb") as file:
                        file_content = file.read()
                        # Encoder le contenu du fichier en Base64
                        encoded_content = base64.b64encode(file_content).decode('utf-8')
                        attachments.append({'filename': os.path.basename(file_path), 'content': encoded_content})
                except Exception as e:
                    logging.error(f"Failed to read attachment file: {e}")
                    messagebox.showerror("Erreur", "Erreur lors de la lecture du fichier joint.")

        add_attachment_button = tk.Button(create_window, text="Ajouter une pièce jointe", command=add_attachment)
        add_attachment_button.pack(pady=10)

        # Bouton pour créer le ticket
        def submit_ticket():
            description = description_entry.get("1.0", tk.END).strip()
            ticket_type = ticket_type_var.get()

            if not description or not ticket_type:
                messagebox.showwarning("Attention", "La description et le type de ticket ne doivent pas être vides.")
                return

            ticket_data = {
                'type': ticket_type,
                'description': description,
                'status': 'open',
                'created_by': self.user_data['id'],
                'attachments': attachments
            }

            response = self.ticket_system.create_ticket(ticket_data)
            logging.debug(f"Create Ticket Response: {response}")

            if response and 'id' in response:
                messagebox.showinfo("Succès", "Ticket créé avec succès ! Vous avez reçu un email de confirmation.")
                self.populate_tickets()
                create_window.destroy()
            else:
                messagebox.showerror("Erreur", "Échec de la création du ticket.")

        submit_button = tk.Button(create_window, text="Créer", command=submit_ticket)
        submit_button.pack(pady=10)

        # Fenêtre modale
        create_window.grab_set()
        self.master.wait_window(create_window)
    def close_ticket(self):
        selected = self.tickets_treeview.selection()
        if selected:
            ticket_info = self.tickets_treeview.item(selected[0], 'values')
            ticket_id = int(ticket_info[0])
            update_data = {'status': 'closed', 'user_id': self.user_data['id'], 'is_admin': self.user_data.get('role') == 'admin'}
            response = self.ticket_system.update_ticket(ticket_id, update_data)
            logging.debug(f"Close Ticket Response: {response}")
            if response and 'id' in response:
                messagebox.showinfo("Succès", "Ticket fermé avec succès!")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de la fermeture du ticket.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

def open_volunteer_dashboard(root, user_data):
    root.withdraw()
    dashboard = tk.Toplevel(root)
    VolunteerView(dashboard, user_data)