# Patch: tickets/src/views/admin_dashboard.py
import tkinter as tk
from tkinter import ttk, messagebox, simpledialog
from src.api.ticket_api import TicketAPI
from src.views.ticket_details import open_ticket_details

class AdminDashboard:
    def __init__(self, root, user_data):
        self.root = root
        self.user_data = user_data
        self.root.title("Admin Dashboard")
        self.root.geometry("1000x700")

        self.create_widgets()
        self.load_tickets()

    def create_widgets(self):
        self.header_frame = tk.Frame(self.root, bg="#4CAF50")
        self.header_frame.pack(fill=tk.X)
        self.header_label = tk.Label(self.header_frame, text="Admin Dashboard", font=("Arial", 18), fg="white", bg="#4CAF50")
        self.header_label.pack(pady=10)

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

        self.ticket_list = tk.Listbox(self.root, height=20, width=120)
        self.ticket_list.pack(padx=10, pady=10)
        self.ticket_list.bind('<Double-1>', self.open_ticket)

        self.buttons_frame = tk.Frame(self.root)
        self.buttons_frame.pack(pady=10)

        self.assign_button = ttk.Button(self.buttons_frame, text="Assign to Me", command=self.assign_ticket)
        self.assign_button.grid(row=0, column=0, padx=5)

        self.close_ticket_button = ttk.Button(self.buttons_frame, text="Close Ticket", command=self.close_ticket)
        self.close_ticket_button.grid(row=0, column=1, padx=5)

        self.validate_ticket_button = ttk.Button(self.buttons_frame, text="Validate Ticket", command=self.validate_ticket)
        self.validate_ticket_button.grid(row=0, column=2, padx=5)

        self.assign_admin_button = ttk.Button(self.buttons_frame, text="Assign to Another Admin", command=self.assign_admin)
        self.assign_admin_button.grid(row=0, column=3, padx=5)

        self.show_messages_button = ttk.Button(self.buttons_frame, text="Show Messages", command=self.show_ticket_messages)
        self.show_messages_button.grid(row=0, column=4, padx=5)

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

    def close_ticket(self):
        try:
            selected_ticket_index = self.ticket_list.curselection()[0]
            selected_ticket_id = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            if TicketAPI.update_ticket(selected_ticket_id, {'status': 'closed'}):
                messagebox.showinfo("Success", "Ticket closed successfully!")
                self.load_tickets()
            else:
                messagebox.showerror("Error", "Failed to close the ticket.")
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to close.")

    def validate_ticket(self):
        try:
            selected_ticket_index = self.ticket_list.curselection()[0]
            selected_ticket_id = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            if TicketAPI.update_ticket(selected_ticket_id, {'status': 'validated'}):
                messagebox.showinfo("Success", "Ticket validated successfully!")
                self.load_tickets()
            else:
                messagebox.showerror("Error", "Failed to validate the ticket.")
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to validate.")

    def assign_admin(self):
        try:
            selected_ticket_index = self.ticket_list.curselection()[0]
            selected_ticket_id = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            admin_id = self.select_admin()
            if admin_id:
                if TicketAPI.update_ticket(selected_ticket_id, {'assigned_to': admin_id}):
                    messagebox.showinfo("Success", "Ticket assigned to another admin successfully!")
                    self.load_tickets()
                else:
                    messagebox.showerror("Error", "Failed to assign the ticket.")
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to assign.")

    def select_admin(self):
        admins = self.get_all_admins()

        window = tk.Toplevel(self.root)
        window.title("Select Admin")
        window.geometry("300x150")

        tk.Label(window, text="Choose an admin:").pack(pady=10)

        selected_admin = tk.StringVar()
        admin_menu = ttk.Combobox(window, textvariable=selected_admin, values=admins)
        admin_menu.pack(pady=10)
        admin_menu.current(0)

        def assign():
            window.destroy()

        assign_button = tk.Button(window, text="Assign", command=assign)
        assign_button.pack(pady=10)

        window.wait_window()
        return selected_admin.get().split(":")[0]

    def get_all_admins(self):
        try:
            admins = TicketAPI.get_all_admins()
            return [f"{admin['id']}: {admin['name']}" for admin in admins]
        except Exception as e:
            print(f"Error: {e}")
            return []

    def show_ticket_messages(self):
        try:
            selected_ticket_index = self.ticket_list.curselection()[0]
            selected_ticket_id = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            messages = TicketAPI.get_ticket_messages(selected_ticket_id)
            window = tk.Toplevel(self.root)
            window.title("Messages")
            tk.Label(window, text="Messages:", font=("Arial", 12)).pack(pady=10)
            messages_text = tk.Text(window, height=20, width=80)
            messages_text.pack(padx=10, pady=10)
            for message in messages:
                messages_text.insert(tk.END, f"{message['author']}: {message['content']}\n")
            messages_text.config(state=tk.DISABLED)
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to view messages.")

    def send_message(self):
        try:
            selected_ticket_index = self.ticket_list.curselection()[0]
            selected_ticket_id = self.ticket_list.get(selected_ticket_index).split(" - ")[0].split(": ")[1]
            message = simpledialog.askstring("Send Message", "Enter your message:")
            if message and TicketAPI.add_message(selected_ticket_id, {'content': message, 'author': self.user_data['name']}):
                messagebox.showinfo("Success", "Message sent successfully!")
                self.show_ticket_messages()
            else:
                messagebox.showerror("Error", "Failed to send the message.")
        except IndexError:
            messagebox.showwarning("Warning", "Please select a ticket to send a message.")

def open_admin_dashboard(parent, user_data):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    AdminDashboard(dashboard, user_data)

