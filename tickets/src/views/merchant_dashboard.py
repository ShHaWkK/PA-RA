# Patch: tickets/src/views/merchant_dashboard.py
import tkinter as tk
from tkinter import ttk, messagebox
from src.api.ticket_api import TicketAPI
from src.views.ticket_details import open_ticket_details

class MerchantDashboard:
    def __init__(self, root, user_data):
        self.root = root
        self.user_data = user_data
        self.root.title("Merchant Dashboard")
        self.root.geometry("800x600")

        self.create_widgets()
        self.load_tickets()

    def create_widgets(self):
        header_frame = tk.Frame(self.root, bg="lightblue", pady=10)
        header_frame.pack(fill=tk.X)

        welcome_label = tk.Label(header_frame, text=f"Welcome, {self.user_data['name']}", bg="lightblue", font=("Helvetica", 16))
        welcome_label.pack(side=tk.LEFT, padx=10)

        logout_button = tk.Button(header_frame, text="Logout", command=self.logout, bg="lightblue", font=("Helvetica", 12))
        logout_button.pack(side=tk.RIGHT, padx=10)

        self.tabs = ttk.Notebook(self.root)
        self.tabs.pack(expand=True, fill=tk.BOTH, padx=10, pady=10)

        self.open_tickets_frame = ttk.Frame(self.tabs)
        self.tabs.add(self.open_tickets_frame, text="Open Tickets")

        self.closed_tickets_frame = ttk.Frame(self.tabs)
        self.tabs.add(self.closed_tickets_frame, text="Closed Tickets")

        self.ticket_list_open = tk.Listbox(self.open_tickets_frame, height=20, width=100, font=("Helvetica", 12))
        self.ticket_list_open.pack(padx=10, pady=10, expand=True, fill=tk.BOTH)
        self.ticket_list_open.bind('<Double-1>', self.open_ticket)

        self.ticket_list_closed = tk.Listbox(self.closed_tickets_frame, height=20, width=100, font=("Helvetica", 12))
        self.ticket_list_closed.pack(padx=10, pady=10, expand=True, fill=tk.BOTH)
        self.ticket_list_closed.bind('<Double-1>', self.open_ticket)

    def load_tickets(self):
        self.ticket_list_open.delete(0, tk.END)
        self.ticket_list_closed.delete(0, tk.END)
        
        tickets = TicketAPI.get_all_tickets()
        open_tickets = [ticket for ticket in tickets if ticket['status'] == 'open']
        closed_tickets = [ticket for ticket in tickets if ticket['status'] == 'closed']
        
        for ticket in open_tickets:
            self.ticket_list_open.insert(tk.END, f"ID: {ticket['id']} - {ticket['description']} - Status: {ticket['status']}")
        
        for ticket in closed_tickets:
            self.ticket_list_closed.insert(tk.END, f"ID: {ticket['id']} - {ticket['description']} - Status: {ticket['status']}")

    def open_ticket(self, event):
        selected_ticket_index = self.ticket_list_open.curselection()[0]
        selected_ticket = self.ticket_list_open.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
        ticket = TicketAPI.get_ticket(selected_ticket)
        open_ticket_details(self.root, ticket, self.user_data)

    def logout(self):
        self.root.destroy()
        from src.views.login_view import open_login
        open_login()

def open_merchant_dashboard(parent, user_data):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    MerchantDashboard(dashboard, user_data)
