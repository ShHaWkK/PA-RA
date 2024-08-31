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
        self.master.configure(bg='#E8F4E5')  # Couleur de fond pour le thème

        self.main_frame = tk.Frame(self.master, bg='#E8F4E5')
        self.main_frame.pack(fill=tk.BOTH, expand=True)

        self.header_frame = tk.Frame(self.main_frame, bg='#3B6E22')
        self.header_frame.pack(fill=tk.X)
        self.header_label = tk.Label(self.header_frame, text="Volunteer Dashboard", font=("Arial", 18), bg='#3B6E22', fg='white')
        self.header_label.pack(pady=10)

        self.tickets_frame = tk.Frame(self.main_frame, bg='#E8F4E5')
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

        self.button_frame = tk.Frame(self.main_frame, bg='#E8F4E5')
        self.button_frame.pack(fill=tk.X, pady=10)

        self.create_ticket_button = tk.Button(self.button_frame, text="Créer un Ticket", command=self.create_ticket, bg='#4CAF50', fg='white')
        self.create_ticket_button.pack(side=tk.LEFT, padx=5)

        self.close_ticket_button = tk.Button(self.button_frame, text="Fermer le Ticket", command=self.close_ticket, bg='#F44336', fg='white')
        self.close_ticket_button.pack(side=tk.LEFT, padx=5)

        self.delete_ticket_button = tk.Button(self.button_frame, text="Supprimer le Ticket", command=self.delete_ticket, bg='#F44336', fg='white')
        self.delete_ticket_button.pack(side=tk.LEFT, padx=5)

        self.open_chat_button = tk.Button(self.button_frame, text="Ouvrir Chat", command=self.open_chat_for_selected_ticket, bg='#2196F3', fg='white')
        self.open_chat_button.pack(side=tk.LEFT, padx=5)

    def show_loader(self, message="Chargement..."):
        """Afficher un message de chargement dynamique."""
        self.loader_window = tk.Toplevel(self.master)
        self.loader_window.title("Veuillez patienter")
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
                    current_user_id = self.user_data['id']
                    admin_id = int(ticket_info[3]) if ticket_info[3] else None
                    recipient_id = admin_id if admin_id != current_user_id else None
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

    def create_ticket(self):
        create_window = tk.Toplevel(self.master)
        create_window.title("Créer un Ticket")
        create_window.geometry("400x400")
        create_window.configure(bg='#E8F4E5')

        tk.Label(create_window, text="Description du Ticket:", bg='#E8F4E5').pack(pady=5)
        description_entry = tk.Text(create_window, height=5, width=40)
        description_entry.pack(pady=5)

        tk.Label(create_window, text="Type de Ticket:", bg='#E8F4E5').pack(pady=5)
        ticket_type_var = tk.StringVar()
        ticket_type_combobox = ttk.Combobox(
            create_window, textvariable=ticket_type_var,
            values=['adhesion', 'collecte', 'stock', 'tournee', 'benevole', 'service'],
            state="readonly"
        )
        ticket_type_combobox.pack(pady=5)

        # Liste pour afficher les pièces jointes ajoutées
        attachments = []
        attachments_listbox = tk.Listbox(create_window, height=5)
        attachments_listbox.pack(pady=5)

        def add_attachment():
            file_path = filedialog.askopenfilename(
                title="Sélectionnez le fichier à joindre",
                filetypes=(("Tous les fichiers", "*.*"), ("Fichiers texte", "*.txt"), ("Images", "*.png;*.jpg;*.jpeg"))
            )
            if file_path:
                try:
                    with open(file_path, "rb") as file:
                        file_content = file.read()
                        encoded_content = base64.b64encode(file_content).decode('utf-8')
                        attachments.append({'filename': os.path.basename(file_path), 'content': encoded_content})
                        attachments_listbox.insert(tk.END, os.path.basename(file_path))  # Ajouter le nom du fichier à la liste
                except Exception as e:
                    logging.error(f"Failed to read attachment file: {e}")
                    messagebox.showerror("Erreur", "Erreur lors de la lecture du fichier joint.")

        add_attachment_button = tk.Button(create_window, text="Ajouter une pièce jointe", command=add_attachment, bg='#4CAF50', fg='white')
        add_attachment_button.pack(pady=10)

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

            self.show_loader("Création du ticket...")  # Afficher le loader
            response = self.ticket_system.create_ticket(ticket_data)
            self.hide_loader()  # Masquer le loader
            logging.debug(f"Create Ticket Response: {response}")

            if response and 'id' in response:
                messagebox.showinfo("Succès", "Ticket créé avec succès ! Vous avez reçu un email de confirmation.")
                self.populate_tickets()
                create_window.destroy()
            else:
                messagebox.showerror("Erreur", "Échec de la création du ticket.")

        submit_button = tk.Button(create_window, text="Créer", command=submit_ticket, bg='#4CAF50', fg='white')
        submit_button.pack(pady=10)

        create_window.grab_set()
        self.master.wait_window(create_window)

    def close_ticket(self):
        selected = self.tickets_treeview.selection()
        if selected:
            ticket_info = self.tickets_treeview.item(selected[0], 'values')
            ticket_id = int(ticket_info[0])
            update_data = {'status': 'closed', 'user_id': self.user_data['id'], 'is_admin': self.user_data.get('role') == 'admin'}

            self.show_loader("Fermeture du ticket...")  # Afficher le loader
            response = self.ticket_system.update_ticket(ticket_id, update_data)
            self.hide_loader()  # Masquer le loader
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

            self.show_loader("Suppression du ticket...")  # Afficher le loader
            response = self.ticket_system.delete_ticket(ticket_id)
            self.hide_loader()  # Masquer le loader
            logging.debug(f"Delete Ticket Response: {response}")

            if response and 'message' in response:
                messagebox.showinfo("Succès", "Ticket supprimé avec succès!")
                self.populate_tickets()
            else:
                messagebox.showerror("Erreur", "Échec de la suppression du ticket.")
        else:
            messagebox.showwarning("Attention", "Veuillez sélectionner un ticket.")

def open_volunteer_dashboard(root, user_data):
    root.withdraw()
    dashboard = tk.Toplevel(root)
    VolunteerView(dashboard, user_data)
