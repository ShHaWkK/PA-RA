import tkinter as tk
from src.api.ticket_api import TicketAPI

class TicketDetails:
    def __init__(self, root, ticket, user_data):
        self.root = root
        self.ticket = ticket
        self.user_data = user_data
        self.root.title(f"Ticket {ticket['id']} Details")

        self.create_widgets()
        self.load_messages()

    def create_widgets(self):
        self.details_frame = tk.Frame(self.root)
        self.details_frame.pack(fill=tk.BOTH, expand=True)

        tk.Label(self.details_frame, text="Type:").grid(row=0, column=0)
        tk.Label(self.details_frame, text=self.ticket['type']).grid(row=0, column=1)

        tk.Label(self.details_frame, text="Description:").grid(row=1, column=0)
        tk.Label(self.details_frame, text=self.ticket['description']).grid(row=1, column=1)

        tk.Label(self.details_frame, text="Status:").grid(row=2, column=0)
        tk.Label(self.details_frame, text=self.ticket['status']).grid(row=2, column=1)

        tk.Label(self.details_frame, text="Created By:").grid(row=3, column=0)
        tk.Label(self.details_frame, text=self.ticket['created_by']).grid(row=3, column=1)

        tk.Label(self.details_frame, text="Assigned To:").grid(row=4, column=0)
        tk.Label(self.details_frame, text=self.ticket['assigned_to'] or "Unassigned").grid(row=4, column=1)

        self.message_list = tk.Listbox(self.root)
        self.message_list.pack(fill=tk.BOTH, expand=True)

        self.message_entry = tk.Entry(self.root)
        self.message_entry.pack(fill=tk.BOTH, expand=True)

        self.buttons_frame = tk.Frame(self.root)
        self.buttons_frame.pack(fill=tk.BOTH, expand=True)

        tk.Button(self.buttons_frame, text="Add Message", command=self.add_message).pack(side=tk.LEFT)
        tk.Button(self.buttons_frame, text="Close Ticket", command=self.close_ticket).pack(side=tk.RIGHT)

    def load_messages(self):
        self.message_list.delete(0, tk.END)
        messages = TicketAPI.get_ticket_messages(self.ticket['id'])
        for message in messages:
            self.message_list.insert(tk.END, f"{message['created_by']} - {message['text']}")

    def add_message(self):
        message_text = self.message_entry.get()
        if not message_text:
            messagebox.showerror("Error", "Message cannot be empty")
            return

        message_data = {
            "ticket_id": self.ticket['id'],
            "text": message_text,
            "created_by": self.user_data['id']
        }

        response = TicketAPI.add_message(self.ticket['id'], message_data)
        if response:
            self.message_entry.delete(0, tk.END)
            self.load_messages()
        else:
            messagebox.showerror("Error", "Failed to add message")

    def close_ticket(self):
        response = TicketAPI.close_ticket(self.ticket['id'], {"closed_by": self.user_data['id']})
        if response:
            self.root.destroy()
        else:
            messagebox.showerror("Error", "Failed to close ticket")

def open_ticket_details(parent, ticket, user_data):
    details = tk.Toplevel(parent)
    TicketDetails(details, ticket, user_data)
