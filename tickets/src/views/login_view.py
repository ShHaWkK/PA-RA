import tkinter as tk
from tkinter import ttk, messagebox, simpledialog
from src.api.ticket_api import TicketAPI
from src.views.admin_dashboard import open_admin_dashboard

class LoginApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Login")
        self.create_widgets()

    def create_widgets(self):
        tk.Label(self.root, text="Email:").grid(row=0, column=0)
        self.email_entry = tk.Entry(self.root)
        self.email_entry.grid(row=0, column=1)

        tk.Label(self.root, text="Password:").grid(row=1, column=0)
        self.password_entry = tk.Entry(self.root, show="*")
        self.password_entry.grid(row=1, column=1)

        tk.Button(self.root, text="Login", command=self.login).grid(row=2, column=0, columnspan=2)

    def login(self):
        email = self.email_entry.get()
        password = self.password_entry.get()
        if not email or not password:
            messagebox.showerror("Error", "Please enter both email and password")
            return

        login_data = {
            "email": email,
            "password": password
        }

        response = TicketAPI.login(login_data)
        if response and 'error' not in response:
            if response['role'] == 'admin':
                open_admin_dashboard(self.root, response)
            else:
                messagebox.showerror("Error", "Unknown user role")
        else:
            messagebox.showerror("Error", "Login failed")

def open_login():
    root = tk.Tk()
    app = LoginApp(root)
    root.mainloop()

if __name__ == "__main__":
    open_login()