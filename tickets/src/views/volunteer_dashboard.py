# Patch: tickets/src/views/volunteer_dashboard.py 
import tkinter as tk
from tkinter import ttk, messagebox
from src.api.ticket_api import TicketAPI
from src.views.ticket_details import open_ticket_details

class VolunteerDashboard:
    def __init__(self, root, user_data):
        self.root = root
        self.user_data = user_data
        self.root.title("Volunteer Dashboard")
        self.root.geometry("800x600")
        
        self.create_widgets()
        self.load_tickets()

    def create_widgets(self):
        self.tabs = ttk.Notebook(self.root)
        self.tabs.pack(expand=True, fill=tk.BOTH)

        self.open_tickets_frame = ttk.Frame(self.tabs)
        self.tabs.add(self.open_tickets_frame, text="Open Tickets")

        self.closed_tickets_frame = ttk.Frame(self.tabs)
        self.tabs.add(self.closed_tickets_frame, text="Closed Tickets")

        self.ticket_list_open = tk.Listbox(self.open_tickets_frame, height=20, width=100)
        self.ticket_list_open.pack(padx=10, pady=10, expand=True, fill=tk.BOTH)
        self.ticket_list_open.bind('<Double-1>', self.open_ticket)

        self.ticket_list_closed = tk.Listbox(self.closed_tickets_frame, height=20, width=100)
        self.ticket_list_closed.pack(padx=10, pady=10, expand=True, fill=tk.BOTH)
        self.ticket_list_closed.bind('<Double-1>', self.open_ticket)

        self.assign_button = ttk.Button(self.root, text="Assign to Me", command=self.assign_ticket)
        self.assign_button.pack(pady=5)

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

    def assign_ticket(self):
        try:
            selected_ticket_index = self.ticket_list_open.curselection()[0]
            selected_ticket_id = self.ticket_list_open.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            data = {"assigned_to": self.user_data["id"]}
            if TicketAPI.update_ticket(selected_ticket_id, data):
                messagebox.showinfo("Success", "Ticket assigned to you successfully!")
                self.load_tickets()
            else:
                messagebox.showerror("Error", "Failed to assign the ticket.")
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to assign.")

def open_volunteer_dashboard(parent, user_data):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    VolunteerDashboard(dashboard, user_data)
