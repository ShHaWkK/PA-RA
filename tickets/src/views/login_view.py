import tkinter as tk
from tkinter import ttk, messagebox
from src.api.ticket_api import TicketAPI
from src.views.admin_dashboard import open_admin_dashboard
from src.views.volunteer_dashboard import open_volunteer_dashboard
from src.views.merchant_dashboard import open_merchant_dashboard

class LoginApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Login")
        self.root.geometry("350x200")
        self.root.configure(bg='#e1f5e1')  # Background color

        self.create_widgets()

    def create_widgets(self):
        # Frame for the login form
        form_frame = tk.Frame(self.root, bg='#e1f5e1')
        form_frame.pack(pady=20)

        # Email label and entry
        tk.Label(form_frame, text="Email:", font=('Arial', 12), bg='#e1f5e1', fg='#333').grid(row=0, column=0, padx=10, pady=10)
        self.email_entry = tk.Entry(form_frame, font=('Arial', 12))
        self.email_entry.grid(row=0, column=1, padx=10, pady=10)

        # Password label and entry
        tk.Label(form_frame, text="Password:", font=('Arial', 12), bg='#e1f5e1', fg='#333').grid(row=1, column=0, padx=10, pady=10)
        self.password_entry = tk.Entry(form_frame, show="*", font=('Arial', 12))
        self.password_entry.grid(row=1, column=1, padx=10, pady=10)

        # Login button
        login_button = tk.Button(self.root, text="Login", command=self.login, font=('Arial', 12), bg='#4caf50', fg='white', relief='flat', overrelief='ridge')
        login_button.pack(pady=10)

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
            elif response['role'] == 'volunteer':
                open_volunteer_dashboard(self.root, response)
            elif response['role'] == 'merchant':
                open_merchant_dashboard(self.root, response)
            else:
                messagebox.showerror("Error", "Unknown user role")
        else:
            messagebox.showerror("Error", "Login failed")

def open_login(root):
    app = LoginApp(root)
import tkinter as tk
from tkinter import ttk, messagebox
from src.api.ticket_api import TicketAPI
from src.views.admin_dashboard import open_admin_dashboard
from src.views.volunteer_dashboard import open_volunteer_dashboard
from src.views.merchant_dashboard import open_merchant_dashboard

class LoginApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Login")
        self.root.geometry("350x200")
        self.root.configure(bg='#e1f5e1')  # Background color

        self.create_widgets()

    def create_widgets(self):
        # Frame for the login form
        form_frame = tk.Frame(self.root, bg='#e1f5e1')
        form_frame.pack(pady=20)

        # Email label and entry
        tk.Label(form_frame, text="Email:", font=('Arial', 12), bg='#e1f5e1', fg='#333').grid(row=0, column=0, padx=10, pady=10)
        self.email_entry = tk.Entry(form_frame, font=('Arial', 12))
        self.email_entry.grid(row=0, column=1, padx=10, pady=10)

        # Password label and entry
        tk.Label(form_frame, text="Password:", font=('Arial', 12), bg='#e1f5e1', fg='#333').grid(row=1, column=0, padx=10, pady=10)
        self.password_entry = tk.Entry(form_frame, show="*", font=('Arial', 12))
        self.password_entry.grid(row=1, column=1, padx=10, pady=10)

        # Login button
        login_button = tk.Button(self.root, text="Login", command=self.login, font=('Arial', 12), bg='#4caf50', fg='white', relief='flat', overrelief='ridge')
        login_button.pack(pady=10)

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
            elif response['role'] == 'volunteer':
                open_volunteer_dashboard(self.root, response)
            elif response['role'] == 'merchant':
                open_merchant_dashboard(self.root, response)
            else:
                messagebox.showerror("Error", "Unknown user role")
        else:
            messagebox.showerror("Error", "Login failed")

def open_login(root):
    app = LoginApp(root)
