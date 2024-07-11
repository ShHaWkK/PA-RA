import tkinter as tk
from tkinter import ttk, messagebox
from src.api.ticket_api import TicketAPI
from src.views.ticket_details import open_ticket_details

class AdminDashboard:
    def __init__(self, root, user_data):
        self.root = root
        self.user_data = user_data
        self.root.title("Admin Dashboard")
        self.root.geometry("800x600")

        self.create_widgets()
        self.load_tickets()

    def create_widgets(self):
        self.tabs = tk.Frame(self.root)
        self.tabs.pack(pady=10)

        self.open_tickets_btn = ttk.Button(self.tabs, text="Open Tickets", command=lambda: self.load_tickets("open"))
        self.open_tickets_btn.grid(row=0, column=0, padx=5)
        self.closed_tickets_btn = ttk.Button(self.tabs, text="Closed Tickets", command=lambda: self.load_tickets("closed"))
        self.closed_tickets_btn.grid(row=0, column=1, padx=5)
        self.processed_tickets_btn = ttk.Button(self.tabs, text="Processed Tickets", command=lambda: self.load_tickets("processed"))
        self.processed_tickets_btn.grid(row=0, column=2, padx=5)

        self.search_frame = tk.Frame(self.root)
        self.search_frame.pack(pady=10)

        self.search_label = tk.Label(self.search_frame, text="Search:")
        self.search_label.grid(row=0, column=0, padx=5)
        self.search_entry = tk.Entry(self.search_frame)
        self.search_entry.grid(row=0, column=1, padx=5)
        self.search_button = ttk.Button(self.search_frame, text="Search", command=self.search_tickets)
        self.search_button.grid(row=0, column=2, padx=5)

        self.ticket_list = tk.Listbox(self.root, height=20, width=100)
        self.ticket_list.pack(padx=10, pady=10)
        self.ticket_list.bind('<Double-1>', self.open_ticket)

        self.assign_button = ttk.Button(self.root, text="Assign to Me", command=self.assign_ticket)
        self.assign_button.pack(pady=5)

    def load_tickets(self, status=None):
        self.ticket_list.delete(0, tk.END)
        tickets = TicketAPI.get_all_tickets()
        if status:
            tickets = [ticket for ticket in tickets if ticket['status'] == status]
        for ticket in tickets:
            self.ticket_list.insert(tk.END, f"ID: {ticket['id']} - {ticket['description']} - Status: {ticket['status']}")

    def search_tickets(self):
        search_term = self.search_entry.get()
        self.ticket_list.delete(0, tk.END)
        criteria = {
            'keyword': search_term,
        }
        tickets = TicketAPI.search_tickets(criteria)
        for ticket in tickets:
            self.ticket_list.insert(tk.END, f"ID: {ticket['id']} - {ticket['description']} - Status: {ticket['status']}")

    def open_ticket(self, event):
        selected_ticket_index = self.ticket_list.curselection()[0]
        selected_ticket = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
        ticket = TicketAPI.get_ticket(selected_ticket)
        open_ticket_details(self.root, ticket, self.user_data)

    def assign_ticket(self):
        try:
            selected_ticket_index = self.ticket_list.curselection()[0]
            selected_ticket_id = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            data = {"assigned_to": self.user_data["id"]}
            if TicketAPI.update_ticket(selected_ticket_id, data):
                messagebox.showinfo("Success", "Ticket assigned to you successfully!")
                self.load_tickets()
            else:
                messagebox.showerror("Error", "Failed to assign the ticket.")
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to assign.")

def open_admin_dashboard(parent, user_data):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    AdminDashboard(dashboard, user_data)
