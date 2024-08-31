import os
import requests
import logging
from dotenv import load_dotenv
import json
import tkinter as tk
from tkinter import ttk, messagebox, simpledialog, filedialog, StringVar
from src.api.ticket_api import TicketAPI
from src.views.chat_view import ChatView
from logging.handlers import RotatingFileHandler
from datetime import datetime

load_dotenv()

# Setting up logging
logging.basicConfig(level=logging.DEBUG)
handler = RotatingFileHandler('admin.log', maxBytes=2000, backupCount=5)
logging.getLogger().addHandler(handler)

class AdminView:
    def __init__(self, master, user_data):
        self.master = master
        self.user_data = user_data
        self.ticket_system = TicketAPI()
        self.admins = self.fetch_admins()
        self.selected_admin_id = None
        self.admin_id_to_name = {admin['id']: f"{admin['firstName']} {admin['lastName']}" for admin in self.admins}
        
        # Setup the UI components
        self.setup_ui()

    def fetch_admins(self):
        admins = self.ticket_system.get_all_admins()
        if 'error' in admins:
            logging.error(f"Failed to fetch admins: {admins['error']}")
            return []
        return admins

    def setup_ui(self):
        self.master.title("Espace Administrateur")
        self.master.geometry("800x600")
        self.master.configure(bg='#E8F4E5')  # Thème de fond

        self.main_frame = tk.Frame(self.master, bg='#E8F4E5')
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.header_frame = tk.Frame(self.main_frame, bg='#3B6E22')
        self.header_frame.pack(fill=tk.X)
        self.header_label = tk.Label(self.header_frame, text="Admin Dashboard", font=("Arial", 18), bg='#3B6E22', fg='white')
        self.header_label.pack(pady=10)

        self.tickets_frame = tk.Frame(self.main_frame, bg='#E8F4E5')
        self.tickets_frame.pack(fill=tk.BOTH, expand=True, padx=20, pady=20)
        self.tickets_treeview = ttk.Treeview(self.tickets_frame, columns=("ID", "Type", "Description", "Status", "Created By", "Admin Name", "Created At", "Updated At"), show="headings")
        self.tickets_treeview.pack(fill=tk.BOTH, expand=True)
        self.tickets_treeview.heading("ID", text="ID du Ticket")
        self.tickets_treeview.heading("Type", text="Type de Ticket")
        self.tickets_treeview.heading("Description", text="Description du Ticket")
        self.tickets_treeview.heading("Status", text="Statut")
        self.tickets_treeview.heading("Created By", text="Créé par")
        self.tickets_treeview.heading("Admin Name", text="Admin Assigné")
        self.tickets_treeview.heading("Created At", text="Créé le")
        self.tickets_treeview.heading("Updated At", text="Mis à jour le")
        self.populate_tickets()

        self.button_frame = tk.Frame(self.main_frame, bg='#E8F4E5')
        self.button_frame.pack(fill=tk.X, pady=10)

        self.create_ticket_button = tk.Button(self.button_frame, text="Créer un Ticket", command=self.create_ticket, bg='#4CAF50', fg='white')
        self.create_ticket_button.pack(side=tk.LEFT, padx=5)

        self.close_ticket_button = tk.Button(self.button_frame, text="Fermer le Ticket", command=self.close_ticket, bg='#F44336', fg='white')
        self.close_ticket_button.pack(side=tk.LEFT, padx=5)

        self.assign_ticket_button = tk.Button(self.button_frame, text="Assigner un Admin", command=self.assign_ticket, bg='#FF9800', fg='white')
        self.assign_ticket_button.pack(side=tk.LEFT, padx=5)

        self.open_chat_button = tk.Button(self.button_frame, text="Ouvrir Chat", command=self.open_chat_for_selected_ticket, bg='#2196F3', fg='white')
        self.open_chat_button.pack(side=tk.LEFT, padx=5)

    def show_loader(self, message="Chargement..."):
        """Afficher un loader dynamique."""
        self.loader_window = tk.Toplevel(self.master)
        self.loader_window.title(message)
        self.loader_window.geometry("300x100")
        self.loader_window.configure(bg='#E8F4E5')
        
        # Barre de progression
        self.progress = ttk.Progressbar(self.loader_window, orient=tk.HORIZONTAL, length=250, mode='indeterminate')
        self.progress.pack(pady=20)
        self.progress.start()  # Démarre l'animation de la barre de progression

        self.loader_window.update()  # Mise à jour de la fenêtre pour afficher immédiatement le loader

    def hide_loader(self):
        """Fermer la fenêtre de chargement."""
        if hasattr(self, 'loader_window'):
            self.progress.stop()  # Arrête l'animation de la barre de progression
            self.loader_window.destroy()

    def format_date(self, date_str):
        if date_str:
            try:
                return datetime.strptime(date_str, "%Y-%m-%d %H:%M:%S").strftime("%Y-%m-%d %H:%M:%S")
            except ValueError as e:
                logging.error(f"Date format error: {e}")
                return "Invalid date"
        return "No date"

    def assign_ticket(self):
        selected = self.tickets_treeview.selection()
        if selected:
            ticket_info = self.tickets_treeview.item(selected[0], 'values')
            ticket_id = int(ticket_info[0])
            self.select_admin(lambda: self.assign_ticket_to_admin(ticket_id))
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

    def select_admin(self, callback):
        if not self.admins:
            messagebox.showwarning("Attention", "Aucun administrateur disponible.")
            return

        self.admin_selection_window = tk.Toplevel(self.master)
        self.admin_selection_window.title("Sélectionner un Admin")
        self.admin_selection_window.geometry("300x150")
        self.admin_selection_window.configure(bg='#E8F4E5')

        tk.Label(self.admin_selection_window, text="Sélectionnez un administrateur:", bg='#E8F4E5').pack(pady=10)

        self.selected_admin = StringVar(self.admin_selection_window)
        self.selected_admin.set(f"{self.admins[0]['firstName']} {self.admins[0]['lastName']}")

        admin_names = [f"{admin['firstName']} {admin['lastName']}" for admin in self.admins]
        admin_menu = tk.OptionMenu(self.admin_selection_window, self.selected_admin, *admin_names)
        admin_menu.pack(pady=10)

        tk.Button(self.admin_selection_window, text="Assigner", command=lambda: self.confirm_admin_selection(callback)).pack(pady=10)

    def confirm_admin_selection(self, callback):
        selected_name = self.selected_admin.get()
        for admin in self.admins:
            if f"{admin['firstName']} {admin['lastName']}" == selected_name:
                self.selected_admin_id = admin['id']
                self.admin_selection_window.destroy()
                logging.debug(f"Selected Admin ID: {self.selected_admin_id}")
                callback()  # Call the provided callback function
                return
        messagebox.showwarning("Attention", "Aucun administrateur sélectionné.")
        self.admin_selection_window.destroy()

    def assign_ticket_to_admin(self, ticket_id):
        if self.selected_admin_id:
            update_data = {'admin_id': self.selected_admin_id}
            logging.debug(f"Assigning admin with ID {self.selected_admin_id} to ticket ID {ticket_id}")
            
            self.show_loader("Assignation de l'admin...")  # Affiche le loader dynamique
            response = self.ticket_system.assign_admin_to_ticket(ticket_id, update_data)
            self.hide_loader()  # Masque le loader une fois le processus terminé
            
            logging.debug(f"Assign Admin to Ticket Response: {response}")
            if response and 'id' in response:
                messagebox.showinfo("Succès", "Admin assigné avec succès au ticket!")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de l'assignation de l'admin au ticket.")
        else:
            messagebox.showwarning("Attention", "Aucun administrateur sélectionné.")

    def open_chat_for_selected_ticket(self):
        selected_items = self.tickets_treeview.selection()
        if selected_items:
            item = selected_items[0]
            ticket_info = self.tickets_treeview.item(item, "values")
            if ticket_info:
                try:
                    ticket_id = int(ticket_info[0])
                    current_user_id = self.user_data['id']
                    created_by = int(ticket_info[4]) if ticket_info[4] else None
                    recipient_id = created_by if created_by != current_user_id else None
                    self.open_chat_for_ticket(ticket_id, current_user_id, recipient_id)
                except ValueError:
                    messagebox.showerror("Erreur", "ID invalide. L'ID doit être un entier.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket pour ouvrir le chat.")

    def open_chat_for_ticket(self, ticket_id, author_id, recipient_id):
        try:
            chat_window = tk.Toplevel(self.master)
            chat_view = ChatView(chat_window, author_id, recipient_id, ticket_id)

            self.show_loader("Chargement des messages...")  # Affiche le loader dynamique
            messages = self.ticket_system.get_ticket_messages(ticket_id)
            self.hide_loader()  # Masque le loader une fois le chargement terminé
            
            if 'message' in messages and messages['message'] == "Il n'y a aucun message dans ce ticket.":
                messagebox.showinfo("Information", "Il n'y a aucun message dans ce ticket.")
                logging.info("Il n'y a aucun message dans ce ticket.")
            else:
                chat_view.populate_messages(messages)
        except ValueError:
            self.hide_loader()  # Assurez-vous de masquer le loader en cas d'erreur
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
                    admin_name = self.admin_id_to_name.get(admin_id, '')
                    created_at = self.format_date(ticket.get('createdAt', ''))
                    updated_at = self.format_date(ticket.get('updatedAt', ''))
                    self.tickets_treeview.insert("", tk.END, values=(ticket_id, ticket_type, description, status, created_by, admin_name, created_at, updated_at))
                    logging.debug(f"Inserted ticket into Treeview: ID={ticket_id}, Type={ticket_type}, Description={description}, Status={status}, Created By={created_by}, Admin Name={admin_name}, Created At={created_at}, Updated At={updated_at}")

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
            
            self.show_loader("Création du ticket...")  # Affiche le loader dynamique
            response = self.ticket_system.create_ticket(ticket_data)
            self.hide_loader()  # Masque le loader une fois le processus terminé
            
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
            
            self.show_loader("Fermeture du ticket...")  # Affiche le loader dynamique
            response = self.ticket_system.update_ticket(ticket_id, update_data)
            self.hide_loader()  # Masque le loader une fois le processus terminé
            
            logging.debug(f"Close Ticket Response: {response}")
            if response and 'id' in response:
                messagebox.showinfo("Succès", "Ticket fermé avec succès!")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de la fermeture du ticket.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

    def delete_ticket(self):
        selected = self.tickets_treeview.selection()
        if selected:
            ticket_info = self.tickets_treeview.item(selected[0], 'values')
            ticket_id = int(ticket_info[0])
            status = ticket_info[2]
            if status != 'closed':
                messagebox.showwarning("Attention", "Seuls les tickets fermés peuvent être supprimés.")
                return
            
            self.show_loader("Suppression du ticket...")  # Affiche le loader dynamique
            response = self.ticket_system.delete_ticket(ticket_id)
            self.hide_loader()  # Masque le loader une fois le processus terminé
            
            logging.debug(f"Delete Ticket Response: {response}")
            if response and 'message' in response:
                messagebox.showinfo("Succès", "Ticket supprimé avec succès!")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de la suppression du ticket.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

def open_admin_dashboard(root, user_data):
    root.withdraw()
    dashboard = tk.Toplevel(root)
    AdminView(dashboard, user_data)
