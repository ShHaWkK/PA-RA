import tkinter as tk
from tkinter import messagebox

class VolunteerDashboard:
    def __init__(self, root, user_data):
        self.root = root
        self.user_data = user_data
        self.root.title("Volunteer Dashboard")

        self.create_widgets()

    def create_widgets(self):
        tk.Label(self.root, text=f"Welcome, {self.user_data['name']}").pack()
        tk.Button(self.root, text="Logout", command=self.logout).pack()

    def logout(self):
        self.root.destroy()
        from login_view import open_login
        open_login()

def open_volunteer_dashboard(parent, user_data):
    parent.withdraw()
    dashboard = tk.Toplevel(parent)
    VolunteerDashboard(dashboard, user_data)
