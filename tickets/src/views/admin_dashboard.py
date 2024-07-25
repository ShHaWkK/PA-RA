import os
import requests
import logging
from dotenv import load_dotenv
import json
import tkinter as tk
from tkinter import ttk, messagebox, simpledialog, filedialog
from src.api.ticket_api import TicketAPI
from src.views.chat_view import ChatView
from logging.handlers import RotatingFileHandler

load_dotenv()

# Setting up logging
logging.basicConfig(level=logging.DEBUG)
handler = RotatingFileHandler('ticket_system.log', maxBytes=2000, backupCount=5)
logging.getLogger().addHandler(handler)

class AdminView:
    def __init__(self, master, user_data):
        self.master = master
        self.user_data = user_data
        self.ticket_system = TicketAPI()
        
        # Setup the UI components
        self.setup_ui()

    def setup_ui(self):
        self.master.title("Espace Administrateur")
        self.master.geometry("800x600")

        self.main_frame = tk.Frame(self.master)
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.header_frame = tk.Frame(self.main_frame)
        self.header_frame.pack(fill=tk.X)
        self.header_label = tk.Label(self.header_frame, text="Admin Dashboard", font=("Arial", 18))
        self.header_label.pack(pady=10)

        self.tickets_frame = tk.Frame(self.main_frame)
        self.tickets_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        self.tickets_treeview = ttk.Treeview(self.tickets_frame, columns=("ID", "Type", "Description", "Status", "Created By", "Admin ID", "Created At", "Updated At"), show="headings")
        self.tickets_treeview.pack(fill=tk.BOTH, expand=True)
        self.tickets_treeview.heading("ID", text="ID du Ticket")
        self.tickets_treeview.heading("Type", text="Type de Ticket")
        self.tickets_treeview.heading("Description", text="Description du Ticket")
        self.tickets_treeview.heading("Status", text="Statut")
        self.tickets_treeview.heading("Created By", text="Créé par")
        self.tickets_treeview.heading("Admin ID", text="ID de l'Admin")
        self.tickets_treeview.heading("Created At", text="Créé le")
        self.tickets_treeview.heading("Updated At", text="Mis à jour le")
        self.tickets_treeview.bind("<ButtonRelease-1>", self.open_chat_on_ticket_click)
        self.populate_tickets()

        self.button_frame = tk.Frame(self.main_frame)
        self.button_frame.pack(fill=tk.X, pady=10)

        self.create_ticket_button = tk.Button(self.button_frame, text="Créer un Ticket", command=self.create_ticket)
        self.create_ticket_button.pack(side=tk.LEFT, padx=5)

        self.close_ticket_button = tk.Button(self.button_frame, text="Fermer le Ticket", command=self.close_ticket)
        self.close_ticket_button.pack(side=tk.LEFT, padx=5)

        self.reassign_ticket_button = tk.Button(self.button_frame, text="Réassigner le Ticket", command=self.reassign_ticket)
        self.reassign_ticket_button.pack(side=tk.LEFT, padx=5)

    def reassign_ticket(self):
        selected = self.tickets_treeview.selection()
        if selected:
            ticket_info = self.tickets_treeview.item(selected[0], 'values')
            ticket_id = int(ticket_info[0])
            new_admin_id = simpledialog.askinteger("Réassigner le Ticket", "Entrez l'ID du nouvel administrateur :")
            if new_admin_id:
                update_data = {'new_admin_id': new_admin_id}
                response = self.ticket_system.reassign_ticket(ticket_id, update_data)
                logging.debug(f"Reassign Ticket Response: {response}")
                if response and 'id' in response:
                    messagebox.showinfo("Succès", "Ticket réassigné avec succès!")
                    self.populate_tickets()
                else:
                    messagebox.showerror("Erreur", "Échec de la réassignation du ticket.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

    def open_chat_on_ticket_click(self, event):
        item = self.tickets_treeview.selection()[0]
        ticket_info = self.tickets_treeview.item(item, "values")
        if ticket_info:
            try:
                ticket_id = int(ticket_info[0])
                created_by = int(ticket_info[4]) if ticket_info[4] else None
                self.open_chat_with_user(ticket_id, created_by)
            except ValueError:
                messagebox.showerror("Erreur", "ID invalide. L'ID doit être un entier.")

    def open_chat_with_user(self, ticket_id, created_by):
        try:
            self.ticket_id = int(ticket_id)
            self.created_by = int(created_by) if created_by is not None else 0
            chat_window = tk.Toplevel(self.master)
            chat_view = ChatView(chat_window, self.created_by, self.user_data['id'], self.ticket_id)
        except ValueError:
            messagebox.showerror("Erreur", "L'ID doit être un entier.")

    def populate_tickets(self):
        for item in self.tickets_treeview.get_children():
            self.tickets_treeview.delete(item)
        tickets = self.ticket_system.get_all_tickets()
        logging.debug(f"Tickets fetched from API: {json.dumps(tickets, indent=2)}")
        try:
            tickets = json.loads(tickets) if isinstance(tickets, str) else tickets
        except json.JSONDecodeError as e:
            logging.error(f"Failed to decode JSON response: {e}")
            messagebox.showerror("Erreur", "Erreur de format de réponse JSON.")
            return

        if isinstance(tickets, dict) and 'error' in tickets:
            messagebox.showerror("Erreur", tickets['error'])
        else:
            for ticket in tickets:
                logging.debug(f"Processing ticket: {ticket.get('id')}, {ticket.get('description')}")
                if isinstance(ticket, dict):
                    ticket_id = ticket.get('id', '')
                    ticket_type = ticket.get('type', '')
                    description = ticket.get('description', '')
                    status = ticket.get('status', '')
                    created_by = ticket.get('createdBy', '')
                    admin_id = ticket.get('assignedTo', '') if ticket.get('assignedTo') is not None else ''
                    created_at = ticket.get('createdAt', '').get('date', '') if isinstance(ticket.get('createdAt'), dict) else ticket.get('createdAt')
                    updated_at = ticket.get('updatedAt', '').get('date', '') if isinstance(ticket.get('updatedAt'), dict) else ticket.get('updatedAt')
                    self.tickets_treeview.insert("", tk.END, values=(ticket_id, ticket_type, description, status, created_by, admin_id, created_at, updated_at))
                    logging.debug(f"Inserted ticket into Treeview: ID={ticket_id}, Type={ticket_type}, Description={description}, Status={status}, Created By={created_by}, Admin ID={admin_id}, Created At={created_at}, Updated At={updated_at}")

    def create_ticket(self):
        title = simpledialog.askstring("Créer un Ticket", "Entrez le titre du ticket :")
        description = simpledialog.askstring("Créer un Ticket", "Entrez la description du ticket :")
        if title and description:
            ticket_data = {
                'type': 'admin',
                'description': description,
                'status': 'open',
                'created_by': self.user_data['id'],
                'attachments': []
            }
            response = self.ticket_system.create_ticket(ticket_data)
            logging.debug(f"Create Ticket Response: {response}")
            if response and 'id' in response:
                messagebox.showinfo("Succès", "Ticket créé avec succès !")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de la création du ticket.")
        else:
            messagebox.showwarning("Attention", "Le titre et la description ne doivent pas être vides.")

    def close_ticket(self):
        selected = self.tickets_treeview.selection()
        if selected:
            ticket_info = self.tickets_treeview.item(selected[0], 'values')
            ticket_id = int(ticket_info[0])
            update_data = {'status': 'closed', 'user_id': self.user_data['id'], 'is_admin': True}
            response = self.ticket_system.update_ticket(ticket_id, update_data)
            logging.debug(f"Close Ticket Response: {response}")
            if response and 'id' in response:
                messagebox.showinfo("Succès", "Ticket fermé avec succès!")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de la fermeture du ticket.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

def open_admin_dashboard(root, user_data):
    root.withdraw()
    dashboard = tk.Toplevel(root)
    AdminView(dashboard, user_data)
