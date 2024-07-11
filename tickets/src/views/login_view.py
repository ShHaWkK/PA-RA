import tkinter as tk
from tkinter import messagebox
from views.dashboard_view import open_dashboard
import requests

class LoginApp:
    def __init__(self, root):
        self.root = root
        self.root.title("Login")

        self.api_url = "http://localhost:80/login"  # Change this to your login API endpoint

        self.create_widgets()

    def create_widgets(self):
        tk.Label(self.root, text="Email:").grid(row=0, column=0)
        self.email_entry = tk.Entry(self.root)
        self.email_entry.grid(row=0, column=1)

        tk.Label(self.root, text="Password:").grid(row=1, column=0)
        self.password_entry = tk.Entry(self.root, show="*")
        self.password_entry.grid(row=1, column=1)

        tk.Button(self.root, text="Espace Bénévole", command=lambda: self.login("volunteer")).grid(row=2, column=0)
        tk.Button(self.root, text="Espace Commerçant", command=lambda: self.login("merchant")).grid(row=2, column=1)
        tk.Button(self.root, text="Espace Admin", command=lambda: self.login("admin")).grid(row=2, column=2)

    def login(self, role):
        email = self.email_entry.get()
        password = self.password_entry.get()

        if not email or not password:
            messagebox.showerror("Error", "Please enter both email and password")
            return

        login_data = {
            "email": email,
            "password": password,
            "role": role
        }

        response = requests.post(self.api_url, json=login_data)

        if response.status_code == 200:
            open_dashboard(self.root, role)
        else:
            messagebox.showerror("Error", "Login failed")

def open_login():
    root = tk.Tk()
    app = LoginApp(root)
    root.mainloop()

if __name__ == "__main__":
    open_login()
