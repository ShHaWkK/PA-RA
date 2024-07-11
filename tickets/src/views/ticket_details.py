import tkinter as tk
from tkinter import ttk, messagebox
from src.api.ticket_api import TicketAPI

class TicketDetails:
    def __init__(self, root, ticket, user_data):
        self.root = root
        self.ticket = ticket
        self.user_data = user_data
        self.root.title("Ticket Details")
        self.create_widgets()

    def create_widgets(self):
        self.details_frame = tk.Frame(self.root)
        self.details_frame.pack(pady=10, padx=10)

        tk.Label(self.details_frame, text="Ticket ID:").grid(row=0, column=0, sticky="e")
        tk.Label(self.details_frame, text=self.ticket.get('id', 'N/A')).grid(row=0, column=1, sticky="w")

        tk.Label(self.details_frame, text="Type:").grid(row=1, column=0, sticky="e")
        tk.Label(self.details_frame, text=self.ticket.get('type', 'N/A')).grid(row=1, column=1, sticky="w")

        tk.Label(self.details_frame, text="Description:").grid(row=2, column=0, sticky="e")
        tk.Label(self.details_frame, text=self.ticket.get('description', 'N/A')).grid(row=2, column=1, sticky="w")

        tk.Label(self.details_frame, text="Created By:").grid(row=3, column=0, sticky="e")
        created_by = self.ticket.get('created_by', 'N/A')
        if isinstance(created_by, dict):
            created_by = created_by.get('name', 'N/A')
        tk.Label(self.details_frame, text=created_by).grid(row=3, column=1, sticky="w")

        tk.Label(self.details_frame, text="Assigned To:").grid(row=4, column=0, sticky="e")
        assigned_to = self.ticket.get('assigned_to', 'N/A')
        if isinstance(assigned_to, dict):
            assigned_to = assigned_to.get('name', 'N/A')
        tk.Label(self.details_frame, text=assigned_to).grid(row=4, column=1, sticky="w")

        tk.Label(self.details_frame, text="Status:").grid(row=5, column=0, sticky="e")
        tk.Label(self.details_frame, text=self.ticket.get('status', 'N/A')).grid(row=5, column=1, sticky="w")

        tk.Label(self.details_frame, text="Attachments:").grid(row=6, column=0, sticky="e")
        attachments = self.ticket.get('attachments', [])
        for idx, attachment in enumerate(attachments):
            tk.Label(self.details_frame, text=attachment).grid(row=6+idx, column=1, sticky="w")

        tk.Label(self.details_frame, text="Messages:").grid(row=7, column=0, sticky="e")
        messages = self.ticket.get('messages', [])
        for idx, message in enumerate(messages):
            tk.Label(self.details_frame, text=f"{message['author']}: {message['content']}").grid(row=7+idx, column=1, sticky="w")

        self.message_entry = tk.Entry(self.details_frame)
        self.message_entry.grid(row=7+len(messages), column=1, sticky="w")

        self.add_message_btn = tk.Button(self.details_frame, text="Add Message", command=self.add_message)
        self.add_message_btn.grid(row=7+len(messages), column=2, sticky="w")

        self.close_ticket_btn = tk.Button(self.details_frame, text="Close Ticket", command=self.close_ticket)
        self.close_ticket_btn.grid(row=8+len(messages), column=2, sticky="w")

    def add_message(self):
        message_content = self.message_entry.get()
        if message_content:
            message_data = {
                'content': message_content,
                'author': self.user_data.get('name', 'Unknown')
            }
            if TicketAPI.add_message(self.ticket['id'], message_data):
                messagebox.showinfo("Success", "Message added successfully!")
                self.message_entry.delete(0, tk.END)
                self.root.destroy()
            else:
                messagebox.showerror("Error", "Failed to add message.")

    def close_ticket(self):
        if TicketAPI.close_ticket(self.ticket['id'], {'closed_by': self.user_data.get('id')}):
            messagebox.showinfo("Success", "Ticket closed successfully!")
            self.root.destroy()
        else:
            messagebox.showerror("Error", "Failed to close ticket.")

def open_ticket_details(parent, ticket, user_data):
    details = tk.Toplevel(parent)
    TicketDetails(details, ticket, user_data)
