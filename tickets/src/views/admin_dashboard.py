import tkinter as tk
from tkinter import messagebox
from src.api.ticket_api import TicketAPI
from src.views.ticket_details import open_ticket_details

class AdminDashboard:
    def __init__(self, root, user_data):
        self.root = root
        self.user_data = user_data
        self.root.title("Admin Dashboard")

        self.create_widgets()
        self.load_tickets()

    def create_widgets(self):
        self.tabs = tk.Frame(self.root)
        self.tabs.pack()

        self.open_tickets_btn = tk.Button(self.tabs, text="Open Tickets", command=lambda: self.load_tickets("open"))
        self.open_tickets_btn.grid(row=0, column=0)
        self.closed_tickets_btn = tk.Button(self.tabs, text="Closed Tickets", command=lambda: self.load_tickets("closed"))
        self.closed_tickets_btn.grid(row=0, column=1)
        self.processed_tickets_btn = tk.Button(self.tabs, text="Processed Tickets", command=lambda: self.load_tickets("processed"))
        self.processed_tickets_btn.grid(row=0, column=2)

        self.ticket_list = tk.Listbox(self.root)
        self.ticket_list.pack(fill=tk.BOTH, expand=True)
        self.ticket_list.bind('<Double-1>', self.open_ticket)

    def load_tickets(self, status=None):
        self.ticket_list.delete(0, tk.END)
        tickets = TicketAPI.get_all_tickets()
        if status:
            tickets = [ticket for ticket in tickets if ticket['status'] == status]
        for ticket in tickets:
            self.ticket_list.insert(tk.END, f"ID: {ticket['id']} - {ticket['description']}")

    def open_ticket(self, event):
        selected_ticket_index = self.ticket_list.curselection()[0]
        selected_ticket = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
        ticket = TicketAPI.get_ticket(selected_ticket)
        open_ticket_details(self.root, ticket, self.user_data)

def open_admin_dashboard(parent, user_data):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    AdminDashboard(dashboard, user_data)
