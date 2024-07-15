# Patch: tickets/src/views/dashboard_view.py

import tkinter as tk
from tkinter import messagebox
from api.ticket_api import TicketAPI

def open_dashboard(parent, role):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    dashboard.title(f"{role.capitalize()} Dashboard")

    tk.Label(dashboard, text=f"Welcome to the {role.capitalize()} Dashboard").pack()

    tk.Button(dashboard, text="Manage Tickets", command=lambda: manage_tickets(dashboard, role)).pack()
    tk.Button(dashboard, text="Logout", command=lambda: logout(dashboard, parent)).pack()

def manage_tickets(parent, role):
    ticket_window = tk.Toplevel(parent)
    ticket_window.title(f"{role.capitalize()} - Manage Tickets")

    tk.Label(ticket_window, text="Type:").grid(row=0, column=0)
    type_entry = tk.Entry(ticket_window)
    type_entry.grid(row=0, column=1)

    tk.Label(ticket_window, text="Description:").grid(row=1, column=0)
    description_entry = tk.Entry(ticket_window)
    description_entry.grid(row=1, column=1)

    tk.Label(ticket_window, text="Status:").grid(row=2, column=0)
    status_entry = tk.Entry(ticket_window)
    status_entry.grid(row=2, column=1)

    tk.Label(ticket_window, text="Created By (User ID):").grid(row=3, column=0)
    created_by_entry = tk.Entry(ticket_window)
    created_by_entry.grid(row=3, column=1)

    tk.Label(ticket_window, text="Assigned To (User ID, optional):").grid(row=4, column=0)
    assigned_to_entry = tk.Entry(ticket_window)
    assigned_to_entry.grid(row=4, column=1)

    def create_ticket():
        ticket_data = {
            "type": type_entry.get(),
            "description": description_entry.get(),
            "status": status_entry.get(),
            "created_by": int(created_by_entry.get()),
        }
        assigned_to = assigned_to_entry.get()
        if assigned_to:
            ticket_data["assigned_to"] = int(assigned_to)

        response = TicketAPI.create_ticket(ticket_data)

        if 'id' in response:
            messagebox.showinfo("Success", "Ticket created successfully")
            response_text.delete(1.0, tk.END)
            response_text.insert(tk.END, response)
        else:
            messagebox.showerror("Error", "Failed to create ticket")
            response_text.delete(1.0, tk.END)
            response_text.insert(tk.END, response)

    tk.Button(ticket_window, text="Create Ticket", command=create_ticket).grid(row=5, column=0, columnspan=2)

    response_text = tk.Text(ticket_window, height=10, width=50)
    response_text.grid(row=6, column=0, columnspan=2)

def logout(current_window, login_window):
    current_window.destroy()
    login_window.deiconify()
